<?php

namespace App\Providers\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "saving" event.
     * 
     * Auto-sync name from first_name, middle_name, last_name
     * before saving to database
     */
    public function saving(User $user): void
    {
        // Only sync if name parts exist
        if ($user->first_name || $user->last_name) {
            $user->syncNameFromParts();
        }
    }

    /**
     * Handle the User "created" event.
     * 
     * Initialize default activity privacy settings using IDEMPOTENT updateOrCreate.
     * This safely handles cases where privacy might already exist (e.g. retry/re-register).
     */
    public function created(User $user): void
    {
        // Create default privacy settings (all public) - IDEMPOTENT
        $activityTypes = ['portfolio', 'course', 'discussion', 'challenge'];

        foreach ($activityTypes as $type) {
            $user->activityPrivacies()->updateOrCreate(
                ['activity_type' => $type],  // unique key
                ['is_public' => true]        // values to set
            );
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }
    }
}
