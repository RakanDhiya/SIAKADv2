<?php

namespace Illuminate\Database\Eloquent\Model;

/**
 * Mengelola tugas pembelajaran dan pengumpulan tugas siswa
 */
class Assignment extends Model
{
    protected $guarded = [];

    protected $casts = ['due_at' => 'datetime'];

    public function teachingAssignment() { return $this->belongsTo(TeachingAssignment::class); }
    public function submissions()        { return $this->hasMany(Submission::class); } 
}