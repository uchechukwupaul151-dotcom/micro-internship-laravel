<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleInterviewRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->role === 'supervisor'; }
    public function rules(): array { return ['scheduled_at' => ['required', 'date', 'after:now'], 'duration_minutes' => ['required', 'integer', 'between:15,180'], 'meeting_url' => ['nullable', 'url', 'max:500']]; }
}