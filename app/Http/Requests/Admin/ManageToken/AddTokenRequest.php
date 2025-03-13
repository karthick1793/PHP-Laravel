<?php

namespace App\Http\Requests\Admin\ManageToken;

use Illuminate\Foundation\Http\FormRequest;

class AddTokenRequest extends FormRequest
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
            'professor_token' => 'required|exists:professors,token',
            'tokens' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'professor_token.required' => 'No professor is selected',
            'professor_token.exists' => 'Professor not found!',
            'tokens.required' => 'Please enter the token count!',
            'tokens.numeric' => 'Token amount should be a integer!',
            'tokens.min' => 'Token amount should be greater than zero!',
        ];
    }
}
