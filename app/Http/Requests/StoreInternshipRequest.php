<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInternshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'supervisor';
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'required_skills' => ['required', 'array', 'min:1'],
            'required_skills.*' => ['required', 'string', 'max:100'],
            'location_type' => ['required', Rule::in(['remote', 'hybrid', 'on-site'])],
            'duration_days' => ['required', 'integer', 'between:5,30'],
            'capacity' => ['required', 'integer', 'between:1,100'],
            'stipend' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}