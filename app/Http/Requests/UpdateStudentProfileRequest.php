<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentProfileRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'student'; }

    public function rules(): array
    {
        return [
            'department' => ['required', 'string', 'max:255'],
            'cgpa' => ['nullable', 'numeric', 'between:0,5'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'technical_skills' => ['nullable', 'array'],
            'technical_skills.*' => ['string', 'max:100'],
            'availability' => ['nullable', 'array'],
            'portfolio_links' => ['nullable', 'array'],
            'portfolio_links.*' => ['url', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }
}