<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'cropped_avatar' => ['nullable', 'string'],
            'remove_avatar' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.max' => 'First name must be at most 100 characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.max' => 'Last name must be at most 100 characters.',
            'bio.max' => 'Bio must be at most 1000 characters.',
        ];
    }

    public function shouldRemoveAvatar(): bool
    {
        return $this->input('remove_avatar') === '1';
    }

    public function hasCroppedAvatar(): bool
    {
        return !empty($this->input('cropped_avatar'));
    }
}
