<?php

namespace App\Http\Requests\Profile;

use App\Enums\SkillLevel;
use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tech_name' => ['required', 'string', 'max:100'],
            'level' => ['required', 'integer', 'min:1', 'max:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'tech_name.required' => 'Please enter a skill name.',
            'tech_name.max' => 'Skill name must be 100 characters or less.',
            'level.required' => 'Please select a skill level.',
            'level.min' => 'Invalid skill level.',
            'level.max' => 'Invalid skill level.',
        ];
    }
}
