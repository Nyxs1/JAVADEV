<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_\-+=\[\]\\\/`~;\']/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => "We can't find a user with that email address.",
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least 1 uppercase letter, 1 number, and 1 special character.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
