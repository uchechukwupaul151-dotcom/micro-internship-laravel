<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyInternshipRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'student'; }

    public function rules(): array
    {
        return ['cover_note' => ['nullable', 'string', 'max:5000']];
    }
}