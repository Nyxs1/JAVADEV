<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\Log;

/**
 * Service to finalize ended events.
 * 
 * Replaces the Console command logic with a service that can be called
 * lazily (on page load) or manually (via admin action).
 */
class FinalizeEndedEventsService
{
    /**
     * Run finalization for all ended, unfinalized events.
     * Safe to call multiple times (idempotent).
     * 
     * @return array{finalized: int, absent: int, incomplete: int}
     */
    public function runIfNeeded(): array
    {
        $stats = ['finalized' => 0, 'absent' => 0, 'incomplete' => 0];

        // Get events that have ended AND not yet finalized
        $endedEvents = Event::where('end_at', '<', now())
            ->whereIn('status', ['published', 'ended'])
            ->whereNull('finalized_at')
            ->get();

        if ($endedEvents->isEmpty()) {
            return $stats;
        }

        foreach ($endedEvents as $event) {
            $result = $this->finalizeEvent($event);
            $stats['finalized']++;
            $stats['absent'] += $result['absent'];
            $stats['incomplete'] += $result['incomplete'];
        }

        Log::info("[FinalizeEvents] Finalized {$stats['finalized']} events, marked {$stats['absent']} absent, {$stats['incomplete']} incomplete.");

        return $stats;
    }

    /**
     * Finalize a single event.
     * 
     * @return array{absent: int, incomplete: int}
     */
    public function finalizeEvent(Event $event): array
    {
        // Task 1: Mark absent - registered but never checked in
        $absentCount = EventParticipant::where('event_id', $event->id)
            ->where('registration_status', 'registered')
            ->whereNull('attendance_status')
            ->update(['attendance_status' => 'absent']);

        // Task 2: Mark incomplete - present but completion not set
        $incompleteCount = EventParticipant::where('event_id', $event->id)
            ->where('registration_status', 'registered')
            ->where('attendance_status', 'present')
            ->whereNull('completion_status')
            ->update(['completion_status' => 'not_completed']);

        // Sync mentor goal status and achieved_participants
        $presentCount = EventParticipant::where('event_id', $event->id)
            ->where('attendance_status', 'present')
            ->count();

        $event->mentors()->update([
            'achieved_participants' => $presentCount,
            'goal_status' => 'done',
        ]);

        // Mark event as finalized (prevents re-processing)
        $event->update([
            'status' => 'ended',
            'finalized_at' => now(),
        ]);

        return ['absent' => $absentCount, 'incomplete' => $incompleteCount];
    }
}
