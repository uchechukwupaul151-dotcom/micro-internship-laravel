<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorProfile extends Model
{
    protected $fillable = [
        'user_id', 'organization_name', 'contact_person', 'phone',
        'company_profile', 'website',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}