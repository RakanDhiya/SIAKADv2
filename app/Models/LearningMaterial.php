<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola materi pembelajaran yang tersedia dalam kegiatan akademik.
 */
class LearningMaterial extends Model
{
    protected $guarded = [];

    public function teachingAssignment() { return $this->belongsTo(teachingAssignment::class); }
}