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
        'user_id', 'action', 'deliberation_pv_id', 'details',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliberationPv(): BelongsTo
    {
        return $this->belongsTo(DeliberationPv::class);
    }

    public static function labels(): array
{
    return [
        self::ACTION_CREATED  => 'Création',
        self::ACTION_UPDATED  => 'Modification',
        self::ACTION_ARCHIVED => 'Archivage',
        self::ACTION_VIEWED   => 'Consultation',
        self::ACTION_DELETED  => 'Suppression',
    ];
}

public function label(): string
{
    return self::labels()[$this->action] ?? $this->action;
}
}
