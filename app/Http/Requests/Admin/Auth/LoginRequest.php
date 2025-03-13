<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['bail', 'required', 'min:6'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Please enter your email',
            'email.email' => 'Please provide a valid email',
            'password.required' => 'Please enter your password',
            'password.min' => 'Password should be atleast of 6 characters',
        ];
    }
}
