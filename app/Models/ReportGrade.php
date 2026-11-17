<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola nilai rapor siswa dalam periode akademik.
 */
class ReportGrade extends Model
{
    protected $guarded = [];

    public function student()   { return $this->belongsTo(Student::class); }
    public function subject()   { return $this->belongsTo(Subject::class); }
    public function semester()  { return $this->belongsTo(Semester::class); }
    public function classroom() { return $this->belongsTo(Classroom::class); }

    /**
     * Menentukan predikat nilai berdasarkan rentang skor.
     */
    public static function predicate(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            default      => 'D',
        };
    }
}