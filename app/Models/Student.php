<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data siswa dan relasi akademiknya.
 */
class Student extends Model
{
    public const GENDERS  = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
    public const STATUSES = ['aktif' => 'Aktif', 'lulus' => 'Lulus', 'pindah' => 'Pindah', 'keluar' => 'Keluar'];

    protected $guarded = [];

    protected $casts = ['birth_date' => 'date'];

    public function user()         { return $this->belongsTo(User::class); }
    public function guardians()    { return $this->belongsToMany(Guardian::class, 'guardian_student'); }
    public function enrollments()  { return $this->hasMany(ClassEnrollment::class); }
    public function attendances()  { return $this->hasMany(Attendance::class); }
    public function scores()       { return $this->hasMany(Score::class); }
    public function reportGrades() { return $this->hasMany(ReportGrade::class); }
    public function submissions()  { return $this->hasMany(Submission::class); }

    // Penempatan kelas yang sedang berjalan (status aktif).
    public function activeEnrollment()
    {
        return $this->hasOne(ClassEnrollment::class)->where('status', 'aktif')->latest('id');
    }

    public static function options(): array
    {
        return static::orderBy('name')->get()
            ->mapWithKeys(fn ($s) => [$s->id => $s->name.' ('.$s->nis.')'])->all();
    }
}