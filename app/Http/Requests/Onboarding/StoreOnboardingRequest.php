<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class StoreOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Parse focus_areas if it's a JSON string
        $focusAreas = $this->input('focus_areas');
        if (\is_string($focusAreas)) {
            $this->merge(['focus_areas' => json_decode($focusAreas, true) ?? []]);
        }
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:today'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:5120'],
            'cropped_avatar' => ['nullable', 'string'],
            'focus_areas' => ['nullable', 'array'],
            'focus_areas.*' => ['string', 'max:50'],
            'preferred_role' => ['required', 'in:member,mentor'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'birth_date.required' => 'Birth date is required.',
            'birth_date.before' => 'Birth date must be before today.',
            'preferred_role.required' => 'Please select your preferred role.',
            'profile_picture.image' => 'File must be an image.',
            'profile_picture.mimes' => 'Image format must be JPG, PNG, or GIF.',
            'profile_picture.max' => 'Image size must be under 5MB.',
        ];
    }

    public function hasCroppedAvatar(): bool
    {
        return !empty($this->input('cropped_avatar'));
    }

    public function wantsMentorRole(): bool
    {
        return $this->input('preferred_role') === 'mentor';
    }
}
