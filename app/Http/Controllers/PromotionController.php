<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassEnrollment;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $options = Classroom::with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($classroom) => [
                $classroom->id => $classroom->name.' ('.$classroom->academicYear->name.')',
            ])
            ->all();

        $enrollments = collect();

        if ($fromId = $request->query('from_classroom_id')) {
            $from = Classroom::find($fromId);

            if ($from) {
                $enrollments = ClassEnrollment::with('student')
                    ->where('classroom_id', $from->id)
                    ->where('status', 'aktif')
                    ->whereHas('student', fn ($query) => $query->where('status', 'aktif'))
                    ->get()
                    ->sortBy('student.name');
            }
        }

        return view('modules.promotion.index', compact('options', 'enrollments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_classroom_id' => ['required', 'integer', 'exists:classrooms,id'],
            'to_classroom_id' => ['nullable', 'integer', 'exists:classrooms,id'],
            'stay_classroom_id' => ['nullable', 'integer', 'exists:classrooms,id'],
            'result' => ['required', 'array', 'min:1'],
            'result.*' => ['nullable', 'in:naik,tinggal,lulus'],
        ]);

        $done = 0;

        DB::transaction(function () use ($data, &$done) {
            $from = Classroom::whereKey($data['from_classroom_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array((int) $from->grade_level, [10, 11, 12], true)) {
                throw ValidationException::withMessages([
                    'from_classroom_id' => 'Tingkat kelas asal tidak valid.',
                ]);
            }

            $activeYear = AcademicYear::where('is_active', true)->first();

            if (! $activeYear) {
                throw ValidationException::withMessages([
                    'from_classroom_id' => 'Belum ada tahun ajaran aktif untuk tujuan kenaikan kelas.',
                ]);
            }

            if ((int) $from->academic_year_id === (int) $activeYear->id) {
                throw ValidationException::withMessages([
                    'from_classroom_id' => 'Kelas asal harus berasal dari tahun ajaran sebelumnya, bukan tahun ajaran aktif.',
                ]);
            }

            $results = collect($data['result'])
                ->filter(fn ($result) => filled($result));

            if ($results->isEmpty()) {
                throw ValidationException::withMessages([
                    'result' => 'Pilih minimal satu hasil untuk diproses.',
                ]);
            }

            $enrollments = ClassEnrollment::with('student')
                ->whereIn('id', $results->keys())
                ->where('classroom_id', $from->id)
                ->where('academic_year_id', $from->academic_year_id)
                ->where('status', 'aktif')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($enrollments->count() !== $results->count()) {
                throw ValidationException::withMessages([
                    'result' => 'Sebagian data siswa tidak valid atau sudah berubah.',
                ]);
            }

            $needsPromotion = $results->contains('naik');
            $needsRetention = $results->contains('tinggal');

            $to = null;
            $stay = null;

            if ($needsPromotion) {
                if ((int) $from->grade_level >= 12) {
                    throw ValidationException::withMessages([
                        'result' => 'Siswa tingkat 12 tidak dapat dinaikkan ke tingkat berikutnya.',
                    ]);
                }

                $to = Classroom::find($data['to_classroom_id'] ?? null);

                $this->validateTargetClass(
                    $from,
                    $to,
                    $activeYear,
                    (int) $from->grade_level + 1,
                    'Kelas tujuan naik kelas'
                );
            }

            if ($needsRetention) {
                $stay = Classroom::find($data['stay_classroom_id'] ?? null);

                $this->validateTargetClass(
                    $from,
                    $stay,
                    $activeYear,
                    (int) $from->grade_level,
                    'Kelas tujuan tinggal kelas'
                );
            }

            foreach ($results as $enrollmentId => $result) {
                $enrollment = $enrollments->get($enrollmentId);
                $student = $enrollment->student;

                if (! $student || $student->status !== 'aktif') {
                    throw ValidationException::withMessages([
                        'result' => 'Hanya siswa berstatus aktif yang dapat diproses.',
                    ]);
                }

                if ($result === 'lulus') {
                    if ((int) $from->grade_level !== 12) {
                        throw ValidationException::withMessages([
                            'result' => 'Hanya siswa tingkat 12 yang dapat dinyatakan lulus.',
                        ]);
                    }

                    $enrollment->update(['status' => 'lulus']);
                    $student->update(['status' => 'lulus']);

                    $done++;
                    continue;
                }

                $target = $result === 'naik' ? $to : $stay;

                $alreadyEnrolled = ClassEnrollment::where('student_id', $student->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->exists();

                if ($alreadyEnrolled) {
                    throw ValidationException::withMessages([
                        'result' => "Siswa {$student->name} sudah memiliki penempatan pada tahun ajaran tujuan.",
                    ]);
                }

                $enrollment->update([
                    'status' => $result,
                ]);

                ClassEnrollment::create([
                    'student_id' => $student->id,
                    'classroom_id' => $target->id,
                    'academic_year_id' => $target->academic_year_id,
                    'status' => 'aktif',
                ]);

                $done++;
            }
        });

        return redirect()
            ->route('promotion.index', [
                'from_classroom_id' => $data['from_classroom_id'],
            ])
            ->with('success', "{$done} siswa berhasil diproses.");
    }

    private function validateTargetClass(
        Classroom $from,
        ?Classroom $target,
        AcademicYear $activeYear,
        int $expectedGrade,
        string $label
    ): void {
        if (! $target) {
            throw ValidationException::withMessages([
                'to_classroom_id' => "{$label} wajib dipilih.",
            ]);
        }

        if ((int) $target->grade_level !== $expectedGrade) {
            throw ValidationException::withMessages([
                'to_classroom_id' => "{$label} harus tingkat {$expectedGrade}.",
            ]);
        }

        if ((int) $target->academic_year_id === (int) $from->academic_year_id) {
            throw ValidationException::withMessages([
                'to_classroom_id' => 'Tahun ajaran tujuan harus berbeda dari tahun ajaran asal.',
            ]);
        }

        if ((int) $target->academic_year_id !== (int) $activeYear->id) {
            throw ValidationException::withMessages([
                'to_classroom_id' => 'Kelas tujuan harus berada di tahun ajaran aktif.',
            ]);
        }

        if ($target->major_id != $from->major_id) {
            throw ValidationException::withMessages([
                'to_classroom_id' => 'Jurusan kelas tujuan harus sama dengan kelas asal.',
            ]);
        }
    }
}