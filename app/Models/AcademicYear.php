<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['label', 'starts_on', 'ends_on', 'is_active', 'external_id'];
protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'is_active' => 'boolean'];
}
