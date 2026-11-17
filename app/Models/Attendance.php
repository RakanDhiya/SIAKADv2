<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data kehadiran siswa dalam kegiatan akademik.
 */
class Attendance extends Model
{
    public const STATUSES = ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'];

    protected $guarded = [];

    protected $casts = ['date' => 'date'];

    public function student()            { return $this->belongsTo(Student::class); }
    public function teachingAssignment() { return $this->belongsTo(TeachingAssignment::class); }
}