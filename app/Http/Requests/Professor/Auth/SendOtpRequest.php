<?php

namespace App\Http\Requests\Professor\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendOtpRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

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
            // 'country_code' => ['required'],
            'mobile_number' => ['bail', 'required', 'numeric', 'digits:10', Rule::exists('professors', 'mobile_number')],
        ];
    }

    public function messages()
    {
        return [
            'country_code.required' => 'Please select your country code',
            'mobile_number.required' => 'Please provide your mobile number',
            'mobile_number.numeric' => 'Given mobile number is invalid',
            'mobile_number.digits' => 'Mobile number should only have 10 digits',
            'mobile_number.exists' => 'Acccount is not registered for this number',
        ];
    }
}
