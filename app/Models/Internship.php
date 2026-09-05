<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Internship extends Model
{
    protected $fillable = [
        'supervisor_id', 'title', 'description', 'required_skills',
        'location_type', 'duration_days', 'capacity', 'stipend', 'status',
    ];

    protected function casts(): array
    {
        return [
            'required_skills' => 'array',
            'stipend' => 'decimal:2',
        ];
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('sequence');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}