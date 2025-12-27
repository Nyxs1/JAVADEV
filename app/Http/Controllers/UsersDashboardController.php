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
use App\Support\Dashboard\DashboardContext;
use App\Support\FlashMessage;
use App\Support\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UsersDashboardController extends Controller
{
    use JsonResponses;

    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function index(Request $request, string $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $this->authorizeOwnDashboard($user);

        $isMentor = $user->isMentor() || $user->isAdmin();
        $isAdmin = $user->isAdmin();
        $ctx = DashboardContext::fromRequest($request);
        $payload = $this->dashboardService->payload($user, $ctx, compact('isMentor', 'isAdmin'));

        return view('pages.users.dashboard', array_merge([
            'user' => $user,
            'tab' => $ctx->tab,
            'isMentor' => $isMentor,
            'isAdmin' => $isAdmin,
        ], $payload));
    }

    public function storeRequirement(StoreRequirementRequest $request, Event $event)
    {
        $this->authorizeMentorOrAdmin($event);
        if ($this->isRequirementsLocked($event)) {
            return back()->with(FlashMessage::ERROR, 'Requirements are locked after event starts.');
        }
        EventRequirement::create([
            'event_id' => $event->id,
            'title' => $request->validated('title'),
            'type' => $request->validated('type'),
            'category' => $request->validated('category'),
            'order' => ($event->requirementItems()->max('order') ?? 0) + 1,
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
        $event->participants()->where('id', $participantId)->firstOrFail()
            ->update(['attendance_status' => 'present', 'checked_in_at' => now()]);
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

    public function storeEvent(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        $data['created_by'] = Auth::id();
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }
        Event::create($data);
        return $this->redirectToDashboard('admin', 'Event created successfully.');
    }

    public function updateEvent(UpdateEventRequest $request, Event $event)
    {
        $data = $request->validated();
        if ($request->hasFile('cover_image')) {
            $this->dashboardService->deleteEventCoverImage($event);
            $data['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }
        $event->update($data);
        return $this->redirectToDashboard('admin', 'Event updated successfully.');
    }

    public function destroyEvent(Event $event)
    {
        if ($event->status !== 'draft' && $event->participants()->count() > 0) {
            return back()->with(FlashMessage::ERROR, 'Cannot delete event with participants.');
        }
        $this->dashboardService->deleteEventCoverImage($event);
        $event->delete();
        return $this->redirectToDashboard('admin', 'Event deleted successfully.');
    }

    public function storeMentor(StoreMentorRequest $request, Event $event)
    {
        if ($event->mentors()->where('user_id', $request->validated('user_id'))->exists()) {
            return back()->with(FlashMessage::ERROR, 'User is already assigned to this event.');
        }
        EventMentor::create([
            'event_id' => $event->id,
            'user_id' => $request->validated('user_id'),
            'role' => $request->validated('role'),
            'goal_title' => $request->validated('goal_title'),
            'target_participants' => $request->validated('target_participants'),
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

    public function destroyReview(Event $event, int $reviewId)
    {
        $event->feedback()->where('id', $reviewId)->delete();
        return back()->with(FlashMessage::SUCCESS, 'Review deleted successfully.');
    }

    public function finalizeEvent(Event $event)
    {
        if (!$event->isEnded()) {
            return back()->with(FlashMessage::ERROR, 'Can only finalize ended events.');
        }
        if ($event->finalized_at) {
            return back()->with(FlashMessage::ERROR, 'Event is already finalized.');
        }
        $this->dashboardService->markAbsentParticipants($event);
        $event->update(['finalized_at' => now()]);
        return back()->with(FlashMessage::SUCCESS, 'Event finalized successfully.');
    }

    public function runBatchFinalization()
    {
        $events = Event::ended()->whereNull('finalized_at')->get();
        foreach ($events as $event) {
            $this->dashboardService->markAbsentParticipants($event);
            $event->update(['finalized_at' => now()]);
        }
        return back()->with(FlashMessage::SUCCESS, "Finalized {$events->count()} events.");
    }

    private function authorizeOwnDashboard(User $user): void
    {
        if (Auth::id() !== $user->id)
            abort(403, 'You can only access your own dashboard.');
    }

    private function authorizeMentorOrAdmin(Event $event): void
    {
        $user = Auth::user();
        if (!$event->mentors()->where('user_id', $user->id)->exists() && !$user->isAdmin())
            abort(403);
    }

    private function isRequirementsLocked(Event $event): bool
    {
        return $event->isRequirementsLocked() && !Auth::user()->isAdmin();
    }

    private function redirectToDashboard(string $tab, string $message)
    {
        return redirect()->route('users.dashboard', ['username' => Auth::user()->username, 'tab' => $tab])
            ->with(FlashMessage::SUCCESS, $message);
    }
}
