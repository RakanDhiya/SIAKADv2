<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Kenaikan kelas: penempatan lama diberi status (naik/tinggal/lulus),
 * lalu dibuat penempatan baru di tahun ajaran tujuan. Riwayat kelas tetap tersimpan.
 */
class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $options = Classroom::options();
        $enrollments = collect();

        if ($from = $request->query('from_classroom_id')) {
            $enrollments = ClassEnrollment::with('student')
                ->where('classroom_id', $from)->where('status', 'aktif')
                ->get()->sortBy('student.name');
        }

        return view('modules.promotion.index', compact('options', 'enrollments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_classroom_id' => ['required', 'exists:classrooms,id'],
            'to_classroom_id'   => ['required', 'exists:classrooms,id', 'different:from_classroom_id'],
            'stay_classroom_id' => ['nullable', 'exists:classrooms,id'],
            'result'            => ['required', 'array'],
            'result.*'          => ['nullable', 'in:naik,tinggal,lulus'],
        ]);

        $from = Classroom::findOrFail($data['from_classroom_id']);
        $to = Classroom::findOrFail($data['to_classroom_id']);
        $stay = isset($data['stay_classroom_id']) ? Classroom::find($data['stay_classroom_id']) : null;

        $done = 0;
        $skipped = 0;

        DB::transaction(function () use ($data, $from, $to, $stay, &$done, &$skipped) {
            foreach ($data['result'] as $enrollmentId => $result) {
                if (! $result) {
                    continue;
                }

                $enrollment = ClassEnrollment::where('id', $enrollmentId)
                    ->where('classroom_id', $from->id)->where('status', 'aktif')->first();
                if (! $enrollment) {
                    continue;
                }

                if ($result === 'lulus') {
                    $enrollment->update(['status' => 'lulus']);
                    Student::where('id', $enrollment->student_id)->update(['status' => 'lulus']);
                    $done++;
                    continue;
                }

                $target = $result === 'naik' ? $to : $stay;

                $invalid = ! $target
                    || $target->academic_year_id === $from->academic_year_id
                    || ClassEnrollment::where('student_id', $enrollment->student_id)
                        ->where('academic_year_id', $target->academic_year_id)->exists();

                if ($invalid) {
                    $skipped++;
                    continue;
                }

                $enrollment->update(['status' => $result]);
                ClassEnrollment::create([
                    'student_id'       => $enrollment->student_id,
                    'classroom_id'     => $target->id,
                    'academic_year_id' => $target->academic_year_id,
                    'status'           => 'aktif',
                ]);
                $done++;
            }
        });

        $message = "{$done} siswa diproses.";
        if ($skipped) {
            $message .= " {$skipped} dilewati (kelas tujuan belum dipilih, tahun ajaran sama, atau sudah punya penempatan di tahun tujuan).";
        }

        return redirect()->route('promotion.index', ['from_classroom_id' => $from->id])->with('success', $message);
    }
}
