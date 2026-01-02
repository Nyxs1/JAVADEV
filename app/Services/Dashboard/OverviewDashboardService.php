<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Models\User;

/**
 * Handles overview tab data loading.
 */
class OverviewDashboardService
{
    /**
     * Load overview data for user dashboard.
     */
    public function load(User $user, bool $isMentor): array
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

    /**
     * Get user's event participation stats.
     */
    private function getEventStats(User $user): array
    {
        return [
            'registered' => $user->participatedEvents()->wherePivot('registration_status', 'registered')->count(),
            'attended' => $user->participatedEvents()->wherePivot('attendance_status', 'present')->count(),
            'completed' => $user->participatedEvents()->wherePivot('completion_status', 'completed')->count(),
        ];
    }

    /**
     * Get upcoming events for user.
     */
    private function getUpcomingEvents(User $user, int $limit): \Illuminate\Database\Eloquent\Collection
    {
        return $user->participatedEvents()
            ->wherePivot('registration_status', 'registered')
            ->upcoming()
            ->orderBy('start_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get mentor-specific stats for overview.
     */
    private function getMentorStats(User $user): array
    {
        $mentorEvents = Event::whereHas('mentors', fn($q) => $q->where('user_id', $user->id));

        return [
            'events' => (clone $mentorEvents)->count(),
            'upcoming' => (clone $mentorEvents)->upcoming()->count(),
        ];
    }
}
