<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data kelas dan relasinya dalam sistem akademik.
 */
class Classroom extends Model
{
    protected $guarded = [];

    public function academicYear()     { return $this->belongsTo(AcademicYear::class); }
    public function major()            { return $this->belongsTo(Major::class); }
    public function homeroomTeacher()  { return $this->belongsTo(Teacher::class, 'homeroom_teacher_id'); }
    public function enrollments()      { return $this->hasMany(ClassEnrollment::class); }
    public function teachingAssignments() { return $this->hasMany(TeachingAssignment::class); }

    public static function options(): array
    {
        return static::with('academicYear')->orderByDesc('academic_year_id')->orderBy('name')->get()
            ->mapWithKeys(fn ($c) => [$c->id => $c->name.' ('.($c->academicYear->name ?? '-').')'])->all();
    }
}