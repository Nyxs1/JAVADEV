<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Portfolio;
use App\Models\UserCourse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EvidenceStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verify ownership of the item being evidenced
        $item = $this->getItem();
        return $item && $item->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'item_type' => 'required|in:portfolio,user_course',
            'item_id' => 'required|integer',
            'type' => 'required|in:github,link,demo,pdf',
            'label' => 'nullable|string|max:100',
            'value' => 'required|url|max:500',
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'item_type.required' => 'Tipe item wajib diisi.',
            'item_type.in' => 'Tipe item tidak valid.',
            'item_id.required' => 'ID item wajib diisi.',
            'type.required' => 'Tipe evidence wajib diisi.',
            'type.in' => 'Tipe evidence tidak valid.',
            'value.required' => 'URL evidence wajib diisi.',
            'value.url' => 'URL evidence tidak valid.',
            'value.max' => 'URL maksimal 500 karakter.',
            'label.max' => 'Label maksimal 100 karakter.',
        ];
    }

    /**
     * Get the item being evidenced.
     */
    public function getItem(): mixed
    {
        return match ($this->item_type) {
            'portfolio' => Portfolio::find($this->item_id),
            'user_course' => UserCourse::find($this->item_id),
            default => null,
        };
    }
}
