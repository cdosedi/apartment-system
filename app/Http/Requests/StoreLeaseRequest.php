<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'in:6,12,24,36,48,60'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:100'],
            'room_id' => ['required', 'exists:rooms,id'],
        ];
    }

    public function messages()
    {
        return [
            'room_id.exists' => 'The selected room does not exist.',
            'room_id.available_room' => 'The selected room is not available or has no available beds.',
        ];
    }
}
