<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Models\EventMentor;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Handles data loading for user dashboard tabs.
 * 
 * Responsibilities:
 * - Overview data aggregation
 * - Events data with filtering
 * - Mentor panel data
 * - Admin panel data
 */
class DashboardService
{
    // =========================================================================
    // OVERVIEW TAB
    // =========================================================================

    public function loadOverviewData(User $user, bool $isMentor): array
    {
        $data = [
            'eventStats' => $this->getEventStats($user),
            'upcomingEvents' => $this->getUpcomingEvents($user, limit: 3),
        ];

        if ($isMentor) {
            $data['mentorStats'] = $this->getMentorStats($user);
        }

        return $data;
    }

    // =========================================================================
    // EVENTS TAB
    // =========================================================================

    public function loadEventsData(User $user, string $filter): array
    {
        return [
            'eventStats' => $this->getEventStats($user),
            'eventCounts' => $this->getEventCounts($user),
            'userEvents' => $this->getFilteredEvents($user, $filter),
            'eventFilter' => $filter,
        ];
    }

    // =========================================================================
    // MENTOR TAB
    // =========================================================================

    public function loadMentorData(User $user, ?string $status): array
    {
        return [
            'mentorEvents' => $this->getMentorEvents($user, $status),
            'mentorStatus' => $status,
            'mentorCounts' => $this->getMentorEventCounts($user),
        ];
    }

    public function loadMentorEventDetail(User $user, Event $event, string $subtab): array
    {
        $mentorRecord = $event->mentors()->where('user_id', $user->id)->first();

        if (!$mentorRecord && !$user->isAdmin()) {
            abort(403, 'You are not assigned to this event.');
        }

        $event->load(['mentors.user']);
        $participants = $event->participants()->with('user')->orderBy('joined_at', 'desc')->get();
        $requirements = $event->requirementItems()->get();

        $reviews = collect();
        $avgRating = null;
        if ($event->isEnded()) {
            $reviews = $event->feedback()->global()->with('fromUser')->latest()->get();
            $avgRating = $event->feedback()->global()->avg('rating');
        }

        return [
            'mentorEvent' => $event,
            'mentorRecord' => $mentorRecord,
            'mentorSubtab' => $subtab,
            'participants' => $participants,
            'infoRequirements' => $requirements->where('type', 'info')->values(),
            'checklistRequirements' => $requirements->where('type', 'checklist')->values(),
            'techRequirements' => $requirements->where('type', 'tech')->groupBy('category'),
            'reviews' => $reviews,
            'avgRating' => $avgRating,
            'participantCounts' => $this->getParticipantCounts($participants),
            'requirementsLocked' => $event->isRequirementsLocked() && !$user->isAdmin(),
        ];
    }

    // =========================================================================
    // ADMIN TAB
    // =========================================================================

    public function loadAdminData(string $section): array
    {
        if ($section === 'finalization') {
            return $this->loadFinalizationData();
        }

        return [
            'adminSection' => $section,
        ];
    }

    public function loadAdminEventsList(Request $request): array
    {
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
            $this->applyStatusFilter($query, $status);
        }

