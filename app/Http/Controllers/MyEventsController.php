<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\EventMentor;
use Illuminate\Support\Facades\Auth;

class MyEventsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get user's participated events
        $participantEvents = EventParticipant::where('user_id', $user->id)
            ->where('registration_status', 'registered')
            ->with([
                'event' => function ($q) {
                    $q->withCount([
                        'participants' => function ($q) {
                            $q->where('registration_status', 'registered');
                        }
                    ]);
                }
            ])
            ->get();

        // Get user's mentor events (if mentor)
        $mentorEvents = collect();
        if ($user->isMentor()) {
            $mentorEvents = EventMentor::where('user_id', $user->id)
                ->with([
                    'event' => function ($q) {
                        $q->withCount([
                            'participants' => function ($q) {
                                $q->where('registration_status', 'registered');
                            }
                        ]);
                    }
                ])
                ->get();
        }

        // Separate into upcoming and past
        $upcomingParticipant = $participantEvents->filter(function ($p) {
            return $p->event && !$p->event->isEnded();
        });

        $pastParticipant = $participantEvents->filter(function ($p) {
            return $p->event && $p->event->isEnded();
        });

        $upcomingMentor = $mentorEvents->filter(function ($m) {
            return $m->event && !$m->event->isEnded();
        });

        $pastMentor = $mentorEvents->filter(function ($m) {
            return $m->event && $m->event->isEnded();
        });

        return view('events.my-events', compact(
            'upcomingParticipant',
            'pastParticipant',
            'upcomingMentor',
            'pastMentor'
        ));
    }
}
