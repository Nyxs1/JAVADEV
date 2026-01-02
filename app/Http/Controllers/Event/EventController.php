<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Http\Support\FlashMessage;
use App\Services\Events\FinalizeEndedEventsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function __construct(
        private FinalizeEndedEventsService $finalizeService
    ) {
    }

    /**
     * Display event listing.
     */
    public function index(Request $request)
    {
        // Lazy finalize: auto-finalize any ended events on page load
        // This is safe, idempotent, and lightweight
        $this->finalizeService->runIfNeeded();

        $query = Event::query()
            ->where(function ($q) {
                $q->where('status', 'published')
                    ->orWhere('status', 'ended');
            })
            ->withCount([
                'participants' => function ($q) {
                    $q->where('registration_status', 'registered');
                }
            ])
            ->with([
                'participants' => function ($q) {
                    $q->where('registration_status', 'registered')
                        ->with(['user:id,username,avatar,first_name,last_name'])
                        ->limit(3);
                }
            ]);

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'upcoming') {
                $query->upcoming();
            } elseif ($status === 'ongoing') {
                $query->ongoing();
            } elseif ($status === 'ended') {
                $query->ended();
            }
        }

        // Filter by mode
        if ($request->filled('mode')) {
            $query->mode($request->input('mode'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('start_at', 'desc')->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * Join an event.
     */
    public function join(Event $event)
    {
        Gate::authorize('join', $event);

        $user = Auth::user();

        // Check if event is full
        if ($event->isFull()) {
            return back()->with(FlashMessage::ERROR, 'This event is full.');
        }

        // Check for existing cancelled registration
        $existing = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            // Reactivate cancelled registration
            $existing->update([
                'registration_status' => 'registered',
                'joined_at' => now(),
            ]);
        } else {
            // Create new registration
            EventParticipant::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'registration_status' => 'registered',
                'joined_at' => now(),
            ]);
        }

        return back()->with(FlashMessage::SUCCESS, 'You have successfully joined the event!');
    }

    /**
     * Cancel event registration.
     */
    public function cancel(Event $event)
    {
        Gate::authorize('cancel', $event);

        $user = Auth::user();

        EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->update(['registration_status' => 'cancelled']);

        return back()->with(FlashMessage::SUCCESS, 'Your registration has been cancelled.');
    }
}
