<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine if user can view the event.
     */
    public function view(?User $user, Event $event): bool
    {
        // Published events are public
        if ($event->status === 'published' || $event->status === 'ended') {
            return true;
        }

        // Draft/cancelled only visible to admin or assigned mentors
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $event->mentors()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine if user can update event requirements.
     */
    public function updateRequirements(User $user, Event $event): bool
    {
        // Admin can always update (even after start, if needed)
        if ($user->isAdmin()) {
            return true;
        }

        // Mentor can update only if assigned to this event
        if ($user->isMentor()) {
            return $event->mentors()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Check if requirements are locked (event has started).
     */
    public function requirementsLocked(User $user, Event $event): bool
    {
        // Admin can override lock
        if ($user->isAdmin()) {
            return false;
        }

        return $event->isRequirementsLocked();
    }

    /**
     * Determine if user can submit a review.
     */
    public function submitReview(User $user, Event $event): bool
    {
        // Event must be ended
        if (!$event->isEnded()) {
            return false;
        }

        // User must be a registered participant who attended (present)
        $participant = $event->participants()
            ->where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->where('attendance_status', 'present')
            ->first();

        return $participant !== null;
    }

    /**
     * Determine if user can join the event.
     */
    public function join(User $user, Event $event): bool
    {
        // Event must be published and upcoming (not started, not ended)
        if ($event->status !== 'published') {
            return false;
        }

        // Cannot join if event has started or ended
        if ($event->isStarted() || $event->isEnded()) {
            return false;
        }

        // Cannot join if event is full
        if ($event->isFull()) {
            return false;
        }

        // User must not already be registered
        $existing = $event->participants()
            ->where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->exists();

        return !$existing;
    }

    /**
     * Determine if user can cancel their registration.
     */
    public function cancel(User $user, Event $event): bool
    {
        // Event must not have started
        if ($event->isStarted()) {
            return false;
        }

        // User must be registered
        $participant = $event->participants()
            ->where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->exists();

        return $participant;
    }

    /**
     * Determine if user can check-in to the event.
     */
    public function checkIn(User $user, Event $event): bool
    {
        // Event must be ongoing
        if (!$event->isOngoing()) {
            return false;
        }

        // User must be registered
        $participant = $event->participants()
            ->where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->first();

        if (!$participant) {
            return false;
        }

        // Must not already be checked in
        return $participant->attendance_status !== 'present';
    }

    /**
     * Determine if user can submit reflection.
     */
    public function submitReflection(User $user, Event $event): bool
    {
        // Event must be ended
        if (!$event->isEnded()) {
            return false;
        }

        // User must be a registered participant who attended
        $participant = $event->participants()
            ->where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->where('attendance_status', 'present')
            ->first();

        return $participant !== null;
    }
}
