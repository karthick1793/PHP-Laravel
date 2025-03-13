<?php

namespace App\Http\Requests\Admin\Activity;

use Illuminate\Foundation\Http\FormRequest;

class DownloadFileRequest extends FormRequest
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
            'type' => 'required|in:csv,pdf,xlsx',
            'search_value' => 'nullable',
            'from_date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'to_date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'quarter_token' => 'nullable|exists:quarters,token',
            'professor_token' => 'nullable|exists:professors,token',
        ];
    }

    public function messages()
    {
        return [
            'quarter_token.exists' => 'Quarters not found!',
            'professor_token.exists' => 'Professor not found!',
        ];
    }
}
