<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:workshop,seminar,mentoring'],
            'level' => ['required', 'integer', 'min:1', 'max:4'],
            'mode' => ['required', 'in:online,onsite,hybrid'],
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'meeting_url' => ['nullable', 'url', 'max:500'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
        ];
    }
}
