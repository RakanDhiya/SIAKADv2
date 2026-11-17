<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data mata pelajaran.
 */
class Subject extends Model
{
    protected $guarded = [];

    public function teachingAssignments() { return $this->hasMany(TeachingAssignment::class); }

    public static function options(): array
    {
        return static::orderBy('name')->pluck('name', 'id')->all();
    }
}