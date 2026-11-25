<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public const ACTION_CREATED   = 'created';
    public const ACTION_UPDATED   = 'updated';
    public const ACTION_SUBMITTED = 'submitted';
    public const ACTION_ARCHIVED  = 'archived';
    public const ACTION_VIEWED    = 'viewed';
    public const ACTION_DELETED   = 'deleted';

    protected $fillable = [
        'user_id', 'action', 'deliberation_pv_id', 'details', 'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliberationPv(): BelongsTo
    {
        return $this->belongsTo(DeliberationPv::class);
    }
}
