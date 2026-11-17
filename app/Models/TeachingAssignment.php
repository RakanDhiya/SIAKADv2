<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola penugasan mengajar beserta aktivitas pembelajaran.
 */
class TeachingAssignment extends Model
{
    protected $guarded = [];

    public function teacher()     { return $this->belongsTo(Teacher::class); }
    public function subject()     { return $this->belongsTo(Subject::class); }
    public function classroom()   { return $this->belongsTo(Classroom::class); }
    public function semester()    { return $this->belongsTo(Semester::class); }
    public function schedules()   { return $this->hasMany(Schedule::class); }
    public function components()  { return $this->hasMany(AssessmentComponent::class); }
    public function materials()   { return $this->hasMany(LearningMaterial::class); }
    public function assignments() { return $this->hasMany(Assignment::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }

    /** Query siswa aktif di kelas penugasan ini. (Bukan relasi Eloquent; jangan diakses sebagai properti.) */
    public function enrolledStudents()
    {
        return Student::whereHas('enrollments', fn ($q) => $q
            ->where('classroom_id', $this->classroom_id)
            ->where('status', 'aktif'))
            ->orderBy('name');
    }

    public function getLabelAttribute(): string
    {
        return ($this->subject->name ?? '-').' — '.($this->classroom->name ?? '-')
            .' ('.($this->semester->label ?? '-').')';
    }

    public function getFullLabelAttribute(): string
    {
        return $this->label.' · '.($this->teacher->name ?? '-');
    }
}