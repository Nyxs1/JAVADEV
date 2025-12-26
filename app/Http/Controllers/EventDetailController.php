<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EventDetailController extends Controller
{
    public function show(Event $event)
    {
        $user = Auth::user();

        // Load relationships
        $event->load([
            'mentors.user',
            'feedback' => function ($query) {
                $query->global()->with('fromUser')->latest()->limit(10);
            },
            'requirementItems',
        ]);

        // Get participant count
        $participantCount = $event->participants()->registered()->count();

        // Get participants preview for avatar stack (max 3)
        $participantsPreview = $event->participants()
            ->registered()
            ->with('user')
            ->limit(3)
            ->get();

        // Initialize user-specific flags
        $isRegistered = false;
        $canReview = false;
        $hasReviewed = false;
        $canJoin = false;
        $canCancel = false;
        $canCheckIn = false;
        $isCheckedIn = false;
        $canSubmitReflection = false;
        $hasReflection = false;
        $isAbsent = false;
        $participant = null;

        if ($user) {
            // Get participant record
            $participant = EventParticipant::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->where('registration_status', 'registered')
                ->first();

            $isRegistered = $participant !== null;

            if ($participant) {
                $isCheckedIn = $participant->isCheckedIn();
                $hasReflection = !empty($participant->reflection);
                $isAbsent = $participant->attendance_status === 'absent';
            }

            // Review logic: event must be ENDED + user must have attended (present)
            $canReview = $event->isEnded() && $isRegistered && !$isAbsent && $participant?->attendance_status === 'present';

            // Check permissions via policy
            $canJoin = Gate::allows('join', $event);
            $canCancel = Gate::allows('cancel', $event);
            $canCheckIn = Gate::allows('checkIn', $event);
            $canSubmitReflection = Gate::allows('submitReflection', $event);

            // Check if already reviewed (one review per user per event)
            if ($canReview) {
                $hasReviewed = $user->hasReviewedEvent($event);
            }
        }

        // Calculate average rating and review count
        $avgRating = $event->feedback()->global()->avg('rating');
        $reviewCount = $event->feedback()->global()->count();

        // Check capacity status
        $isFull = $event->isFull();
        $remainingSpots = $event->getRemainingSpots();

        // Group requirements by type
        $requirementItems = $event->requirementItems;
        $infoRequirements = $requirementItems->where('type', 'info')->values();
        $checklistRequirements = $requirementItems->where('type', 'checklist')->values();
        $techRequirements = $requirementItems->where('type', 'tech')->groupBy('category');

        // Load user's checklist progress if registered
        $userChecks = collect();
        if ($user && $isRegistered) {
            $userChecks = $event->requirementChecks()
                ->where('user_id', $user->id)
                ->where('is_checked', true)
                ->pluck('requirement_id');
        }

        return view('events.show', compact(
            'event',
            'participantCount',
            'participantsPreview',
            'isRegistered',
            'canReview',
            'hasReviewed',
            'canJoin',
            'canCancel',
            'canCheckIn',
            'isCheckedIn',
            'canSubmitReflection',
            'hasReflection',
            'isAbsent',
            'participant',
            'avgRating',
            'reviewCount',
            'isFull',
            'remainingSpots',
            'infoRequirements',
            'checklistRequirements',
            'techRequirements',
            'userChecks'
        ));
    }
}
