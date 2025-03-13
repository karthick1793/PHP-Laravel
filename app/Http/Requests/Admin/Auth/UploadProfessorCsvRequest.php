<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UploadProfessorCsvRequest extends FormRequest
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
            'professor_csv' => ['required', 'file', 'extensions:csv'],
        ];
    }

    public function messages()
    {
        return [
            'professor_csv.required' => 'Please provide the csv',
            'professor_csv.file' => 'Uploaded content is not a file',
            'professor_csv.extensions' => 'Please upload the file with .csv extension',
        ];
    }
}
