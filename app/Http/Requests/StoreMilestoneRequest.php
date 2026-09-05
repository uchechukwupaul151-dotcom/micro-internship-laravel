<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'supervisor';
    }

    public function rules(): array
    {
        return [
            'sequence' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'deliverable' => ['required', 'string'],
            'criteria' => ['required', 'string'],
            'due_date' => ['required', 'date'],
        ];
    }
}