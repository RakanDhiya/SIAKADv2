<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesStudent;
use App\Models\Attendance;
use App\Models\ClassEnrollment;
use App\Models\ReportGrade;
use App\Models\Semester;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    use ResolvesStudent;

    public function show(Request $request)
    {
        [$students, $student] = $this->pickStudent($request);

        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $semester = $request->query('semester')
            ? $semesters->firstWhere('id', (int) $request->query('semester'))
            : ($semesters->firstWhere('is_active', true) ?? $semesters->first());

        $grades = collect();
        $attendance = collect();
        $classroom = null;

        if ($student && $semester) {
            $grades = ReportGrade::with('subject')
                ->where('student_id', $student->id)->where('semester_id', $semester->id)->get()
                ->sortBy(fn ($g) => $g->subject->name)->values();

            $classroom = ClassEnrollment::with('classroom.homeroomTeacher')
                ->where('student_id', $student->id)
                ->where('academic_year_id', $semester->academic_year_id)->first()?->classroom;

            $attendance = Attendance::where('student_id', $student->id)
                ->whereHas('teachingAssignment', fn ($q) => $q->where('semester_id', $semester->id))
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        }

        return view('modules.report-card.show', [
            'students'   => $students,
            'student'    => $student,
            'semesters'  => $semesters,
            'semester'   => $semester,
            'grades'     => $grades,
            'classroom'  => $classroom,
            'attendance' => $attendance,
            'average'    => $grades->avg('final_score'),
        ]);
    }
}
