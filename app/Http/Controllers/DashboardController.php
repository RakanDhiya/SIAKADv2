<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesStudent;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Teacher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ResolvesStudent;

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $semester = Semester::active();

        $data = [
            'counts' => [
                'Siswa aktif'    => Student::where('status', 'aktif')->count(),
                'Guru'           => Teacher::count(),
                'Kelas'          => Classroom::whereHas('academicYear', fn ($q) => $q->where('is_active', true))->count(),
                'Mata pelajaran' => Subject::count(),
            ],
            'year'          => AcademicYear::active(),
            'semester'      => $semester,
            'announcements' => Announcement::visibleTo($user)->latest('published_at')->limit(5)->get(),
            'today'         => now()->dayOfWeekIso,
            'todaySchedule' => collect(),
            'extra'         => [],
        ];

        if ($user->hasRole('admin')) {
            $data['classSummary'] = Classroom::whereHas('academicYear', fn ($q) => $q->where('is_active', true))
                ->withCount(['enrollments as students_count' => fn ($q) => $q->where('status', 'aktif')])
                ->orderBy('name')->get();

            $data['attendanceToday'] = Attendance::whereDate('date', today())
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        }

        if ($user->hasRole('guru') && $user->teacher && $semester) {
            $teacherId = $user->teacher->id;

            $data['todaySchedule'] = Schedule::with(['teachingAssignment.subject', 'teachingAssignment.classroom'])
                ->where('day', $data['today'])
                ->whereHas('teachingAssignment', fn ($q) => $q->where('teacher_id', $teacherId)->where('semester_id', $semester->id))
                ->orderBy('start_time')->get();

            $data['extra']['Tugas menunggu dinilai'] = Submission::whereNull('graded_at')
                ->whereHas('assignment.teachingAssignment', fn ($q) => $q->where('teacher_id', $teacherId))->count();
        }

        if ($user->hasRole('siswa', 'ortu')) {
            [$students, $student] = $this->pickStudent($request);
            $classroomId = $student?->activeEnrollment?->classroom_id;
            $data['students'] = $students;
            $data['student'] = $student;
            $data['classroom'] = $student?->activeEnrollment?->classroom;

            if ($classroomId && $semester) {
                $data['todaySchedule'] = Schedule::with(['teachingAssignment.subject', 'teachingAssignment.teacher'])
                    ->where('day', $data['today'])
                    ->whereHas('teachingAssignment', fn ($q) => $q->where('classroom_id', $classroomId)->where('semester_id', $semester->id))
                    ->orderBy('start_time')->get();

                $data['pendingTasks'] = Assignment::with('teachingAssignment.subject')
                    ->whereHas('teachingAssignment', fn ($q) => $q->where('classroom_id', $classroomId)->where('semester_id', $semester->id))
                    ->whereDoesntHave('submissions', fn ($q) => $q->where('student_id', $student->id))
                    ->where(fn ($q) => $q->whereNull('due_at')->orWhere('due_at', '>=', now()))
                    ->orderBy('due_at')->limit(5)->get();
            }
        }

        return view('modules.dashboard.index', $data);
    }
}