        return [
            'adminEvents' => $query->orderBy('start_at', 'desc')->paginate(15),
            'adminCounts' => $this->getAdminEventCounts(),
        ];
    }

    public function loadAdminEventDetail(Event $event, string $subtab): array
    {
        $data = [
            'adminEvent' => $event,
            'adminSubtab' => $subtab,
        ];

        match ($subtab) {
            'mentors' => $data = array_merge($data, $this->loadEventMentorsData($event)),
            'requirements' => $data = array_merge($data, $this->loadEventRequirementsData($event)),
            'reviews' => $data = array_merge($data, $this->loadEventReviewsData($event)),
            default => null,
        };

        return $data;
    }

    public function loadFinalizationData(): array
    {
        return [
            'adminSection' => 'finalization',
            'pendingEvents' => $this->getPendingFinalizationEvents(),
            'finalizedEvents' => $this->getRecentlyFinalizedEvents(),
        ];
    }

    // =========================================================================
    // PRIVATE HELPERS - Events
    // =========================================================================

    private function getEventStats(User $user): array
    {
        return [
            'registered' => $user->participatedEvents()->wherePivot('registration_status', 'registered')->count(),
            'attended' => $user->participatedEvents()->wherePivot('attendance_status', 'present')->count(),
            'completed' => $user->participatedEvents()->wherePivot('completion_status', 'completed')->count(),
        ];
    }

    private function getEventCounts(User $user): array
    {
        $registered = $user->participatedEvents()->wherePivot('registration_status', 'registered');

        return [
            'upcoming' => (clone $registered)->upcoming()->count(),
            'ongoing' => (clone $registered)->ongoing()->count(),
            'past' => (clone $registered)->ended()->count(),
        ];
    }

    private function getUpcomingEvents(User $user, int $limit): \Illuminate\Database\Eloquent\Collection
    {
        return $user->participatedEvents()
            ->wherePivot('registration_status', 'registered')
            ->upcoming()
            ->orderBy('start_at', 'asc')
            ->limit($limit)
            ->get();
    }

    private function getFilteredEvents(User $user, string $filter)
    {
        $query = $user->participatedEvents()->wherePivot('registration_status', 'registered');

        match ($filter) {
            'ongoing' => $query->ongoing()->orderBy('start_at', 'asc'),
            'past' => $query->ended()->orderBy('start_at', 'desc'),
            default => $query->upcoming()->orderBy('start_at', 'asc'),
        };

        return $query->paginate(10);
    }

    // =========================================================================
    // PRIVATE HELPERS - Mentor
    // =========================================================================

    private function getMentorStats(User $user): array
    {
        $mentorEvents = Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id));

        return [
            'events' => (clone $mentorEvents)->count(),
            'upcoming' => (clone $mentorEvents)->upcoming()->count(),
        ];
    }

    private function getMentorEvents(User $user, ?string $status)
    {
        $query = Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id))
            ->with(['mentors' => fn($q) => $q->where('user_id', $user->id)])
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'participants as present_count' => fn($q) => $q->where('attendance_status', 'present'),
                'participants as completed_count' => fn($q) => $q->where('completion_status', 'completed'),
            ]);

        if ($status) {
            $this->applyStatusFilter($query, $status);
        }

        return $query->orderBy('start_at', 'desc')->paginate(10);
    }

    private function getMentorEventCounts(User $user): array
    {
        $baseQuery = fn() => Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id));

        return [
            'all' => $baseQuery()->count(),
            'upcoming' => $baseQuery()->upcoming()->count(),
            'ongoing' => $baseQuery()->ongoing()->count(),
            'ended' => $baseQuery()->ended()->count(),
        ];
    }

    private function getParticipantCounts($participants): array
    {
        return [
            'registered' => $participants->where('registration_status', 'registered')->count(),
            'present' => $participants->where('attendance_status', 'present')->count(),
            'absent' => $participants->where('attendance_status', 'absent')->count(),
            'completed' => $participants->where('completion_status', 'completed')->count(),
        ];
    }

    // =========================================================================
    // PRIVATE HELPERS - Admin
    // =========================================================================

    private function getAdminEventCounts(): array
    {
        return [
            'all' => Event::count(),
            'draft' => Event::where('status', 'draft')->count(),
            'upcoming' => Event::upcoming()->count(),
            'ongoing' => Event::ongoing()->count(),
            'ended' => Event::ended()->count(),
        ];
    }

    private function loadEventMentorsData(Event $event): array
    {
        $event->load(['mentors.user']);

        return [
            'eventMentors' => $event->mentors,
            'availableMentors' => User::whereHas('role', fn($q) => $q->whereIn('name', ['mentor', 'admin']))
                ->whereNotIn('id', $event->mentors->pluck('user_id'))
                ->orderBy('name')
                ->get(),
        ];
    }

    private function loadEventRequirementsData(Event $event): array
    {
        $requirements = $event->requirementItems()->get();

        return [
            'infoRequirements' => $requirements->where('type', 'info')->values(),
            'checklistRequirements' => $requirements->where('type', 'checklist')->values(),
            'techRequirements' => $requirements->where('type', 'tech')->groupBy('category'),
        ];
    }

    private function loadEventReviewsData(Event $event): array
    {
        $reviews = $event->feedback()->global();

        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = (clone $reviews)->where('rating', $i)->count();
        }

        return [
            'eventReviews' => $reviews->with('fromUser')->latest()->paginate(20),
            'avgRating' => $event->feedback()->global()->avg('rating'),
            'reviewCount' => $event->feedback()->global()->count(),
            'ratingDistribution' => $ratingDistribution,
        ];
    }

    private function getPendingFinalizationEvents()
    {
        return Event::ended()
            ->whereNull('finalized_at')
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'participants as present_count' => fn($q) => $q->where('attendance_status', 'present'),
                'participants as completed_count' => fn($q) => $q->where('completion_status', 'completed'),
            ])
            ->orderBy('end_at', 'desc')
            ->get();
    }

    private function getRecentlyFinalizedEvents()
    {
        return Event::whereNotNull('finalized_at')
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'participants as completed_count' => fn($q) => $q->where('completion_status', 'completed'),
            ])
            ->orderBy('finalized_at', 'desc')
            ->limit(10)
            ->get();
    }

    // =========================================================================
    // PRIVATE HELPERS - Common
    // =========================================================================

    private function applyStatusFilter($query, string $status): void
    {
        match ($status) {
            'upcoming' => $query->upcoming(),
            'ongoing' => $query->ongoing(),
            'ended' => $query->ended(),
            'draft' => $query->where('status', 'draft'),
            'cancelled' => $query->where('status', 'cancelled'),
            default => null,
        };
    }
}
