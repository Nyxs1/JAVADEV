<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()
            ],
            'verification_code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.unique' => 'Username is already taken.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'verification_code.required' => 'Verification code is required.',
            'verification_code.size' => 'Verification code must be 6 digits.',
        ];
    }
}
