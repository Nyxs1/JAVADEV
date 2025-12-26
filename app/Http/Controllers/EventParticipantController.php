<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Support\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EventParticipantController extends Controller
{
    /**
     * Check-in to an event.
     */
    public function checkIn(Event $event)
    {
        Gate::authorize('checkIn', $event);

        $user = Auth::user();

        $participant = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->first();

        if (!$participant) {
            return back()->with(FlashMessage::ERROR, 'You are not registered for this event.');
        }

        if ($participant->isCheckedIn()) {
            return back()->with(FlashMessage::INFO, 'You have already checked in.');
        }

        $participant->checkIn();

        // Sync mentor achieved participants
        $event->mentors->each(fn($mentor) => $mentor->syncAchievedParticipants());

        return back()->with(FlashMessage::SUCCESS, 'Check-in successful!');
    }

    /**
     * Submit reflection for an event.
     */
    public function submitReflection(Request $request, Event $event)
    {
        Gate::authorize('submitReflection', $event);

        $validated = $request->validate([
            'reflection' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        $participant = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->where('attendance_status', 'present')
            ->first();

        if (!$participant) {
            return back()->with(FlashMessage::ERROR, 'You must have attended this event to submit a reflection.');
        }

        $participant->update(['reflection' => $validated['reflection']]);

        return back()->with(FlashMessage::SUCCESS, 'Reflection submitted successfully!');
    }

    /**
     * Get certificate (if available).
     */
    public function certificate(Event $event)
    {
        $user = Auth::user();

        $participant = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->first();

        if (!$participant) {
            abort(404, 'Participant not found.');
        }

        if (!$participant->canReceiveCertificate()) {
            return back()->with(FlashMessage::ERROR, 'Certificate not available.');
        }

        // Redirect to certificate URL or download
        return redirect($participant->certificate_url);
    }
}
