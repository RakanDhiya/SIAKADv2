<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mengelola agenda dalam kalender akademik sekolah.
 */
class AcademicEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Mengambil tahun ajaran pemilik agenda.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Mengambil semester yang terkait dengan agenda.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}