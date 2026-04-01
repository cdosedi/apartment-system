<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'address' => ['required', 'string', 'max:500'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
        ];
    }

    public function messages()
    {
        return [
            'contact_number.regex' => 'Contact number must contain 10–15 digits (e.g., 09123456789).',
            'emergency_contact_number.regex' => 'Emergency contact number must contain 10–15 digits.',
        ];
    }

    protected function prepareForValidation()
    {
        foreach (['contact_number', 'emergency_contact_number'] as $field) {
            if ($this->filled($field)) {
                $this->merge([$field => preg_replace('/[^0-9]/', '', $this->$field)]);
            }
        }
    }
}
