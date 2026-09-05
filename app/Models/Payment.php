<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['application_id', 'payer_id', 'payee_id', 'amount', 'currency', 'status', 'reference', 'funded_at', 'released_at'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'funded_at' => 'datetime', 'released_at' => 'datetime']; }
    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function payer(): BelongsTo { return $this->belongsTo(User::class, 'payer_id'); }
    public function payee(): BelongsTo { return $this->belongsTo(User::class, 'payee_id'); }
}