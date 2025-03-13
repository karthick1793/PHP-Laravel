<?php

namespace App\Http\Requests\Admin\Delivery;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInBulkRequest extends FormRequest
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
        $data['type'] = 'required|in:all,multi';
        $data['slot'] = 'required|in:Morning,Evening';
        $data['date'] = 'required|date_format:Y-m-d';
        $data['status'] = ['required', Rule::in('Delivered', 'Not Delivered')];
        if ($this->type == 'multi') {
            $data['booking_ids'] = 'required|array';
            $data['booking_ids.*'] = 'required|exists:professor_milk_booking,id';
        }

        return $data;
    }

    public function messages()
    {
        return [
            'booking_ids.required' => 'No records are selected',
            'booking_ids.*.required' => 'No records are selected',
        ];
    }
}
