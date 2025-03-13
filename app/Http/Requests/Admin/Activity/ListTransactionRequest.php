<?php

namespace App\Http\Requests\Admin\Activity;

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
            'page' => 'required',
            'per_page' => 'required',
            'from_date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'to_date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'search_value' => 'nullable',
            'quarter_token' => 'nullable|exists:quarters,token',
            'professor_token' => 'nullable|exists:professors,token',
        ];
    }
}
