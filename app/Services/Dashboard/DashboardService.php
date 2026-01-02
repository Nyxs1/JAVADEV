<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Models\User;
use App\Services\Shared\Dashboard\DashboardContext;
use Illuminate\Http\Request;

/**
 * Orchestrator service that delegates to specialized dashboard services.
 * 
 * This is a thin coordinator - all business logic lives in the sub-services:
 * - OverviewDashboardService: Overview tab data
 * - EventsDashboardService: Events tab data
 * - MentorDashboardService: Mentor tab data
 * - AdminDashboardService: Admin tab data
 */
class DashboardService
{
    public function __construct(
        private OverviewDashboardService $overview,
        private EventsDashboardService $events,
        private MentorDashboardService $mentor,
        private AdminDashboardService $admin,
    ) {
    }

    /**
     * Get dashboard payload based on tab context.
     * 
     * @param User $user The dashboard owner
     * @param DashboardContext $ctx Request context with tab/filter/etc
     * @param array $flags Additional flags like isMentor, isAdmin
     * @return array Data payload for view
     */
    public function payload(User $user, DashboardContext $ctx, array $flags = []): array
    {
        return match ($ctx->tab) {
            'events' => $this->events->load($user, $ctx->filter),
            'mentor' => $this->mentor->load($user, $ctx),
            'admin' => $this->admin->load($ctx, request()),
            'portfolio' => [
                'portfolios' => $user->portfolios()->with(['evidences', 'builtFromCourse', 'screenshots'])->get(),
                'userCourses' => $user->userCourses()->get(),
            ],
            'courses' => ['enrolledCourses' => collect()],
            'discussions' => ['userDiscussions' => collect()],
            default => $this->overview->load($user, $flags['isMentor'] ?? false),
        };
    }

    // =========================================================================
    // DELEGATED ACTION METHODS (backwards compatibility for controller)
    // =========================================================================

    /**
     * Mark untracked participants as absent.
     * Delegates to MentorDashboardService.
     */
    public function markAbsentParticipants(Event $event): void
    {
        $this->mentor->markAbsentParticipants($event);
    }

    /**
     * Delete event cover image from storage.
     * Delegates to AdminDashboardService.
     */
    public function deleteEventCoverImage(Event $event): void
    {
        $this->admin->deleteEventCoverImage($event);
    }
}
