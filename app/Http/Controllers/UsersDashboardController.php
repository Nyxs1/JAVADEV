<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\StoreMentorRequest;
use App\Http\Requests\Event\StoreRequirementRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Requests\Event\UpdateMentorRequest;
use App\Models\Event;
use App\Models\EventMentor;
use App\Models\EventRequirement;
use App\Models\User;
use App\Services\Dashboard\DashboardService;
use App\Support\FlashMessage;
use App\Support\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsersDashboardController extends Controller
{
    use JsonResponses;

    public function __construct(
        private DashboardService $dashboardService
    ) {
    }

    // =========================================================================
    // MAIN DASHBOARD
    // =========================================================================

    public function index(Request $request, string $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $this->authorizeOwnDashboard($user);

        $tab = $request->get('tab', 'overview');
        $isMentor = $user->isMentor() || $user->isAdmin();
        $isAdmin = $user->isAdmin();

        $data = [
            'user' => $user,
            'tab' => $tab,
            'isMentor' => $isMentor,
            'isAdmin' => $isAdmin,
        ];

        $data = array_merge($data, $this->loadTabData($tab, $user, $isMentor, $request));

        return view('pages.users.dashboard', $data);
    }

    // =========================================================================
    // MENTOR ACTIONS
    // =========================================================================

    public function storeRequirement(StoreRequirementRequest $request, Event $event)
    {
        $this->authorizeMentorOrAdmin($event);

        if ($this->isRequirementsLocked($event)) {
            return back()->with(FlashMessage::ERROR, 'Requirements are locked after event starts.');
        }

        $validated = $request->validated();
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
        $this->authorizeMentorOrAdmin($event);

        if ($this->isRequirementsLocked($event)) {
            return back()->with(FlashMessage::ERROR, 'Requirements are locked after event starts.');
        }

        $requirement->delete();

        return back()->with(FlashMessage::SUCCESS, 'Requirement deleted successfully.');
    }

    public function markPresent(Request $request, Event $event, int $participantId)
    {
        $this->authorizeMentorOrAdmin($event);

        if (!$event->isOngoing()) {
            return back()->with(FlashMessage::ERROR, 'Can only mark attendance during ongoing event.');
        }

        $participant = $event->participants()->where('id', $participantId)->firstOrFail();
        $participant->update([
            'attendance_status' => 'present',
            'checked_in_at' => now(),
        ]);

        return back()->with(FlashMessage::SUCCESS, 'Participant marked as present.');
    }

    public function markCompleted(Request $request, Event $event, int $participantId)
    {
        $this->authorizeMentorOrAdmin($event);

        $participant = $event->participants()->where('id', $participantId)->firstOrFail();

        if ($participant->attendance_status !== 'present') {
            return back()->with(FlashMessage::ERROR, 'Participant must be present to mark as completed.');
        }

        $participant->update(['completion_status' => 'completed']);

        return back()->with(FlashMessage::SUCCESS, 'Participant marked as completed.');
    }

    // =========================================================================
    // ADMIN EVENT ACTIONS
    // =========================================================================

    public function storeEvent(StoreEventRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['created_by'] = Auth::id();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        Event::create($validated);

        return $this->redirectToDashboard('admin', 'Event created successfully.');
    }

    public function updateEvent(UpdateEventRequest $request, Event $event)
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            $this->deleteOldCoverImage($event);
            $validated['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        $event->update($validated);

        return $this->redirectToDashboard('admin', 'Event updated successfully.');
    }

    public function destroyEvent(Event $event)
    {
        if ($event->status !== 'draft' && $event->participants()->count() > 0) {
            return back()->with(FlashMessage::ERROR, 'Cannot delete event with participants.');
        }

        $this->deleteOldCoverImage($event);
        $event->delete();

        return $this->redirectToDashboard('admin', 'Event deleted successfully.');
    }

    // =========================================================================
    // ADMIN MENTOR ASSIGNMENT
    // =========================================================================

    public function storeMentor(StoreMentorRequest $request, Event $event)
    {
        $validated = $request->validated();

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

    public function updateMentor(UpdateMentorRequest $request, Event $event, EventMentor $mentor)
    {
        $mentor->update($request->validated());

        return back()->with(FlashMessage::SUCCESS, 'Mentor updated successfully.');
    }

    public function destroyMentor(Event $event, EventMentor $mentor)
    {
        $mentor->delete();

        return back()->with(FlashMessage::SUCCESS, 'Mentor removed from event.');
    }

    // =========================================================================
    // ADMIN REVIEW MODERATION
    // =========================================================================

    public function destroyReview(Event $event, int $reviewId)
    {
        $event->feedback()->where('id', $reviewId)->delete();

        return back()->with(FlashMessage::SUCCESS, 'Review deleted successfully.');
    }

    // =========================================================================
    // FINALIZATION
    // =========================================================================

    public function finalizeEvent(Event $event)
    {
        if (!$event->isEnded()) {
            return back()->with(FlashMessage::ERROR, 'Can only finalize ended events.');
        }

        if ($event->finalized_at) {
            return back()->with(FlashMessage::ERROR, 'Event is already finalized.');
        }

        $this->markAbsentParticipants($event);
        $event->update(['finalized_at' => now()]);

        return back()->with(FlashMessage::SUCCESS, 'Event finalized successfully.');
    }

    public function runBatchFinalization()
    {
        $events = Event::ended()->whereNull('finalized_at')->get();

        foreach ($events as $event) {
            $this->markAbsentParticipants($event);
            $event->update(['finalized_at' => now()]);
        }

        return back()->with(FlashMessage::SUCCESS, "Finalized {$events->count()} events.");
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function loadTabData(string $tab, User $user, bool $isMentor, Request $request): array
    {
        return match ($tab) {
            'events' => $this->dashboardService->loadEventsData($user, $request->get('filter', 'upcoming')),
            'portfolio' => ['portfolioItems' => collect()],
            'courses' => ['enrolledCourses' => collect()],
            'discussions' => ['userDiscussions' => collect()],
            'mentor' => $this->loadMentorTabData($user, $request),
            'admin' => $this->loadAdminTabData($user, $request),
            default => $this->dashboardService->loadOverviewData($user, $isMentor),
        };
    }

    private function loadMentorTabData(User $user, Request $request): array
    {
        $eventSlug = $request->get('event');

        if ($eventSlug) {
            $event = Event::where('slug', $eventSlug)->firstOrFail();
            return $this->dashboardService->loadMentorEventDetail(
                $user,
                $event,
                $request->get('subtab', 'participants')
            );
        }

        return $this->dashboardService->loadMentorData($user, $request->get('status'));
    }

    private function loadAdminTabData(User $user, Request $request): array
    {
        $section = $request->get('section', 'events');
        $eventSlug = $request->get('event');

        $data = $this->dashboardService->loadAdminData($section);

        if ($section === 'finalization') {
            return $data;
        }

        if ($eventSlug) {
            $event = Event::where('slug', $eventSlug)->firstOrFail();
            return array_merge($data, $this->dashboardService->loadAdminEventDetail(
                $event,
                $request->get('subtab', 'edit')
            ));
        }

        return array_merge($data, $this->dashboardService->loadAdminEventsList($request));
    }

    private function authorizeOwnDashboard(User $user): void
    {
        if (Auth::id() !== $user->id) {
            abort(403, 'You can only access your own dashboard.');
        }
    }

    private function authorizeMentorOrAdmin(Event $event): void
    {
        $user = Auth::user();

        if (!$event->mentors()->where('user_id', $user->id)->exists() && !$user->isAdmin()) {
            abort(403);
        }
    }

    private function isRequirementsLocked(Event $event): bool
    {
        return $event->isRequirementsLocked() && !Auth::user()->isAdmin();
    }

    private function deleteOldCoverImage(Event $event): void
    {
        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }
    }

    private function markAbsentParticipants(Event $event): void
    {
        $event->participants()
            ->where('registration_status', 'registered')
            ->whereNull('attendance_status')
            ->update(['attendance_status' => 'absent']);
    }

    private function redirectToDashboard(string $tab, string $message)
    {
        return redirect()
            ->route('users.dashboard', ['username' => Auth::user()->username, 'tab' => $tab])
            ->with(FlashMessage::SUCCESS, $message);
    }
}
