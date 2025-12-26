<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequirementsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by Gate in controller
    }

    public function rules(): array
    {
        return [
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:255'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.language' => ['nullable', 'array'],
            'tech_stack.language.*' => ['string', 'max:255'],
            'tech_stack.framework' => ['nullable', 'array'],
            'tech_stack.framework.*' => ['string', 'max:255'],
            'tech_stack.database' => ['nullable', 'array'],
            'tech_stack.database.*' => ['string', 'max:255'],
            'tech_stack.tools' => ['nullable', 'array'],
            'tech_stack.tools.*' => ['string', 'max:255'],
            'accounts' => ['nullable', 'array'],
            'accounts.*' => ['string', 'max:255'],
            'checklist' => ['nullable', 'array'],
            'checklist.*' => ['string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get structured requirements data for JSON storage.
     */
    public function getRequirementsData(): array
    {
        $data = [];

        if ($this->filled('skills')) {
            $data['skills'] = array_filter($this->input('skills'));
        }

        if ($this->filled('tech_stack')) {
            $techStack = [];
            foreach (['language', 'framework', 'database', 'tools'] as $key) {
                if ($this->filled("tech_stack.{$key}")) {
                    $techStack[$key] = array_filter($this->input("tech_stack.{$key}"));
                }
            }
            if (!empty($techStack)) {
                $data['tech_stack'] = $techStack;
            }
        }

        if ($this->filled('accounts')) {
            $data['accounts'] = array_filter($this->input('accounts'));
        }

        if ($this->filled('checklist')) {
            $data['checklist'] = array_filter($this->input('checklist'));
        }

        if ($this->filled('notes')) {
            $data['notes'] = $this->input('notes');
        }

        return $data;
    }
}
