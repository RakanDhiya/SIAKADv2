<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola komponen penilaian pada penugasan mengajar.
 */

class AssessmentComponent extends Model
{
    protected $guarded = [];

    public function teachingssignment() { return $this->belongsTo(TeachingAssignment::class); }
    public function scores()            { return $this->hasMany(Score::class); }
}