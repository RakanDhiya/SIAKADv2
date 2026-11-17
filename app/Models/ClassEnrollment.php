<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data penempatan siswa pada kelas berdasarkan tahun ajaran.
 */
class ClassEnrollment extends Model
{
    public const STATUSES = [
        'aktif'  => 'Aktif',
        'naik'   => 'Naik kelas',
        'tinggal' => 'Tinggal kelas',
        'lulus'  => 'Lulus',
        'pindah' => 'Pindah',
    ];

    protected $guarded = [];

    public function student()      { return $this->belongsTo(Student::class); }
    public function classroom()    { return $this->belongsTo(Classroom::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
}