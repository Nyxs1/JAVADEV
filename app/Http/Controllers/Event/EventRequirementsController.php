<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Requests\Event\UpdateRequirementsRequest;
use App\Http\Support\FlashMessage;
use Illuminate\Support\Facades\Gate;

class EventRequirementsController extends Controller
{
    public function edit(Event $event)
    {
        Gate::authorize('updateRequirements', $event);

        $isLocked = Gate::check('requirementsLocked', $event);

        return view('events.requirements-edit', compact('event', 'isLocked'));
    }

    public function update(UpdateRequirementsRequest $request, Event $event)
    {
        Gate::authorize('updateRequirements', $event);

        // Check if locked for non-admin
        if (Gate::check('requirementsLocked', $event)) {
            return back()->with(FlashMessage::ERROR, 'Requirements cannot be edited after event has started.');
        }

        $event->requirements = $request->getRequirementsData();
        $event->save();

        return redirect()
            ->route('events.show', $event)
            ->with(FlashMessage::SUCCESS, 'Requirements updated successfully.');
    }
}
