<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GithubCommit extends Model
{
    protected $fillable = ['deliverable_id', 'commit_hash', 'author_name', 'author_email', 'message', 'diff', 'committed_at'];
    protected function casts(): array { return ['committed_at' => 'datetime']; }
    public function deliverable(): BelongsTo { return $this->belongsTo(Deliverable::class); }
}