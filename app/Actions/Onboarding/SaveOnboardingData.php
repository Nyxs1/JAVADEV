<?php

namespace App\Actions\Onboarding;

use App\Models\User;
use App\Models\RoleRequest;
use App\Actions\Profile\UploadAvatar;
use App\Support\Constants\AvatarConfig;
use App\Support\Constants\RoleId;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class SaveOnboardingData
{
    public function __construct(
        private UploadAvatar $uploadAvatar
    ) {
    }

    /**
     * Save onboarding data for user.
     *
     * @param User $user
     * @param array $data Validated onboarding data
     * @param string|null $croppedAvatar Base64 cropped avatar (for navbar circle) - NOT USED for banner
     * @param UploadedFile|null $profilePicture ORIGINAL uploaded file (for banner)
     * @param bool $wantsMentor Whether user wants mentor role
     * @return void
     */
    public function execute(
        User $user,
        array $data,
        ?string $croppedAvatar = null,
        ?UploadedFile $profilePicture = null,
        bool $wantsMentor = false
    ): void {
        $avatarPath = $user->avatar;

        // IMPORTANT: Save ORIGINAL file for banner display (NOT cropped)
        // The cropped avatar is only used for navbar circle display
        if ($profilePicture) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk(AvatarConfig::STORAGE_DISK)->delete($user->avatar);
            }
            // Save original file - this is used for BANNER display
            $avatarPath = $profilePicture->store(AvatarConfig::STORAGE_PATH, AvatarConfig::STORAGE_DISK);
        }

        // OPTIONAL: Save avatar_focus data for display transforms
        // This allows dynamic cropping on display instead of pre-cropping
        $avatarFocus = $user->avatar_focus ?? [];
        if (isset($data['avatar_zoom']) || isset($data['avatar_pan_x']) || isset($data['avatar_pan_y'])) {
            $avatarFocus = [
                'zoom' => $data['avatar_zoom'] ?? 1.0,
                'panXNorm' => $data['avatar_pan_x'] ?? 0,
                'panYNorm' => $data['avatar_pan_y'] ?? 0,
            ];
        }

        // Update user personal info
        $user->update([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'birth_date' => $data['birth_date'],
            'avatar' => $avatarPath,
            'avatar_focus' => $avatarFocus,
        ]);

        $user->syncNameFromParts();
        $user->save();

        // Handle mentor role request
        if ($wantsMentor) {
            $this->createMentorRoleRequest($user);
        }
    }

    private function createMentorRoleRequest(User $user): void
    {
        RoleRequest::create([
            'user_id' => $user->id,
            'from_role_id' => $user->role_id,
            'to_role_id' => RoleId::MENTOR,
            'reason' => 'Onboarding: User selected mentor role',
            'status' => 'pending',
        ]);
    }
}
