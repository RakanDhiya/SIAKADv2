<?php

namespace App\Http\Controllers\Concerns;

use App\Models\TeachingAssignment;

/**
 * Pembatasan data untuk Guru: guru hanya mengelola penugasan miliknya.
 * Admin tidak dibatasi.
 */
trait TeacherScope
{
    /** ID guru untuk user guru; null untuk admin. */
    protected function teacherId(): ?int
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return null;
        }

        $teacher = $user->teacher;
        abort_unless($teacher, 403, 'Akun guru ini belum ditautkan ke data guru. Hubungi admin.');

        return (int) $teacher->id;
    }

    /** Opsi penugasan (id => label) yang boleh diakses user saat ini. */
    protected function assignmentOptions(): array
    {
        $query = TeachingAssignment::with(['subject', 'classroom', 'semester.academicYear', 'teacher'])
            ->orderByDesc('id');

        $teacherId = $this->teacherId();
        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        return $query->get()
            ->mapWithKeys(fn ($a) => [$a->id => $teacherId ? $a->label : $a->full_label])
            ->all();
    }

    /** Batasi query model yang punya relasi teachingAssignment. */
    protected function scopeToTeacher($query)
    {
        $teacherId = $this->teacherId();

        if ($teacherId) {
            $query->whereHas('teachingAssignment', fn ($q) => $q->where('teacher_id', $teacherId));
        }

        return $query;
    }
}
