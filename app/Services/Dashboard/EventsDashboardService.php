<?php

namespace App\Services\Dashboard;

use App\Models\User;

/**
 * Handles events tab data loading.
 */
class EventsDashboardService
{
    /**
     * Load events tab data.
     */
    public function load(User $user, string $filter): array
    {
        return [
            'eventStats' => $this->getEventStats($user),
            'eventCounts' => $this->getEventCounts($user),
            'userEvents' => $this->getFilteredEvents($user, $filter),
            'eventFilter' => $filter,
        ];
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
     * Get event counts by filter category.
     */
    private function getEventCounts(User $user): array
    {
        $registered = $user->participatedEvents()->wherePivot('registration_status', 'registered');

        return [
            'upcoming' => (clone $registered)->upcoming()->count(),
            'ongoing' => (clone $registered)->ongoing()->count(),
            'past' => (clone $registered)->ended()->count(),
        ];
    }

    /**
     * Get filtered events with pagination.
     */
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
}
