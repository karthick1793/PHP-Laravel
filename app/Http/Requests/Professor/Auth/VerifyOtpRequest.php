<?php

namespace App\Http\Requests\Professor\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyOtpRequest extends FormRequest
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
            'otp' => ['required', 'digits:4'],
            'country_code' => ['required'],
            'mobile_number' => ['required', 'numeric', 'digits:10', Rule::exists('professors', 'mobile_number')->where('country_code', $this->country_code)],
        ];
    }

    public function messages()
    {
        return [
            'otp.required' => 'Please enter the otp',
            'otp.digits' => 'Otp should only have 4 digits',
            'country_code.required' => 'Please select your country code',
            'mobile_number.required' => 'Please provide your mobile number',
            'mobile_number.numeric' => 'Given mobile number is invalid',
            'mobile_number.digits' => 'Mobile number should only have 10 digits',
            'mobile_number.exists' => 'Acccount is not registered for this number',
        ];
    }
}
