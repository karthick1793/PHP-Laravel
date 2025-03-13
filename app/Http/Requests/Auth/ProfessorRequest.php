<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfessorRequest extends FormRequest
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

        if ($method == 'csvAddUser') {
            return [
                'professor_csv' => ['required', 'file', 'extensions:csv', 'max:2048'], // File validation
                // 'selected_tokens' => [''],
            ];
        }

        if ($method == 'professorList') {
            return [
                'type' => ['required', 'in:0,1'],
                'page' => ['required', 'not_in:0'], // File validation
            ];
        }
        if ($method == 'activityList') {
            return [
                'type' => ['required', 'in:0,1'],
                'page' => ['required', 'not_in:0'], // File validation
            ];
        }
        if ($method == 'addCoin') {
            return [
                'coin_count' => ['required', 'not_in:0'],
                'professor_token' => ['required', Rule::exists('professors', 'token')],
            ];
        }

        if ($method == 'sendMoblieOtp') {
            return [
                'country_code' => ['required', 'not_in:0'],
                'mobile_number' => ['required', 'numeric', 'digits:10', Rule::exists('professors', 'mobile_number')],
            ];
        }
        if ($method == 'verifyMobileOtp') {
            return [
                'otp' => ['required', 'numeric', 'digits:4', 'not_in:0'],
                'country_code' => ['required', 'not_in:0'],
                'mobile_number' => ['required', 'numeric', 'digits:10', Rule::exists('professors', 'mobile_number')],
            ];
        }
        if ($method == 'homeList') {
            return [
                // 'professor_token' => ['required']
                'professor_token' => ['required', Rule::exists('professors', 'token')],

            ];
        }

        return [];
    }

    public function messages()
    {

        $method = $this->route()->getActionMethod();

        if ($method == 'csvAddUser') {
            return [
                'upload_professor_csv.required' => 'Please provide a CSV file!',
                'upload_professor_csv.extensions' => 'The uploaded file must be a CSV.',
            ];
        }

        if ($method == 'activityList') {
            return [
                'type.required' => 'Please Provide type', // File validation
                'type.in' => 'Please Provide 0 or 1', // File validation
                'page.not_in' => 'Please Provide page without zero',
                'page.required' => 'Please Provide page number',
            ];
        }
        if ($method == 'professorList') {
            return [
                'type.required' => 'Please Provide type', // File validation
                'type.in' => 'Please Provide 0 or 1', // File validation
                'page.not_in' => 'Please Provide page without zero',
                'page.required' => 'Please Provide page number',
            ];
        }

        if ($method == 'addCoin') {
            return [
                'professor_token.required' => 'Please provide professor token!',
                'professor_token.exists' => 'Invalid professor token',
                'coin_count.required' => 'Please provide coins!',
                'coin_count.not_in' => 'Please Provide without zero ',
            ];
        }
        if ($method == 'homeList') {
            return [
                'professor_token.required' => 'Please provide professor token!',
                'professor_token.exists' => 'Invalid professor token',

            ];
        }
        if ($method == 'sendMoblieOtp') {

            return [
                'country_code.required' => 'Please provide country code!',
                'country_code.not_in' => 'Please Provide country code without zero',
                'mobile_number.required' => 'Please provide mobilenumber!',
                'mobile_number.exists' => 'Invalid professor mobile number',
                'mobile_number.numeric' => 'Provide mobile number is only numeric!',
                'mobile_number.digits' => 'Provide mobile number is only digits 10 ',
            ];
        }
        if ($method == 'verifyMobileOtp') {

            return [
                'country_code.required' => 'Please provide country code!',
                'country_code.not_in' => 'Please Provide country code without zero',
                'mobile_number.required' => 'Please provide mobilenumber!',
                'mobile_number.exists' => 'Invalid professor mobile number',
                'mobile_number.numeric' => 'Provide mobile number is only numeric!',
                'mobile_number.digits' => 'Provide mobile number is only digits 10 ',
                'otp.required' => 'Please provide otp ',
                'otp.numeric' => 'Provide otp is only numeric!',
                'otp.digits' => 'Provide otp is only 4 digits ',
                'otp.not_in' => 'Please Provide otp without zero',
            ];
        }

    }
}
