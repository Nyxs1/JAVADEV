<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRequirement;
use App\Models\EventRequirementCheck;
use App\Http\Support\Traits\JsonResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EventChecklistController extends Controller
{
    use JsonResponses;

    public function toggle(Event $event, EventRequirement $requirement): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return $this->jsonError('Unauthorized', [], 401);
        }

        // Verify requirement belongs to event
        if ($requirement->event_id !== $event->id) {
            return $this->jsonError('Invalid requirement', [], 400);
        }

        // Verify requirement is checklist type
        if ($requirement->type !== 'checklist') {
            return $this->jsonError('Only checklist items can be toggled', [], 400);
        }

        // Verify user is registered for event
        $isRegistered = $user->isRegisteredForEvent($event);
        if (!$isRegistered) {
            return $this->jsonError('Join event untuk mulai checklist persiapan.', [], 403);
        }

        // Toggle check status
        $check = EventRequirementCheck::where('event_id', $event->id)
            ->where('requirement_id', $requirement->id)
            ->where('user_id', $user->id)
            ->first();

        if ($check) {
            $newStatus = !$check->is_checked;
            $check->update([
                'is_checked' => $newStatus,
                'checked_at' => $newStatus ? now() : null,
            ]);
        } else {
            $check = EventRequirementCheck::create([
                'event_id' => $event->id,
                'requirement_id' => $requirement->id,
                'user_id' => $user->id,
                'is_checked' => true,
                'checked_at' => now(),
            ]);
            $newStatus = true;
        }

        return $this->jsonSuccess('Checklist updated', [
            'data' => [
                'is_checked' => $newStatus,
                'requirement_id' => $requirement->id,
            ]
        ]);
    }
}
