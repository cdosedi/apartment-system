<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'contact_number' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/'],
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'contact_number.regex' => 'Contact number must contain only digits (10–15 digits).',
    //     ];
    // }
}
