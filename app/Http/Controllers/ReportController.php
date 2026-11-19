<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TeacherScope;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\ReportGrade;
use App\Models\Score;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use TeacherScope;

    public function students(Request $request)
    {
        $students = Student::with('activeEnrollment.classroom')
            ->when($request->query('classroom_id'), fn ($q, $c) => $q->whereHas('activeEnrollment', fn ($e) => $e->where('classroom_id', $c)))
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderBy('name')->get();

        return view('modules.reports.students', [
            'students'   => $students,
            'classrooms' => Classroom::options(),
            'statuses'   => Student::STATUSES,
        ]);
    }

    public function teachers()
    {
        return view('modules.reports.teachers', ['teachers' => Teacher::orderBy('name')->get()]);
    }

    public function attendance(Request $request)
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);

        $options = $this->assignmentOptions();
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to = $request->query('to', today()->toDateString());
        $ta = null;
        $students = collect();
        $counts = collect();

        if ($id = $request->query('teaching_assignment_id')) {
            abort_unless(isset($options[$id]), 404);

            $ta = TeachingAssignment::with(['subject', 'classroom', 'semester.academicYear'])->findOrFail($id);
            $students = $ta->enrolledStudents()->get();
            $counts = Attendance::where('teaching_assignment_id', $ta->id)
                ->whereBetween('date', [$from, $to])
                ->selectRaw('student_id, status, count(*) as total')
                ->groupBy('student_id', 'status')->get()
                ->groupBy('student_id')->map(fn ($rows) => $rows->pluck('total', 'status'));
        }

        return view('modules.reports.attendance', [
            'options' => $options, 'from' => $from, 'to' => $to, 'ta' => $ta,
            'students' => $students, 'counts' => $counts, 'statuses' => Attendance::STATUSES,
        ]);
    }

    public function scores(Request $request)
    {
        $options = $this->assignmentOptions();
        $ta = null;
        $components = $students = $scores = $finals = collect();

        if ($id = $request->query('teaching_assignment_id')) {
            abort_unless(isset($options[$id]), 404);

            $ta = TeachingAssignment::with(['subject', 'classroom', 'semester.academicYear'])->findOrFail($id);
            $components = $ta->components()->orderBy('id')->get();
            $students = $ta->enrolledStudents()->get();
            $scores = Score::whereIn('assessment_component_id', $components->pluck('id'))
                ->get()->groupBy('student_id')->map->keyBy('assessment_component_id');
            $finals = ReportGrade::where('semester_id', $ta->semester_id)
                ->where('subject_id', $ta->subject_id)->get()->keyBy('student_id');
        }

        return view('modules.reports.scores', compact('options', 'ta', 'components', 'students', 'scores', 'finals'));
    }
}
