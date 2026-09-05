<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id', 'matriculation_number', 'department', 'cgpa', 'bank_name', 'account_name', 'account_number',
        'technical_skills', 'availability', 'portfolio_links',
    ];

    protected function casts(): array
    {
        return [
            'cgpa' => 'decimal:2',
            'technical_skills' => 'array',
            'availability' => 'array',
            'portfolio_links' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}