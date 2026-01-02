<?php

namespace App\Services\Onboarding;

use App\Models\User;
use App\Models\RoleRequest;
use App\Services\Profile\Actions\UploadAvatar;
use App\Services\Shared\Constants\AvatarConfig;
use App\Services\Shared\Constants\RoleId;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
     * IMPORTANT:
     * - Onboarding editor uses HORIZONTAL banner frame (3:1) with a CENTER circle overlay
     * - So avatar_focus MUST be stored as frame='banner' + store frame metadata
     * - Navbar circle will convert banner-focus -> circle on render (in avatar component)
     */
    public function execute(
        User $user,
        array $data,
        ?string $croppedAvatar = null,
        ?UploadedFile $profilePicture = null,
        bool $wantsMentor = false
    ): void {
        $avatarPath = $user->avatar;
        $croppedAvatarSaved = false;

        // PRIORITY 1: Use cropped base64 (generated from onboarding canvas)
        if ($croppedAvatar && !empty($croppedAvatar)) {
            $result = $this->uploadAvatar->execute($user, $croppedAvatar);

            if ($result['success']) {
                $avatarPath = $result['path'];
                $croppedAvatarSaved = true;
                Log::info("[Onboarding] Saved cropped avatar via UploadAvatar for user {$user->id}: {$avatarPath}");
            } else {
                Log::warning("[Onboarding] Cropped avatar upload failed: " . ($result['message'] ?? 'Unknown error'));
            }
        }

        // PRIORITY 2: Fallback to original uploaded file (only if no crop was saved)
        if (!$croppedAvatarSaved && $profilePicture) {
            if ($user->avatar) {
                Storage::disk(AvatarConfig::STORAGE_DISK)->delete($user->avatar);
            }

            $extension = $profilePicture->getClientOriginalExtension() ?: 'jpg';
            $filename = AvatarConfig::STORAGE_PATH . "/{$user->id}_" . time() . ".{$extension}";

            Storage::disk(AvatarConfig::STORAGE_DISK)->put(
                $filename,
                file_get_contents($profilePicture->getRealPath())
            );

            $avatarPath = $filename;

            Log::info("[Onboarding] Saved original file for user {$user->id}: {$avatarPath}");
        }

        // Debug incoming data (you already log this; keep it)
        Log::info("[Onboarding] Incoming avatar data:", [
            'avatar_zoom' => $data['avatar_zoom'] ?? 'NOT SET',
            'avatar_pan_x' => $data['avatar_pan_x'] ?? 'NOT SET',
            'avatar_pan_y' => $data['avatar_pan_y'] ?? 'NOT SET',
        ]);

        // ✅ FIXED: Store as BANNER coordinate space
        // Since onboarding now saves a 768x256 crop of the visible frame,
        // we store frame='banner' and reset zoom/pan (WYSWYG)
        $avatarFocus = $user->avatar_focus ?? [];

        if ($croppedAvatarSaved) {
            $avatarFocus = [
                'zoom' => 1.0,
                'panX' => 0.0,
                'panY' => 0.0,
                'frame' => 'banner',
            ];
            Log::info("[Onboarding] Saving avatar_focus as WYSWYG banner:", $avatarFocus);
        } elseif (isset($data['avatar_zoom']) || isset($data['avatar_pan_x']) || isset($data['avatar_pan_y'])) {
            // Fallback for non-cropped path (if any)
            $avatarFocus = [
                'zoom' => (float) ($data['avatar_zoom'] ?? 1.0),
                'panX' => (float) ($data['avatar_pan_x'] ?? 0),
                'panY' => (float) ($data['avatar_pan_y'] ?? 0),
                'frame' => 'banner',
            ];
            Log::info("[Onboarding] Saving avatar_focus from inputs (banner space):", $avatarFocus);
        } else {
            Log::warning("[Onboarding] No avatar focus data in request!");
        }

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

        if ($wantsMentor) {
            RoleRequest::create([
                'user_id' => $user->id,
                'from_role_id' => $user->role_id,
                'to_role_id' => RoleId::MENTOR,
                'reason' => 'Onboarding: User selected mentor role',
                'status' => 'pending',
            ]);
        }
    }
}
