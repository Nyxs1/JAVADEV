<?php

namespace App\Services\Profile;

use App\Models\User;
use App\Actions\Profile\UpdateProfile;
use App\Actions\Profile\UploadAvatar;
use App\Actions\Profile\DeleteAvatar;
use App\Actions\Profile\ChangePassword;

class ProfileService
{
    public function __construct(
        private UpdateProfile $updateProfile,
        private UploadAvatar $uploadAvatar,
        private DeleteAvatar $deleteAvatar,
        private ChangePassword $changePassword
    ) {
    }

    /**
     * Update user profile with optional avatar handling.
     *
     * @param User $user
     * @param array $data Validated profile data
     * @param bool $removeAvatar Whether to remove avatar
     * @param string|null $croppedAvatar Base64 cropped avatar data
     * @return array{success: bool, message?: string, avatar_changed: bool, avatar_url: ?string, avatar_version: int}
     */
    public function updateProfile(
        User $user,
        array $data,
        bool $removeAvatar = false,
        ?string $croppedAvatar = null
    ): array {
        $avatarChanged = false;

        // Handle avatar removal (priority)
        if ($removeAvatar) {
            $this->deleteAvatar->execute($user);
            $avatarChanged = true;
        }
        // Handle avatar upload
        elseif ($croppedAvatar) {
            $result = $this->uploadAvatar->execute($user, $croppedAvatar);
            if (!$result['success']) {
                return [
                    'success' => false,
                    'message' => $result['message'],
                    'avatar_changed' => false,
                    'avatar_url' => null,
                    'avatar_version' => 0,
                ];
            }
            $avatarChanged = true;
        }

        // Update profile data
        $this->updateProfile->execute($user, $data);

        // Refresh user to get updated timestamp
        $user->refresh();

        $avatarVersion = $user->updated_at->timestamp;
        $avatarUrl = $user->avatar
            ? asset('storage/' . $user->avatar) . '?v=' . $avatarVersion
            : null;

        return [
            'success' => true,
            'avatar_changed' => $avatarChanged,
            'avatar_url' => $avatarUrl,
            'avatar_version' => $avatarVersion,
        ];
    }

    /**
     * Change user password.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return array{success: bool, message: string}
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        return $this->changePassword->execute($user, $currentPassword, $newPassword);
    }
}
