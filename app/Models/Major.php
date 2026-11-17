<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data jurusan dalam sistem akademik.
 */
class Major extends Model
{
    protected $guarded = [];

    public function classrooms() { return $this->hasMany(Classroom::class); }

    public static function options(): array
    {
        return static::orderBy('name')->pluck('name', 'id')->all();
    }
}