<?php

namespace App\Actions\Profile;

use App\Models\User;

class UpdateProfile
{
    /**
     * Update user profile information.
     *
     * @param User $user
     * @param array{first_name: string, middle_name: ?string, last_name: string, bio: ?string} $data
     * @return void
     */
    public function execute(User $user, array $data): void
    {
        $user->first_name = $data['first_name'];
        $user->middle_name = $data['middle_name'] ?? null;
        $user->last_name = $data['last_name'];
        $user->bio = $data['bio'] ?? null;

        $user->syncNameFromParts();
        $user->save();
    }
}
