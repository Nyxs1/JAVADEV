<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFeedback;
use App\Http\Requests\Event\StoreReviewRequest;
use App\Support\FlashMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EventReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Event $event)
    {
        Gate::authorize('submitReview', $event);

        $user = Auth::user();

        // Check if already reviewed
        $existing = EventFeedback::where('event_id', $event->id)
            ->where('from_user_id', $user->id)
            ->whereNull('to_user_id')
            ->first();

        if ($existing) {
            return back()->with(FlashMessage::ERROR, 'You have already submitted a review for this event.');
        }

        EventFeedback::create([
            'event_id' => $event->id,
            'from_user_id' => $user->id,
            'to_user_id' => null,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        return back()->with(FlashMessage::SUCCESS, 'Thank you for your review!');
    }
}
