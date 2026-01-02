<?php

namespace App\Services\Profile\Actions;

use App\Models\User;
use App\Services\Shared\Constants\AvatarConfig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadAvatar
{
    /**
     * Upload avatar from base64 cropped image data.
     *
     * @param User $user
     * @param string $base64Data
     * @return array{success: bool, message?: string, path?: string}
     */
    public function execute(User $user, string $base64Data): array
    {
        $parseResult = $this->parseBase64Image($base64Data);

        if (!$parseResult['success']) {
            return ['success' => false, 'message' => $parseResult['message']];
        }

        $validationResult = $this->validateImage($parseResult['type'], $parseResult['data']);

        if (!$validationResult['success']) {
            return ['success' => false, 'message' => $validationResult['message']];
        }

        return $this->saveImage($user, $parseResult['data'], $parseResult['type']);
    }

    private function parseBase64Image(string $base64Data): array
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            return ['success' => false, 'message' => 'Invalid image format.'];
        }

        $imageData = substr($base64Data, strpos($base64Data, ',') + 1);
        $decodedData = base64_decode($imageData);

        if ($decodedData === false) {
            return ['success' => false, 'message' => 'Failed to process image.'];
        }

        return [
            'success' => true,
            'type' => strtolower($type[1]),
            'data' => $decodedData,
        ];
    }

    private function validateImage(string $type, string $data): array
    {
        if (!\in_array($type, AvatarConfig::ALLOWED_TYPES, true)) {
            return ['success' => false, 'message' => 'Invalid image format.'];
        }

        if (\strlen($data) > AvatarConfig::MAX_SIZE_BYTES) {
            return ['success' => false, 'message' => 'Image size must be under 5MB.'];
        }

        return ['success' => true];
    }

    private function saveImage(User $user, string $imageData, string $fileType): array
    {
        $this->deleteExistingAvatar($user);

        $filename = AvatarConfig::STORAGE_PATH . "/{$user->id}_" . time() . ".{$fileType}";

        Storage::disk(AvatarConfig::STORAGE_DISK)->put($filename, $imageData);

        $user->avatar = $filename;
        $user->save();

        Log::info("[Avatar] Uploaded new avatar for user {$user->id}: {$filename}");

        return ['success' => true, 'path' => $filename];
    }

    private function deleteExistingAvatar(User $user): void
    {
        if ($user->avatar) {
            Storage::disk(AvatarConfig::STORAGE_DISK)->delete($user->avatar);
        }
    }
}
