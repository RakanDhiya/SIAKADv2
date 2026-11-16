<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Trait ResolvesStudent
 *
 * Mengatur cakupan akses data siswa berdasarkan
 * peran pengguna yang sedang login.
 */
trait ResolvesStudent
{
    /**
     * Mengambil data siswa sesuai hak akses pengguna.
     *
     * Admin dapat mengakses seluruh data siswa,
     * siswa hanya dapat mengakses datanya sendiri,
     * dan orang tua hanya dapat mengakses data anaknya.
     *
     * @return Builder
     *
     * @throws HttpException
     */
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

    /**
     * Menentukan siswa yang akan ditampilkan berdasarkan request.
     *
     * Jika parameter student tersedia, sistem akan memilih
     * siswa tersebut selama masih berada dalam cakupan akses.
     * Jika tidak tersedia, sistem memilih siswa pertama.
     *
     * @return array{0: Collection, 1: ?Student}
     */
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
