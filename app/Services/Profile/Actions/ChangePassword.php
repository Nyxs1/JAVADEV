<?php

namespace App\Services\Profile\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePassword
{
    /**
     * Change user password.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return array{success: bool, message: string}
     */
    public function execute(User $user, string $currentPassword, string $newPassword): array
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect.',
            ];
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return [
            'success' => true,
            'message' => 'Password changed successfully!',
        ];
    }
}
