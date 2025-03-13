<?php

namespace App\Http\Requests\Admin\ManageToken;

use Illuminate\Foundation\Http\FormRequest;

class ListProfessorRequest extends FormRequest
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
            'page' => 'required',
            'per_page' => 'required|min:1',
            'search_value' => 'nullable',
            'quarter_token' => 'nullable|exists:quarters,token',
        ];
    }

    public function messages()
    {
        return [
            'page.required' => 'Page count is not provided',
            'per_page.required' => 'Per page count is not provided',
            'per_page.min' => 'Per page value should not be less than one',
            'quarter_token.exists' => 'Quarters not found!',
        ];
    }
}
