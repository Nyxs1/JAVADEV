<?php

namespace App\Services\Profile\Actions;

use App\Models\User;

class UpdateProfile
{
    /**
     * Update user profile information.
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function execute(User $user, array $data): void
    {
        $user->first_name = $data['first_name'];
        $user->middle_name = $data['middle_name'] ?? null;
        $user->last_name = $data['last_name'];
        $user->bio = $data['bio'] ?? null;

        // Handle avatar focus data (pan/zoom)
        if (isset($data['avatar_zoom']) || isset($data['avatar_pan_x']) || isset($data['avatar_pan_y'])) {
            $user->avatar_focus = [
                'zoom' => (float) ($data['avatar_zoom'] ?? 1.0),
                'panX' => (float) ($data['avatar_pan_x'] ?? 0),
                'panY' => (float) ($data['avatar_pan_y'] ?? 0),
                // Profile settings editor is 1:1 (circle) focus space.
                'frame' => 'circle',
            ];
        }

        $user->syncNameFromParts();
        $user->save();
    }
}
