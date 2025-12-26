<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMentor;
use App\Models\EventRequirement;
use App\Models\User;
use App\Support\FlashMessage;
use App\Support\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsersDashboardController extends Controller
{
    use JsonResponses;

    public function index(Request $request, string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        // Verify the authenticated user is viewing their own dashboard
        if (Auth::id() !== $user->id) {
            abort(403, 'You can only access your own dashboard.');
        }

        $tab = $request->get('tab', 'overview');

        $data = [
            'user' => $user,
            'tab' => $tab,
            'isMentor' => $user->isMentor() || $user->isAdmin(),
            'isAdmin' => $user->isAdmin(),
        ];

        // Load tab-specific data
        match ($tab) {
            'events' => $this->loadEventsData($data, $request),
            'portfolio' => $this->loadPortfolioData($data),
            'courses' => $this->loadCoursesData($data),
            'discussions' => $this->loadDiscussionsData($data),
            'mentor' => $this->loadMentorData($data, $request),
            'admin' => $this->loadAdminData($data, $request),
            default => $this->loadOverviewData($data),
        };

        return view('pages.users.dashboard', $data);
    }

    private function loadOverviewData(array &$data): void
    {
        $user = $data['user'];

        // Member event stats
        $data['eventStats'] = [
            'registered' => $user->participatedEvents()->wherePivot('registration_status', 'registered')->count(),
            'attended' => $user->participatedEvents()->wherePivot('attendance_status', 'present')->count(),
            'completed' => $user->participatedEvents()->wherePivot('completion_status', 'completed')->count(),
        ];

        // Upcoming events only (for overview)
        $data['upcomingEvents'] = $user->participatedEvents()
            ->wherePivot('registration_status', 'registered')
            ->upcoming()
            ->orderBy('start_at', 'asc')
            ->limit(3)
            ->get();

        // Mentor stats (if mentor)
        if ($data['isMentor']) {
            $data['mentorStats'] = [
                'events' => Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id))->count(),
                'upcoming' => Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id))->upcoming()->count(),
            ];
        }
    }

    private function loadEventsData(array &$data, Request $request): void
    {
        $user = $data['user'];
        $filter = $request->get('filter', 'upcoming');

        // Event stats
        $data['eventStats'] = [
            'registered' => $user->participatedEvents()->wherePivot('registration_status', 'registered')->count(),
            'attended' => $user->participatedEvents()->wherePivot('attendance_status', 'present')->count(),
            'completed' => $user->participatedEvents()->wherePivot('completion_status', 'completed')->count(),
        ];

        // Event counts by category
        $data['eventCounts'] = [
            'upcoming' => $user->participatedEvents()->wherePivot('registration_status', 'registered')->upcoming()->count(),
            'ongoing' => $user->participatedEvents()->wherePivot('registration_status', 'registered')->ongoing()->count(),
            'past' => $user->participatedEvents()->wherePivot('registration_status', 'registered')->ended()->count(),
        ];

        // Filtered event list
        $query = $user->participatedEvents()->wherePivot('registration_status', 'registered');

        match ($filter) {
            'ongoing' => $query->ongoing()->orderBy('start_at', 'asc'),
            'past' => $query->ended()->orderBy('start_at', 'desc'),
            default => $query->upcoming()->orderBy('start_at', 'asc'),
        };

        $data['userEvents'] = $query->paginate(10);
        $data['eventFilter'] = $filter;
    }

    private function loadPortfolioData(array &$data): void
    {
        // Placeholder for future portfolio feature
        $data['portfolioItems'] = collect();
    }

    private function loadCoursesData(array &$data): void
    {
        // Placeholder for future courses feature
        $data['enrolledCourses'] = collect();
    }

    private function loadDiscussionsData(array &$data): void
    {
        // Placeholder for future discussions feature
        $data['userDiscussions'] = collect();
    }

    private function loadMentorData(array &$data, Request $request): void
    {
        $user = $data['user'];
        $status = $request->get('status');
        $eventSlug = $request->get('event');

        // If viewing specific event
        if ($eventSlug) {
            $event = Event::where('slug', $eventSlug)->firstOrFail();
            $mentorRecord = $event->mentors()->where('user_id', $user->id)->first();

            if (!$mentorRecord && !$user->isAdmin()) {
                abort(403, 'You are not assigned to this event.');
            }

            $this->loadMentorEventDetail($data, $event, $mentorRecord, $request);
            return;
        }

        // List mentor events
        $query = Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id))
            ->with(['mentors' => fn($q) => $q->where('user_id', $user->id)])
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'participants as present_count' => fn($q) => $q->where('attendance_status', 'present'),
                'participants as completed_count' => fn($q) => $q->where('completion_status', 'completed'),
            ]);

        if ($status === 'upcoming') {
            $query->upcoming();
        } elseif ($status === 'ongoing') {
            $query->ongoing();
        } elseif ($status === 'ended') {
            $query->ended();
        }

        $data['mentorEvents'] = $query->orderBy('start_at', 'desc')->paginate(10);
        $data['mentorStatus'] = $status;
        $data['mentorCounts'] = [
            'all' => Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id))->count(),
            'upcoming' => Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id))->upcoming()->count(),
            'ongoing' => Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id))->ongoing()->count(),
            'ended' => Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id))->ended()->count(),
        ];
    }

    private function loadMentorEventDetail(array &$data, Event $event, ?EventMentor $mentorRecord, Request $request): void
    {
        $subtab = $request->get('subtab', 'participants');

        $event->load(['mentors.user']);

        $participants = $event->participants()
            ->with('user')
            ->orderBy('joined_at', 'desc')
            ->get();

        $requirements = $event->requirementItems()->get();

        $reviews = collect();
        $avgRating = null;
        if ($event->isEnded()) {
            $reviews = $event->feedback()->global()->with('fromUser')->latest()->get();
            $avgRating = $event->feedback()->global()->avg('rating');
        }

        $data['mentorEvent'] = $event;
        $data['mentorRecord'] = $mentorRecord;
        $data['mentorSubtab'] = $subtab;
        $data['participants'] = $participants;
        $data['infoRequirements'] = $requirements->where('type', 'info')->values();
        $data['checklistRequirements'] = $requirements->where('type', 'checklist')->values();
        $data['techRequirements'] = $requirements->where('type', 'tech')->groupBy('category');
        $data['reviews'] = $reviews;
        $data['avgRating'] = $avgRating;
        $data['participantCounts'] = [
            'registered' => $participants->where('registration_status', 'registered')->count(),
            'present' => $participants->where('attendance_status', 'present')->count(),
            'absent' => $participants->where('attendance_status', 'absent')->count(),
            'completed' => $participants->where('completion_status', 'completed')->count(),
        ];
        $data['requirementsLocked'] = $event->isRequirementsLocked() && !$data['user']->isAdmin();
    }

    private function loadAdminData(array &$data, Request $request): void
    {
        $section = $request->get('section', 'events');
        $eventSlug = $request->get('event');

        $data['adminSection'] = $section;

        if ($section === 'finalization') {
            $this->loadFinalizationData($data);
            return;
        }

        // If viewing/editing specific event
        if ($eventSlug) {
            $event = Event::where('slug', $eventSlug)->firstOrFail();
            $this->loadAdminEventDetail($data, $event, $request);
            return;
        }

        // List all events
        $query = Event::query()
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'mentors as mentors_count',
            ]);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            match ($status) {
                'upcoming' => $query->upcoming(),
                'ongoing' => $query->ongoing(),
                'ended' => $query->ended(),
                'draft' => $query->where('status', 'draft'),
                'cancelled' => $query->where('status', 'cancelled'),
                default => null,
            };
        }

        $data['adminEvents'] = $query->orderBy('start_at', 'desc')->paginate(15);
        $data['adminCounts'] = [
            'all' => Event::count(),
            'draft' => Event::where('status', 'draft')->count(),
            'upcoming' => Event::upcoming()->count(),
            'ongoing' => Event::ongoing()->count(),
            'ended' => Event::ended()->count(),
        ];
    }

    private function loadAdminEventDetail(array &$data, Event $event, Request $request): void
    {
        $subtab = $request->get('subtab', 'edit');

        $data['adminEvent'] = $event;
        $data['adminSubtab'] = $subtab;

        match ($subtab) {
            'mentors' => $this->loadEventMentorsData($data, $event),
            'requirements' => $this->loadEventRequirementsData($data, $event),
            'reviews' => $this->loadEventReviewsData($data, $event),
            default => null,
        };
    }

    private function loadEventMentorsData(array &$data, Event $event): void
    {
        $event->load(['mentors.user']);

        $data['eventMentors'] = $event->mentors;
        $data['availableMentors'] = User::whereHas('role', fn($q) => $q->whereIn('name', ['mentor', 'admin']))
            ->whereNotIn('id', $event->mentors->pluck('user_id'))
            ->orderBy('name')
            ->get();
    }

    private function loadEventRequirementsData(array &$data, Event $event): void
    {
        $requirements = $event->requirementItems()->get();

        $data['infoRequirements'] = $requirements->where('type', 'info')->values();
        $data['checklistRequirements'] = $requirements->where('type', 'checklist')->values();
        $data['techRequirements'] = $requirements->where('type', 'tech')->groupBy('category');
    }

    private function loadEventReviewsData(array &$data, Event $event): void
    {
        $data['eventReviews'] = $event->feedback()->global()->with('fromUser')->latest()->paginate(20);
        $data['avgRating'] = $event->feedback()->global()->avg('rating');
        $data['reviewCount'] = $event->feedback()->global()->count();

        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = $event->feedback()->global()->where('rating', $i)->count();
        }
        $data['ratingDistribution'] = $ratingDistribution;
    }

    private function loadFinalizationData(array &$data): void
    {
        $data['pendingEvents'] = Event::ended()
            ->whereNull('finalized_at')
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'participants as present_count' => fn($q) => $q->where('attendance_status', 'present'),
                'participants as completed_count' => fn($q) => $q->where('completion_status', 'completed'),
            ])
            ->orderBy('end_at', 'desc')
            ->get();

        $data['finalizedEvents'] = Event::whereNotNull('finalized_at')
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'participants as completed_count' => fn($q) => $q->where('completion_status', 'completed'),
            ])
            ->orderBy('finalized_at', 'desc')
            ->limit(10)
            ->get();
    }

    // ========== ACTION METHODS ==========

    // Mentor actions
    public function storeRequirement(Request $request, Event $event)
    {
        $user = Auth::user();

        if (!$event->mentors()->where('user_id', $user->id)->exists() && !$user->isAdmin()) {
            abort(403);
        }

        if ($event->isRequirementsLocked() && !$user->isAdmin()) {
            return back()->with(FlashMessage::ERROR, 'Requirements are locked after event starts.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:info,checklist,tech',
            'category' => 'nullable|string|in:tools,language,framework,database,other',
        ]);

        $maxOrder = $event->requirementItems()->max('order') ?? 0;

        EventRequirement::create([
            'event_id' => $event->id,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'category' => $validated['category'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        return back()->with(FlashMessage::SUCCESS, 'Requirement added successfully.');
    }

    public function destroyRequirement(Event $event, EventRequirement $requirement)
    {
        $user = Auth::user();

        if (!$event->mentors()->where('user_id', $user->id)->exists() && !$user->isAdmin()) {
            abort(403);
        }

        if ($event->isRequirementsLocked() && !$user->isAdmin()) {
            return back()->with(FlashMessage::ERROR, 'Requirements are locked after event starts.');
        }

        $requirement->delete();

        return back()->with(FlashMessage::SUCCESS, 'Requirement deleted successfully.');
    }

    public function markPresent(Request $request, Event $event, int $participantId)
    {
        $user = Auth::user();

        if (!$event->mentors()->where('user_id', $user->id)->exists() && !$user->isAdmin()) {
            abort(403);
        }

        if (!$event->isOngoing()) {
            return back()->with(FlashMessage::ERROR, 'Can only mark attendance during ongoing event.');
        }

        $participant = $event->participants()->where('id', $participantId)->first();
        if (!$participant) {
            abort(404);
        }

        $participant->update([
            'attendance_status' => 'present',
            'checked_in_at' => now(),
        ]);

        return back()->with(FlashMessage::SUCCESS, 'Participant marked as present.');
    }

    public function markCompleted(Request $request, Event $event, int $participantId)
    {
        $user = Auth::user();

        if (!$event->mentors()->where('user_id', $user->id)->exists() && !$user->isAdmin()) {
            abort(403);
        }

        $participant = $event->participants()->where('id', $participantId)->first();
        if (!$participant || $participant->attendance_status !== 'present') {
            return back()->with(FlashMessage::ERROR, 'Participant must be present to mark as completed.');
        }

        $participant->update([
            'completion_status' => 'completed',
        ]);

        return back()->with(FlashMessage::SUCCESS, 'Participant marked as completed.');
    }

    // Admin event actions
    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:workshop,seminar,mentoring',
            'level' => 'required|integer|min:1|max:4',
            'mode' => 'required|in:online,onsite,hybrid',
            'start_at' => 'required|date|after:now',
            'end_at' => 'required|date|after:start_at',
            'capacity' => 'nullable|integer|min:1',
            'location_text' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url|max:500',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        Event::create($validated);

        return redirect()->route('users.dashboard', ['username' => auth()->user()->username, 'tab' => 'admin'])->with(FlashMessage::SUCCESS, 'Event created successfully.');
    }

    public function updateEvent(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:workshop,seminar,mentoring',
            'level' => 'required|integer|min:1|max:4',
            'mode' => 'required|in:online,onsite,hybrid',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'capacity' => 'nullable|integer|min:1',
            'location_text' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url|max:500',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,cancelled,ended',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        $event->update($validated);

        return redirect()->route('users.dashboard', ['username' => auth()->user()->username, 'tab' => 'admin'])->with(FlashMessage::SUCCESS, 'Event updated successfully.');
    }

    public function destroyEvent(Event $event)
    {
        if ($event->status !== 'draft' && $event->participants()->count() > 0) {
            return back()->with(FlashMessage::ERROR, 'Cannot delete event with participants.');
        }

        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return redirect()->route('users.dashboard', ['username' => auth()->user()->username, 'tab' => 'admin'])->with(FlashMessage::SUCCESS, 'Event deleted successfully.');
    }

    // Admin mentor assignment
    public function storeMentor(Request $request, Event $event)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:mentor,co-mentor,speaker,moderator',
            'goal_title' => 'nullable|string|max:255',
            'target_participants' => 'nullable|integer|min:1',
        ]);

        if ($event->mentors()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with(FlashMessage::ERROR, 'User is already assigned to this event.');
        }

        EventMentor::create([
            'event_id' => $event->id,
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
            'goal_title' => $validated['goal_title'] ?? null,
            'target_participants' => $validated['target_participants'] ?? null,
            'goal_status' => 'planned',
        ]);

        return back()->with(FlashMessage::SUCCESS, 'Mentor assigned successfully.');
    }

    public function updateMentor(Request $request, Event $event, EventMentor $mentor)
    {
        $validated = $request->validate([
            'role' => 'required|in:mentor,co-mentor,speaker,moderator',
            'goal_title' => 'nullable|string|max:255',
            'target_participants' => 'nullable|integer|min:1',
        ]);

        $mentor->update($validated);

        return back()->with(FlashMessage::SUCCESS, 'Mentor updated successfully.');
    }

    public function destroyMentor(Event $event, EventMentor $mentor)
    {
        $mentor->delete();

        return back()->with(FlashMessage::SUCCESS, 'Mentor removed from event.');
    }

    // Admin review moderation
    public function destroyReview(Event $event, int $reviewId)
    {
        $event->feedback()->where('id', $reviewId)->delete();

        return back()->with(FlashMessage::SUCCESS, 'Review deleted successfully.');
    }

    // Finalization
    public function finalizeEvent(Event $event)
    {
        if (!$event->isEnded()) {
            return back()->with(FlashMessage::ERROR, 'Can only finalize ended events.');
        }

        if ($event->finalized_at) {
            return back()->with(FlashMessage::ERROR, 'Event is already finalized.');
        }

        // Mark absent participants
        $event->participants()
            ->where('registration_status', 'registered')
            ->whereNull('attendance_status')
            ->update(['attendance_status' => 'absent']);

        $event->update(['finalized_at' => now()]);

        return back()->with(FlashMessage::SUCCESS, 'Event finalized successfully.');
    }

    public function runBatchFinalization()
    {
        $events = Event::ended()
            ->whereNull('finalized_at')
            ->get();

        $count = 0;
        foreach ($events as $event) {
            $event->participants()
                ->where('registration_status', 'registered')
                ->whereNull('attendance_status')
                ->update(['attendance_status' => 'absent']);

            $event->update(['finalized_at' => now()]);
            $count++;
        }

        return back()->with(FlashMessage::SUCCESS, "Finalized {$count} events.");
    }
}
