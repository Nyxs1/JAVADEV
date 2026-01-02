<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreMentorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['required', 'in:mentor,co-mentor,speaker,moderator'],
            'goal_title' => ['nullable', 'string', 'max:255'],
            'target_participants' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
