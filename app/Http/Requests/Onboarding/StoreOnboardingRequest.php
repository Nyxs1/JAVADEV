<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['required', 'string'],
            'birth_date' => ['required', 'date'],

            'profile_picture' => ['nullable', 'image'],

            // REQUIRED when profile_picture is uploaded (frontend generates this from canvas)
            'cropped_avatar' => ['required_with:profile_picture', 'nullable', 'string'],

            'avatar_zoom' => ['nullable', 'numeric'],
            'avatar_pan_x' => ['nullable', 'numeric'],
            'avatar_pan_y' => ['nullable', 'numeric'],
        ];
    }

    public function hasCroppedAvatar(): bool
    {
        $v = $this->input('cropped_avatar');

        return is_string($v)
            && str_starts_with($v, 'data:image/')
            && strlen($v) > 100;
    }

    public function wantsMentorRole(): bool
    {
        return (bool) $this->input('wants_mentor', false);
    }
}
