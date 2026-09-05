<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupervisorProfileRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'supervisor'; }

    public function rules(): array
    {
        return [
            'organization_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company_profile' => ['nullable', 'string', 'max:5000'],
            'website' => ['nullable', 'url', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }
}