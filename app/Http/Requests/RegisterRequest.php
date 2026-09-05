<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return !$this->user();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', Rule::in(['student', 'supervisor'])],
            'matriculation_number' => ['required_if:role,student', 'nullable', 'string', 'max:100', 'unique:student_profiles,matriculation_number'],
            'department' => ['required_if:role,student', 'nullable', 'string', 'max:255'],
            'organization_name' => ['required_if:role,supervisor', 'nullable', 'string', 'max:255'],
            'contact_person' => ['required_if:role,supervisor', 'nullable', 'string', 'max:255'],
        ];
    }
}