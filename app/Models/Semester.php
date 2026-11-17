<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data semester dalam tahun ajaran.
 */
class Semester extends Model
{
    public const NAMES = ['Ganjil' => 'Ganjil', 'Genap' => 'Genap'];

    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }

    public function getLabelAttribute(): string
    {
        return $this->name.' '.($this->academicYear->name ?? '');
    }

    public static function active(): ?self
    {
        return static::with('academicYear')->where('is_active', true)->first();
    }

    public static function options(): array
    {
        return static::with('academicYear')->orderByDesc('id')->get()
            ->mapWithKeys(fn ($s) => [$s->id => $s->label])->all();
    }
}