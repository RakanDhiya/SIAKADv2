<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola pengumpulan tugas dan status keterlambatan siswa.
 */
class Submission extends Model
{
    protected $guarded = [];

    protected $casts = ['submitted_at' => 'datetime', 'graded_at' => 'datetime'];

    public function assignment() { return $this->belongsTo(Assignment::class); }
    public function student()    { return $this->belongsTo(Student::class); }

    public function isLate(): bool
    {
        $due = $this->assignment->due_at ?? null;

        return $due && $this->submitted_at && $this->submitted_at->gt($due);
    }
}