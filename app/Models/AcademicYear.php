<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = ['label', 'starts_on', 'ends_on', 'is_active', 'external_id'];
protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'is_active' => 'boolean'];

public function deliberationPvs(): HasMany
{
    return $this->hasMany(\App\Models\DeliberationPv::class);
}
}