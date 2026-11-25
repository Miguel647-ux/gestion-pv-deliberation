<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliberationPv extends Model
{
    public const STATUS_SOUMIS    = 'soumis';
    public const STATUS_ARCHIVE   = 'archive';
    public const STATUSES = [
        self::STATUS_SOUMIS,
        self::STATUS_ARCHIVE,
    ];

    public const IG_CODE          = 'IG';
    public const SPECIALTIES_IG   = ['ARI', 'AIP'];
    public const SPECIALTY_LEVELS = ['L3'];

    public const LEVEL_SEMESTERS = [
    'L1' => ['S1', 'S2'],
    'L2' => ['S3', 'S4'],
    'L3' => ['S5'],
    'M1' => ['S1', 'S2'],
    'M2' => ['S3', 'S4'],
];

/**
 * Retourne les codes de semestres valides pour un niveau donné.
 */
public static function semestersForLevel(?string $levelCode): array
{
    return self::LEVEL_SEMESTERS[$levelCode] ?? [];
}

/**
 * Vérifie qu'un couple (niveau, semestre) est cohérent.
 */
public static function semesterApplies(?string $levelCode, ?string $semesterCode): bool
{
    return in_array($semesterCode, self::semestersForLevel($levelCode), true);
}

    protected $fillable = [
        'academic_year_id', 'program_id', 'level_id', 'semester_id',
        'class_name', 'specialty', 'deliberation_date',
        'file_path', 'file_name', 'file_type', 'file_size',
        'status', 'created_by', 'archived_at',
    ];

    protected $casts = [
        'deliberation_date' => 'date',
        'archived_at'       => 'datetime',
    ];

    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function program(): BelongsTo      { return $this->belongsTo(Program::class); }
    public function level(): BelongsTo        { return $this->belongsTo(Level::class); }
    public function semester(): BelongsTo     { return $this->belongsTo(Semester::class); }
    public function creator(): BelongsTo      { return $this->belongsTo(User::class, 'created_by'); }

    /**
     * Périmètre de visibilité : DA voit tout, CD ne voit que sa filière.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isDa()
            ? $query
            : $query->where('program_id', $user->program_id);
    }

    /**
     * La spécialité ne s'applique qu'en filière IG et à partir de la L3.
     */
    public static function specialtyApplies(?string $programCode, ?string $levelCode): bool
    {
        return $programCode === self::IG_CODE
            && in_array($levelCode, self::SPECIALTY_LEVELS, true);
    }
}