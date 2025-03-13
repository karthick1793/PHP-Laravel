<?php

namespace App\Http\Requests\Admin\ManageToken;

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
            'quarter_token' => 'nullable|exists:quarters,token',
        ];
    }

    public function messages()
    {
        return [
            'quarter_token.exists' => 'Quarters not found!',
        ];
    }
}
