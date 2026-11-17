<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data tahun ajaran dalam sistem akademik.
 */
class AcademicYear extends Model
{
    protected $guarded = [];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_active' => 'boolean'];

    public function semesters()  { return $this->hasMany(Semester::class); }
    public function classrooms() { return $this->hasMany(Classroom::class); }

    /**
     * Mengambil tahun ajaran yang sedang aktif.
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Mengambil daftaf tahun ajaran untuk kebutuhan pilihan data.
     */
    public static function options(): array
    {
        return static::orderByDesc('start_date')->pluck('name', 'id')->all();
    }
}