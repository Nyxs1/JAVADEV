<?php

namespace App\Policies;

use App\Models\ItemEvidence;
use App\Models\User;

class ItemEvidencePolicy
{
    /**
     * Determine if the user can view any evidences.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the evidence.
     */
    public function view(User $user, ItemEvidence $evidence): bool
    {
        // Public evidence can be viewed by anyone
        if ($evidence->is_public) {
            return true;
        }

        // Private evidence can only be viewed by owner
        return $user->id === $evidence->user_id;
    }

    /**
     * Determine if the user can create evidence.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the evidence.
     */
    public function update(User $user, ItemEvidence $evidence): bool
    {
        return $user->id === $evidence->user_id;
    }

    /**
     * Determine if the user can delete the evidence.
     */
    public function delete(User $user, ItemEvidence $evidence): bool
    {
        return $user->id === $evidence->user_id;
    }
}
