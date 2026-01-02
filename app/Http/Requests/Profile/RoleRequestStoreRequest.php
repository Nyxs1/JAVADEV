<?php

namespace App\Http\Requests\Profile;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RoleRequestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be authenticated and not have a pending request
        $user = Auth::user();
        return $user && !$user->hasPendingRoleRequest();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'to_role_id' => 'required|exists:roles,id',
            'reason' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'to_role_id.required' => 'Role yang dituju wajib dipilih.',
            'to_role_id.exists' => 'Role tidak valid.',
            'reason.max' => 'Alasan maksimal 500 karakter.',
        ];
    }

    /**
     * Get the target role.
     */
    public function getTargetRole(): ?Role
    {
        return Role::find($this->to_role_id);
    }
}
