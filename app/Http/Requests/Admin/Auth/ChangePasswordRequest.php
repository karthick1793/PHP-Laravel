<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'password' => ['required', 'min:6'],
            'confirm_password' => ['required', 'same:password'],
            'email' => ['required', 'email', 'exists:admin_users,email'],
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Please enter your new password',
            'password.min' => 'Password should have atleast 6 characters',
            'confirm_password.same' => 'Confirm password should be the same as password',
            'email.required' => 'Please provide your email id',
            'email.email' => 'Given email is invalid',
            'email.exists' => 'Account with this email does not exist!',
        ];
    }
}
