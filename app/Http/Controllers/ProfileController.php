<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdateAccountRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\StoreSkillRequest;
use App\Http\Requests\Profile\UpdateSkillRequest;
use App\Services\Profile\ProfileService;
use App\Services\Profile\UsernameService;
use App\Services\Profile\SkillService;
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
        private ProfileService $profileService,
        private UsernameService $usernameService,
        private SkillService $skillService
    ) {
    }

    // =========================================================================
    // PUBLIC PROFILE
    // =========================================================================

    public function show($username)
    {
        $user = User::with(['role', 'skills'])->where('username', $username)->firstOrFail();
        $isOwnProfile = Auth::check() && Auth::id() === $user->id;

        // Load published items for public profile
        // For own profile, show all items; for others, only published
        $portfolios = $isOwnProfile
            ? $user->portfolios()->get()
            : $user->publishedPortfolios()->get();

        $courses = $isOwnProfile
            ? $user->userCourses()->get()
            : $user->publishedCourses()->get();

        return view('pages.profile.index', [
            'user' => $user,
            'isOwnProfile' => $isOwnProfile,
            'portfolioActivities' => $portfolios,
            'courseActivities' => $courses,
            'eventSummary' => $this->getEventSummary($user),
            'certificates' => $this->getCertificates($user),
            'discussionSummary' => $this->getDiscussionSummary($user),
        ]);
    }

    public function index()
    {
        return redirect()->route('profile.show', Auth::user()->username);
    }

    // =========================================================================
    // SETTINGS
    // =========================================================================

    public function settings()
    {
        $user = Auth::user();
        $user->load('skills');

        return view('pages.profile.settings', ['user' => $user]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $result = $this->profileService->updateProfile(
            $user,
            $data,
            $request->shouldRemoveAvatar(),
            $request->hasCroppedAvatar() ? $data['cropped_avatar'] : null
        );

        if (!$result['success']) {
            return $this->respondWithError($request, $result['message']);
        }

        return $this->respondWithSuccess(
            $request,
            'Profile updated successfully!',
            route('profile.show', $user->username),
            [
                'avatar_url' => $result['avatar_url'],
                'avatar_version' => $result['avatar_version'],
                'avatar_changed' => $result['avatar_changed'],
            ]
        );
    }

    // =========================================================================
    // USERNAME
    // =========================================================================

    public function checkUsernameAvailability(Request $request)
    {
        $username = $request->input('username', '');
        $result = $this->usernameService->checkAvailability($username, Auth::user());

        return response()->json($result);
    }

    public function updateUsername(Request $request)
    {
        $username = $request->input('username', '');
        $result = $this->usernameService->update(Auth::user(), $username);

        return response()->json($result);
    }

    public function updateAccount(UpdateAccountRequest $request)
    {
        $user = Auth::user();
        $user->username = $request->validated()['username'];
        $user->save();

        return redirect()->route('profile.settings')
            ->with(FlashMessage::SUCCESS, 'Username saved successfully!');
    }

    /**
     * @deprecated Use checkUsernameAvailability instead
     */
    public function checkUsername(Request $request)
    {
        return $this->checkUsernameAvailability($request);
    }

    // =========================================================================
    // PASSWORD
    // =========================================================================

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

    // =========================================================================
    // SKILLS
    // =========================================================================

    public function storeSkill(StoreSkillRequest $request)
    {
        $data = $request->validated();
        $result = $this->skillService->addSkill(Auth::user(), $data['tech_name'], $data['level']);

        $flashType = $result['success'] ? FlashMessage::SUCCESS : FlashMessage::ERROR;
        return back()->with($flashType, $result['message']);
    }

    public function updateSkill(UpdateSkillRequest $request, int $skillId)
    {
        $data = $request->validated();
        $result = $this->skillService->updateLevel(Auth::user(), $skillId, $data['level']);

        $flashType = $result['success'] ? FlashMessage::SUCCESS : FlashMessage::ERROR;
        return back()->with($flashType, $result['message']);
    }

    public function destroySkill(int $skillId)
    {
        $result = $this->skillService->remove(Auth::user(), $skillId);

        $flashType = $result['success'] ? FlashMessage::SUCCESS : FlashMessage::ERROR;
        return back()->with($flashType, $result['message']);
    }

    // =========================================================================
    // PRIVACY & VISIBILITY
    // =========================================================================

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

    // =========================================================================
    // ROLE CHANGE
    // =========================================================================

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

    // =========================================================================
    // AVATAR FOCUS
    // =========================================================================

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

    // =========================================================================
    // PRIVATE HELPERS (Profile Data)
    // =========================================================================

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

    private function getCertificates(User $user): array
    {
        $eventCertificates = $user->participatedEvents()
            ->wherePivot('completion_status', 'completed')
            ->whereNotNull('event_participants.certificate_url')
            ->get();

        $certificates = $eventCertificates->map(fn($event) => [
            'title' => $event->title,
            'year' => $event->start_at->format('Y'),
            'source' => 'Event',
        ])->toArray();

        usort($certificates, fn($a, $b) => $b['year'] <=> $a['year']);

        return $certificates;
    }

    private function getDiscussionSummary(User $user): array
    {
        // Placeholder for future discussion feature
        return [
            'totalThreads' => 0,
            'totalReplies' => 0,
            'recentParticipation' => [],
        ];
    }

    // =========================================================================
    // RESPONSE HELPERS
    // =========================================================================

    private function respondWithError(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->jsonValidationError($message);
        }
        return back()->withErrors(['cropped_avatar' => $message]);
    }

    private function respondWithSuccess(Request $request, string $message, string $redirectTo, array $data = [])
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->jsonSuccess($message, $data);
        }
        return redirect($redirectTo)->with(FlashMessage::SUCCESS, $message);
    }
}
