<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola nilai siswa berdasarkan komponen penilaian.
 */
class Score extends Model
{
    protected $guarded = [];

    public function component() { return $this->belongsTo(AssessmentComponent::class); }
    public function student()   { return $this->belongsTo(Student::class); }
}