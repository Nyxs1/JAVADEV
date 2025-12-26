<?php

namespace App\Actions\Profile;

use App\Models\User;
use App\Support\Constants\AvatarConfig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DeleteAvatar
{
    /**
     * Delete user's avatar from storage and database.
     *
     * @param User $user
     * @return void
     */
    public function execute(User $user): void
    {
        if ($user->avatar) {
            Storage::disk(AvatarConfig::STORAGE_DISK)->delete($user->avatar);
            Log::info("[Avatar] Deleted avatar file: {$user->avatar}");
        }

        $user->avatar = null;
        $user->save();

        Log::info("[Avatar] User {$user->id} avatar cleared");
    }
}
