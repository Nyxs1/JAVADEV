<?php

namespace App\Services\Profile\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UpdateAvatarFocus
{
    /**
     * Update avatar focal point (x, y, zoom).
     *
     * @param User $user
     * @param array $focus ['x' => 0-1, 'y' => 0-1, 'zoom' => 1-3]
     * @return void
     */
    public function execute(User $user, array $focus): void
    {
        $user->avatar_focus = [
            'x' => max(0, min(1, floatval($focus['x'] ?? 0.5))),
            'y' => max(0, min(1, floatval($focus['y'] ?? 0.5))),
            'zoom' => max(1, min(3, floatval($focus['zoom'] ?? 1.0))),
        ];
        $user->save();

        Log::info("[Avatar] Updated focus for user {$user->id}", $user->avatar_focus);
    }
}
