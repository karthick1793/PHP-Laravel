<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyMailOtpRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:admin_users,email'],
            'otp' => ['required', 'digits:4'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Please provide your email id',
            'email.email' => 'Given email is invalid',
            'email.exists' => 'Account with this email does not exist!',
            'otp.required' => 'Please enter the otp',
            'otp.digits' => 'Otp should only have 4 digits',
        ];
    }
}
