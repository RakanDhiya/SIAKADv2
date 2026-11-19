<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Student;
use Illuminate\Http\Request;

/**
 * Menentukan siswa mana yang boleh dilihat user:
 * admin = semua, siswa = dirinya sendiri, orang tua = anak yang terhubung.
 */
trait ResolvesStudent
{
    protected function viewableStudents()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return Student::query();
        }

        if ($user->hasRole('siswa')) {
            return Student::where('user_id', $user->id);
        }

        if ($user->hasRole('ortu')) {
            return Student::whereHas('guardians', fn ($q) => $q->where('user_id', $user->id));
        }

        abort(403);
    }

    /** @return array{0: \Illuminate\Support\Collection, 1: ?Student} */
    protected function pickStudent(Request $request): array
    {
        $students = $this->viewableStudents()->orderBy('name')->get();
        $requested = $request->query('student');

        $student = $requested
            ? $students->firstWhere('id', (int) $requested)
            : $students->first();

        return [$students, $student];
    }
}
