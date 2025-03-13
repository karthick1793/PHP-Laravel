<?php

namespace App\Http\Requests\Professor\Activity;

use Illuminate\Foundation\Http\FormRequest;

class ListTransactionRequest extends FormRequest
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
            'page' => 'bail|required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'page.required' => 'Page count is not given!',
            'page.numeric' => 'Page count should be a integer',
            'page.min' => 'Page count should not be less than one',
        ];
    }
}
