<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenant = $this->route('tenant');

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('tenants')->ignore($tenant->id),
            ],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'address' => ['required', 'string', 'max:500'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
        ];
    }

    public function messages()
    {
        return [
            'contact_number.regex' => 'Contact number must contain only digits (10–15 digits).',
            'emergency_contact_number.regex' => 'Emergency contact number must contain only digits (10–15 digits).',
        ];
    }
}
