<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Console\Command;

class FinalizeEndedEvents extends Command
{
    protected $signature = 'events:finalize-attendance';

    protected $description = 'Finalize attendance and completion status for ended events';

    public function handle(): int
    {
        // Only process events that have ended AND not yet finalized
        $endedEvents = Event::where('end_at', '<', now())
            ->whereIn('status', ['published', 'ended'])
            ->whereNull('finalized_at')
            ->get();

        if ($endedEvents->isEmpty()) {
            $this->info('No events to finalize.');
            return Command::SUCCESS;
        }

        $totalAbsent = 0;
        $totalIncomplete = 0;
        $finalizedCount = 0;

        foreach ($endedEvents as $event) {
            // Task 1: Mark absent - registered but never checked in
            $absentCount = EventParticipant::where('event_id', $event->id)
                ->where('registration_status', 'registered')
                ->whereNull('attendance_status')
                ->update(['attendance_status' => 'absent']);

            $totalAbsent += $absentCount;

            // Task 2: Mark incomplete - present but completion not set
            $incompleteCount = EventParticipant::where('event_id', $event->id)
                ->where('registration_status', 'registered')
                ->where('attendance_status', 'present')
                ->whereNull('completion_status')
                ->update(['completion_status' => 'not_completed']);

            $totalIncomplete += $incompleteCount;

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

            $finalizedCount++;
        }

        $this->info("Finalized {$finalizedCount} events.");
        $this->info("Marked {$totalAbsent} participants as absent.");
        $this->info("Marked {$totalIncomplete} participants as not completed.");

        return Command::SUCCESS;
    }
}
