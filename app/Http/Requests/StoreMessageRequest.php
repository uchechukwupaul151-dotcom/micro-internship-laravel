<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array { return ['recipient_id' => ['required', 'exists:users,id', 'different:' . $this->user()?->id], 'application_id' => ['nullable', 'exists:applications,id'], 'body' => ['required', 'string', 'max:5000']]; }
}