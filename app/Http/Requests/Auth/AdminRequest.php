<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
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
    public function rules()
    {
        $method = $this->route()->getActionMethod();
        if ($method == 'login') {
            return [
                'email' => ['required', 'email'],
                'password' => ['required', 'min:6'],
            ];
        }
        if ($method == 'addUser') {
            return [
                'email' => ['required', 'email'],
                'name' => ['required'],
                'password' => ['required', 'min:6'],
            ];
        }

        if ($method == 'forgotPassword') {
            return [
                'email' => ['required', 'email', Rule::exists('admin_users', 'email')],
            ];
        }
        if ($method == 'verifyOtp') {
            return [
                'email' => ['required', 'email', Rule::exists('admin_users', 'email')],
                'otp' => ['required', 'digits:4'],

            ];
        }
        if ($method == 'resendOtp') {
            return [
                'email' => ['required', 'email', Rule::exists('admin_users', 'email')],
            ];
        }

        if ($method == 'changePassword') {
            return [
                'password' => ['required', 'string', 'min6', 'confirmed'],
                'confirm_password' => ['required', 'string', 'min:6', 'same:password'],
                'email' => ['required', 'email', Rule::exists('admin_users', 'email')],
            ];
        }

        return [];
    }

    public function messages()
    {

        $method = $this->route()->getActionMethod();
        if ($method == 'login') {
            return [
                'email.required' => 'Please provide email address!',
                'password.required' => 'Please provide password!',
            ];

        }

        if ($method == 'addUser') {
            return [
                'email.required' => 'Please provide email address!',
                'password.required' => 'Please provide password!',
                'name.required' => 'Please provide Name!',

            ];

        }

        if ($method == 'forgotPassword' || $method == 'resendOtp') {
            return [
                'email.required' => 'Please provide email address!',
                'email.exists' => 'Invalid email id',
            ];
        }
        if ($method == 'verifyOtp') {
            return [
                'email.required' => 'Please provide email address!',
                'email.exists' => 'Invalid email id',
                'otp.digits' => 'OTP must be in 4 digits',
                // 'otp.exists' => 'OTP Invalid',
            ];
        }
        if ($method == 'changePassword') {
            return [
                'email.required' => 'Please provide email address!',
                'password.required' => 'Please provide password!',
                'password.min' => 'Please must be in 4 character !',
            ];

        }

    }
}
