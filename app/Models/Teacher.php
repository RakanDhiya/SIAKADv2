<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data guru dan relasi akademiknya.
 */
class Teacher extends Model
{
    public const GENDERS = ['L' => 'Laki-laki', 'P' => 'Perempuan'];

    protected $guarded = [];

    public function user()               { return $this->belongsTo(User::class); }
    public function teachingAssignments() { return $this->hasMany(TeachingAssignment::class); }
    public function homeroomClasses()    { return $this->hasMany(Classroom::class, 'homeroom_teacher_id'); }

    public static function options(): array
    {
        return static::orderBy('name')->pluck('name', 'id')->all();
    }
}