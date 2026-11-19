<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TeacherScope;
use App\Models\Attendance;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    use TeacherScope;

    public function index(Request $request)
    {
        $request->validate(['date' => ['nullable', 'date']]);

        $options = $this->assignmentOptions();
        $date = $request->query('date', today()->toDateString());
        $ta = null;
        $students = collect();
        $existing = collect();

        if ($id = $request->query('teaching_assignment_id')) {
            abort_unless(isset($options[$id]), 404);

            $ta = TeachingAssignment::with(['subject', 'classroom'])->findOrFail($id);
            $students = $ta->enrolledStudents()->get();
            $existing = Attendance::where('teaching_assignment_id', $ta->id)
                ->whereDate('date', $date)->get()->keyBy('student_id');
        }

        return view('modules.attendance.index', [
            'options'  => $options,
            'date'     => $date,
            'ta'       => $ta,
            'students' => $students,
            'existing' => $existing,
            'statuses' => Attendance::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teaching_assignment_id' => ['required', Rule::in(array_keys($this->assignmentOptions()))],
            'date'     => ['required', 'date'],
            'status'   => ['required', 'array'],
            'status.*' => ['required', Rule::in(array_keys(Attendance::STATUSES))],
            'note'     => ['nullable', 'array'],
            'note.*'   => ['nullable', 'string', 'max:150'],
        ]);

        $ta = TeachingAssignment::findOrFail($data['teaching_assignment_id']);
        $validIds = $ta->enrolledStudents()->pluck('id')->all();

        foreach ($data['status'] as $studentId => $status) {
            if (! in_array((int) $studentId, $validIds, true)) {
                continue;
            }

            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'teaching_assignment_id' => $ta->id, 'date' => $data['date']],
                ['status' => $status, 'note' => $data['note'][$studentId] ?? null]
            );
        }

        return redirect()
            ->route('attendance.index', ['teaching_assignment_id' => $ta->id, 'date' => $data['date']])
            ->with('success', 'Absensi berhasil disimpan.');
    }
}
