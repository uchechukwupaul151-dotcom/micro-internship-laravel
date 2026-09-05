<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitDeliverableRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'student'; }

    public function rules(): array
    {
        return [
            'file' => ['nullable', 'file', 'max:20480'],
            'repository_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->file('file') && !$this->filled('repository_url')) {
                $validator->errors()->add('file', 'Provide a file or repository URL.');
            }
        });
    }
}