<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = ['student_id', 'internship_id', 'certificate_number', 'verification_hash', 'pdf_path', 'issued_at'];
    protected function casts(): array { return ['issued_at' => 'datetime']; }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function internship(): BelongsTo { return $this->belongsTo(Internship::class); }
}