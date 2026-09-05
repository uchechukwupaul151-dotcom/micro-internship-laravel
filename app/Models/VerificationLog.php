<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationLog extends Model
{
    protected $fillable = ['deliverable_id', 'passed', 'checks'];

    protected function casts(): array
    {
        return ['passed' => 'boolean', 'checks' => 'array'];
    }

    public function deliverable(): BelongsTo { return $this->belongsTo(Deliverable::class); }
}