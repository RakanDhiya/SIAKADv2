<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola jadwal pembelajaran berdasarkan hari dan waktu.
 */
class Schedule extends Model
{
    public const DAYS = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];

    protected $guarded = [];

    public function teachingAssignment() { return $this->belongsTo(TeachingAssignment::class); }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day] ?? '-';
    }

    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5).'–'.substr($this->end_time, 0, 5);
    }
}