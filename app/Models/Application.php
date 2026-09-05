<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = ['internship_id', 'student_id', 'cover_note', 'match_score', 'status'];

    protected function casts(): array
    {
        return ['match_score' => 'decimal:2'];
    }

    public function internship(): BelongsTo { return $this->belongsTo(Internship::class); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function interviews(): HasMany { return $this->hasMany(Interview::class); }
    public function payment(): HasMany { return $this->hasMany(Payment::class); }
}