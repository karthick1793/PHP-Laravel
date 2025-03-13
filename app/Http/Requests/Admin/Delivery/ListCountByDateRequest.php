<?php

namespace App\Http\Requests\Admin\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class ListCountByDateRequest extends FormRequest
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
            'page' => 'required|numeric',
            'per_page' => 'required|numeric',
            'search_value' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'page' => 'required',
            'per_page' => 'required',
        ];
    }
}
