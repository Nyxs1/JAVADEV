<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Models\User;
use App\Support\Dashboard\DashboardContext;

/**
 * Handles mentor tab data loading.
 */
class MentorDashboardService
{
    /**
     * Load mentor tab data based on context.
     */
    public function load(User $user, DashboardContext $ctx): array
    {
        if (!$ctx->hasEventDetail()) {
            return $this->loadEventsList($user, $ctx->status);
        }

        return $this->loadEventDetail(
            $user,
            Event::where('slug', $ctx->eventSlug)->firstOrFail(),
            $ctx->subtab
        );
    }

    /**
     * Load mentor events list.
     */
    public function loadEventsList(User $user, ?string $status): array
    {
        return [
            'mentorEvents' => $this->getMentorEvents($user, $status),
            'mentorStatus' => $status,
            'mentorCounts' => $this->getMentorEventCounts($user),
        ];
    }

    /**
     * Load mentor event detail view.
     */
    public function loadEventDetail(User $user, Event $event, string $subtab): array
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

    /**
     * Mark untracked participants as absent for finalization.
     */
    public function markAbsentParticipants(Event $event): void
    {
        $event->participants()
            ->where('registration_status', 'registered')
            ->whereNull('attendance_status')
            ->update(['attendance_status' => 'absent']);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

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
