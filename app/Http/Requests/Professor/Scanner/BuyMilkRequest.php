<?php

namespace App\Http\Requests\Professor\Scanner;

use Illuminate\Foundation\Http\FormRequest;

class BuyMilkRequest extends FormRequest
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
            'value' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'value.required' => 'Please try again!',
            'value.string' => "Scanned value's format is invalid!",
        ];
    }
}
