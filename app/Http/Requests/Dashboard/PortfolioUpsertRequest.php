<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Portfolio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class PortfolioUpsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // If updating, verify ownership
        if ($this->filled('portfolio_id')) {
            $portfolio = Portfolio::find($this->portfolio_id);
            return $portfolio && $portfolio->user_id === Auth::id();
        }

        // Creating new portfolio - user must be authenticated
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'portfolio_id' => 'nullable|integer|exists:portfolios,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'readme_md' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'screenshots' => 'nullable|array|max:10',
            'screenshots.*' => 'image|max:4096',
            'source_course_id' => 'nullable|integer|exists:user_courses,id',
            'publish_now' => 'nullable|boolean',
            'agree_publish' => 'nullable|accepted',
            // Evidence fields (inline form)
            'new_evidences' => 'nullable|array|max:10',
            'new_evidences.*.type' => 'required_with:new_evidences|in:github,link,demo,pdf',
            'new_evidences.*.label' => 'nullable|string|max:100',
            'new_evidences.*.value' => 'required_with:new_evidences|url|max:500',
        ];

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // If publish_now is set, require agree_publish
            if ($this->boolean('publish_now') && !$this->boolean('agree_publish')) {
                $validator->errors()->add(
                    'agree_publish',
                    'Kamu harus menyetujui ketentuan sebelum publish.'
                );
            }
        });
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul portfolio wajib diisi.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'cover.image' => 'Cover harus berupa gambar.',
            'cover.max' => 'Ukuran cover maksimal 2MB.',
            'screenshots.max' => 'Maksimal 10 screenshot.',
            'screenshots.*.image' => 'Semua screenshot harus berupa gambar.',
            'screenshots.*.max' => 'Ukuran screenshot maksimal 4MB.',
            'agree_publish.accepted' => 'Kamu harus menyetujui ketentuan sebelum publish.',
            'new_evidences.*.type.in' => 'Tipe evidence tidak valid.',
            'new_evidences.*.value.url' => 'URL evidence tidak valid.',
        ];
    }

    /**
     * Get the existing portfolio if updating.
     */
    public function getPortfolio(): ?Portfolio
    {
        if ($this->filled('portfolio_id')) {
            return Portfolio::find($this->portfolio_id);
        }
        return null;
    }
}
