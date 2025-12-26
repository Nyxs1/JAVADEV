<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdateAccountRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Services\Profile\ProfileService;
use App\Models\User;
use App\Models\RoleRequest;
use App\Support\FlashMessage;
use App\Support\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use JsonResponses;

    public function __construct(
        private ProfileService $profileService
    ) {
    }

    public function show($username)
    {
        $user = User::with(['role', 'skills'])->where('username', $username)->firstOrFail();
        $isOwnProfile = Auth::check() && Auth::user()->id === $user->id;

        // Public profile data (narrative, not detailed)
        $portfolioActivities = [];
        $courseActivities = [];

        // Event summary (narrative only, not detailed list)
        $eventSummary = $this->getEventSummary($user);

        // Certificates (achievements from courses and events)
        $certificates = $this->getCertificates($user);

        // Discussion summary (stats, not full logs)
        $discussionSummary = $this->getDiscussionSummary($user);

        return view('pages.profile.index', compact(
            'user',
            'isOwnProfile',
            'portfolioActivities',
            'courseActivities',
            'eventSummary',
            'certificates',
            'discussionSummary'
        ));
    }

    /**
     * Get event participation summary for public profile (narrative).
     */
    private function getEventSummary(User $user): array
    {
        $currentYear = now()->year;

        $totalEvents = $user->participatedEvents()
            ->wherePivot('registration_status', 'registered')
            ->count();

        $eventsThisYear = $user->participatedEvents()
            ->wherePivot('registration_status', 'registered')
            ->whereYear('start_at', $currentYear)
            ->count();

        return [
            'total' => $totalEvents,
            'year' => $eventsThisYear > 0 ? $currentYear : null,
        ];
    }

    /**
     * Get certificates (achievements from courses and events).
     */
    private function getCertificates(User $user): array
    {
        $certificates = [];

        // Get certificates from completed events
        $eventCertificates = $user->participatedEvents()
            ->wherePivot('completion_status', 'completed')
            ->whereNotNull('event_participants.certificate_url')
            ->get();

        foreach ($eventCertificates as $event) {
            $certificates[] = [
                'title' => $event->title,
                'year' => $event->start_at->format('Y'),
                'source' => 'Event',
            ];
        }

        // Future: Add course certificates when course completion is implemented
        // $courseCertificates = $user->completedCourses()->get();

        // Sort by year descending
        usort($certificates, fn($a, $b) => $b['year'] <=> $a['year']);

        return $certificates;
    }

    /**
     * Get discussion summary (stats, not full logs).
     */
    private function getDiscussionSummary(User $user): array
    {
        // Placeholder - implement when discussion models are available
        $totalThreads = 0;
        $totalReplies = 0;
        $recentParticipation = [];

        // Future implementation:
        // $totalThreads = $user->discussionThreads()->count();
        // $totalReplies = $user->discussionReplies()->count();
        // $recentParticipation = $user->discussionActivities()
        //     ->latest()
        //     ->limit(5)
        //     ->get()
        //     ->map(fn($item) => [
        //         'title' => $item->thread->title,
        //         'type' => $item->type,
        //         'date' => $item->created_at->diffForHumans(),
        //     ])->toArray();

        return [
            'totalThreads' => $totalThreads,
            'totalReplies' => $totalReplies,
            'recentParticipation' => $recentParticipation,
        ];
    }

    /**
     * Get member event participation history.
     */
    private function getMemberEventHistory(User $user): array
    {
        $participations = $user->participatedEvents()
            ->wherePivot('registration_status', 'registered')
            ->orderBy('start_at', 'desc')
            ->limit(10)
            ->get();

        return $participations->map(function ($event) {
            $pivot = $event->pivot;

            // Derive status
            $status = 'registered';
            if ($pivot->completion_status === 'completed') {
                $status = 'completed';
            } elseif ($pivot->attendance_status === 'present') {
                $status = 'attended';
            } elseif ($pivot->attendance_status === 'absent') {
                $status = 'absent';
            }

            return [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'date' => $event->start_at->format('d M Y'),
                'cover_image' => $event->cover_image,
                'status' => $status,
                'certificate_url' => $pivot->completion_status === 'completed' ? $pivot->certificate_url : null,
            ];
        })->toArray();
    }

    /**
     * Get mentor event history.
     */
    private function getMentorEventHistory(User $user): array
    {
        $mentorships = $user->mentoringEvents()
            ->orderBy('start_at', 'desc')
            ->limit(10)
            ->get();

        return $mentorships->map(function ($event) {
            $pivot = $event->pivot;

            return [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'date' => $event->start_at->format('d M Y'),
                'cover_image' => $event->cover_image,
                'role' => $pivot->role,
                'role_label' => match ($pivot->role) {
                    'mentor' => 'Mentor',
                    'co-mentor' => 'Co-Mentor',
                    'speaker' => 'Speaker',
                    'moderator' => 'Moderator',
                    default => ucfirst($pivot->role),
                },
                'goal_status' => $pivot->goal_status,
                'achieved_participants' => $pivot->achieved_participants ?? 0,
            ];
        })->toArray();
    }

    public function index()
    {
        return redirect()->route('profile.show', Auth::user()->username);
    }

    public function settings()
    {
        $user = Auth::user();
        $user->load('skills');

        return view('pages.profile.settings', ['user' => $user]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $isAjax = $request->ajax() || $request->wantsJson();
        $data = $request->validated();

        $result = $this->profileService->updateProfile(
            $user,
            $data,
            $request->shouldRemoveAvatar(),
            $request->hasCroppedAvatar() ? $data['cropped_avatar'] : null
        );

        if (!$result['success']) {
            if ($isAjax) {
                return $this->jsonValidationError($result['message']);
            }
            return back()->withErrors(['cropped_avatar' => $result['message']]);
        }

        if ($isAjax) {
            return $this->jsonSuccess('Profile updated successfully!', [
                'avatar_url' => $result['avatar_url'],
                'avatar_version' => $result['avatar_version'],
                'avatar_changed' => $result['avatar_changed'],
            ]);
        }

        return redirect()->route('profile.show', $user->username)
            ->with(FlashMessage::SUCCESS, 'Profile updated successfully!');
    }

    public function updateAccount(UpdateAccountRequest $request)
    {
        $user = Auth::user();
        $user->username = $request->validated()['username'];
        $user->save();

        return redirect()->route('profile.settings')
            ->with(FlashMessage::SUCCESS, 'Username saved successfully!');
    }

    public function checkUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:30|regex:/^[a-zA-Z0-9_]+$/',
        ]);

        $username = $request->input('username');
        $currentUser = Auth::user();

        if ($currentUser && $currentUser->username === $username) {
            return $this->jsonSuccess('This is your current username.', ['available' => true]);
        }

        $exists = User::where('username', $username)->exists();

        return $exists
            ? $this->jsonError('Username is already taken.', ['available' => false])
            : $this->jsonSuccess('Username is available!', ['available' => true]);
    }

    /**
     * Check username availability (GET endpoint for Instagram-style flow)
     */
    public function checkUsernameAvailability(Request $request)
    {
        $username = strtolower(trim($request->input('username', '')));

        // Validation
        if (strlen($username) < 3) {
            return response()->json(['available' => false, 'reason' => 'Username must be at least 3 characters']);
        }

        if (strlen($username) > 20) {
            return response()->json(['available' => false, 'reason' => 'Username must be 20 characters or less']);
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return response()->json(['available' => false, 'reason' => 'Only letters, numbers, and underscores allowed']);
        }

        $currentUser = Auth::user();

        // Same as current
        if ($currentUser && $currentUser->username === $username) {
            return response()->json(['available' => true, 'same' => true]);
        }

        // Check if taken
        $exists = User::where('username', $username)->exists();

        if ($exists) {
            return response()->json(['available' => false, 'reason' => 'Username is already taken']);
        }

        return response()->json(['available' => true]);
    }

    /**
     * Update username (POST endpoint for Instagram-style flow)
     */
    public function updateUsername(Request $request)
    {
        $username = strtolower(trim($request->input('username', '')));
        $currentUser = Auth::user();

        // Validation
        if (strlen($username) < 3 || strlen($username) > 20) {
            return response()->json(['success' => false, 'message' => 'Username must be 3-20 characters']);
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return response()->json(['success' => false, 'message' => 'Invalid username format']);
        }

        // Check if same
        if ($currentUser->username === $username) {
            return response()->json(['success' => false, 'message' => 'This is already your username']);
        }

        // Check if taken
        if (User::where('username', $username)->where('id', '!=', $currentUser->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Username is already taken']);
        }

        // Update
        $currentUser->username = $username;
        $currentUser->save();

        return response()->json(['success' => true, 'message' => 'Username updated successfully']);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $result = $this->profileService->changePassword(
            Auth::user(),
            $data['current_password'],
            $data['new_password']
        );

        if (!$result['success']) {
            return back()->withErrors(['current_password' => $result['message']]);
        }

        return redirect()->route('profile.settings')
            ->with(FlashMessage::SUCCESS, $result['message']);
    }

    public function requestRoleChange(Request $request)
    {
        $user = Auth::user();

        if ($user->hasPendingRoleRequest()) {
            return redirect()->route('profile.index')
                ->with(FlashMessage::ERROR, 'You already have a pending role request.');
        }

        $validated = $request->validate([
            'to_role_id' => [
                'required',
                'exists:roles,id',
                Rule::notIn([$user->role_id]),
            ],
            'reason' => 'nullable|string|max:500',
        ]);

        RoleRequest::create([
            'user_id' => $user->id,
            'from_role_id' => $user->role_id,
            'to_role_id' => $validated['to_role_id'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('profile.index')
            ->with(FlashMessage::SUCCESS, 'Role change request submitted successfully!');
    }

    public function updatePrivacy(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'activity_type' => 'required|string|in:events,mentoring,portfolio,course,discussion,challenge',
            'is_public' => 'required|boolean',
        ]);

        $user->setActivityPrivacy($validated['activity_type'], $validated['is_public']);

        $tabName = ucfirst($validated['activity_type']);
        $status = $validated['is_public'] ? 'public' : 'private';

        return $this->jsonSuccess("{$tabName} is now {$status}.", [
            'activity_type' => $validated['activity_type'],
            'is_public' => $validated['is_public'],
        ]);
    }

    public function updateVisibility(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'is_profile_public' => 'required|boolean',
        ]);

        $user->is_profile_public = $validated['is_profile_public'];
        $user->save();

        $message = $validated['is_profile_public']
            ? 'Profile content is now public.'
            : 'Profile content is now private.';

        return $this->jsonSuccess($message, [
            'is_profile_public' => $user->is_profile_public,
        ]);
    }

    public function storeSkill(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'tech_name' => 'required|string|max:100',
            'level' => 'required|integer|min:1|max:4',
        ]);

        $techSlug = \Illuminate\Support\Str::slug($validated['tech_name']);

        if ($user->skills()->where('tech_slug', $techSlug)->exists()) {
            return back()->with(FlashMessage::ERROR, 'This skill already exists.');
        }

        $user->skills()->create([
            'tech_slug' => $techSlug,
            'tech_name' => $validated['tech_name'],
            'level' => $validated['level'],
        ]);

        return back()->with(FlashMessage::SUCCESS, 'Skill added successfully.');
    }

    public function updateSkill(Request $request, int $skillId)
    {
        $user = Auth::user();
        $skill = $user->skills()->findOrFail($skillId);

        $validated = $request->validate([
            'level' => 'required|integer|min:1|max:4',
        ]);

        $skill->update(['level' => $validated['level']]);

        return back()->with(FlashMessage::SUCCESS, 'Skill updated successfully.');
    }

    public function destroySkill(int $skillId)
    {
        $user = Auth::user();
        $skill = $user->skills()->findOrFail($skillId);
        $skill->delete();

        return back()->with(FlashMessage::SUCCESS, 'Skill removed successfully.');
    }

    public function updateAvatarFocus(Request $request)
    {
        $validated = $request->validate([
            'x' => 'required|numeric|min:0|max:1',
            'y' => 'required|numeric|min:0|max:1',
            'zoom' => 'required|numeric|min:1|max:3',
        ]);

        $user = Auth::user();
        $user->avatar_focus = [
            'x' => floatval($validated['x']),
            'y' => floatval($validated['y']),
            'zoom' => floatval($validated['zoom']),
        ];
        $user->save();

        return $this->jsonSuccess('Avatar position saved!', [
            'avatar_focus' => $user->avatar_focus,
        ]);
    }
}
