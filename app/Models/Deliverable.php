<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\GithubCommit;

class Deliverable extends Model
{
    protected $fillable = [
        'milestone_id', 'student_id', 'file_path', 'repository_url', 'sha256_hash',
        'status', 'feedback', 'submitted_at',
    ];

    protected function casts(): array { return ['submitted_at' => 'datetime']; }
    public function milestone(): BelongsTo { return $this->belongsTo(Milestone::class); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function verificationLogs(): HasMany { return $this->hasMany(VerificationLog::class); }
    public function commits(): HasMany { return $this->hasMany(GithubCommit::class); }
}