<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMentorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'in:mentor,co-mentor,speaker,moderator'],
            'goal_title' => ['nullable', 'string', 'max:255'],
            'target_participants' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
