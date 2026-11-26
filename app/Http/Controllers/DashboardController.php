<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesStudent;
use App\Models\AcademicEvent;
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
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    use ResolvesStudent;

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $semester = Semester::active();
        $academicYear = AcademicYear::active();

        $data = [
            'counts' => [
                'Siswa aktif'    => Student::where('status', 'aktif')->count(),
                'Guru'           => Teacher::count(),
                'Kelas'          => Classroom::whereHas('academicYear', fn ($q) => $q->where('is_active', true))->count(),
                'Mata pelajaran' => Subject::count(),
            ],

            'year' => $academicYear,
            'semester' => $semester,

            'announcements' => Announcement::visibleTo($user)
                ->latest('published_at')
                ->limit(5)
                ->get(),

            // Lima agenda akademik terdekat pada tahun ajaran aktif.
            'upcomingEvents' => AcademicEvent::with('academicYear')
                ->when(
                    $academicYear,
                    fn ($q) => $q->where('academic_year_id', $academicYear->id),
                    fn ($q) => $q->whereRaw('1 = 0')
                )
                ->whereDate('end_date', '>=', today())
                ->orderBy('start_date')
                ->limit(5)
                ->get(),

            // Seluruh agenda yang berlangsung pada bulan berjalan.
            'calendarEvents' => AcademicEvent::when(
                    $academicYear,
                    fn ($q) => $q->where('academic_year_id', $academicYear->id),
                    fn ($q) => $q->whereRaw('1 = 0')
                )
                ->whereDate('start_date', '<=', now()->endOfMonth())
                ->whereDate('end_date', '>=', now()->startOfMonth())
                ->orderBy('start_date')
                ->get(),

            'today' => now()->dayOfWeekIso,
            'todaySchedule' => collect(),
            'extra' => [],
        ];

        // Dashboard khusus Admin.
        if ($user->hasRole('admin')) {
            $data['classSummary'] = Classroom::whereHas(
                'academicYear',
                fn ($q) => $q->where('is_active', true)
            )
                ->withCount([
                    'enrollments as students_count' => fn ($q) => $q->where('status', 'aktif'),
                ])
                ->orderBy('name')
                ->get();

            $data['attendanceToday'] = Attendance::whereDate('date', today())
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            // Rekap absensi selama satu minggu berjalan.
            $weekStart = Carbon::today()->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->addDays(6);

            $weeklyRecords = Attendance::query()
                ->whereBetween('date', [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ])
                ->selectRaw('DATE(date) as attendance_date, status, COUNT(*) as total')
                ->groupByRaw('DATE(date), status')
                ->get()
                ->groupBy('attendance_date');

            $dayNames = [
                1 => 'Sen',
                2 => 'Sel',
                3 => 'Rab',
                4 => 'Kam',
                5 => 'Jum',
                6 => 'Sab',
                7 => 'Min',
            ];

            $data['weeklyAttendance'] = collect(range(0, 6))
                ->map(function ($offset) use ($weekStart, $weeklyRecords, $dayNames) {
                    $date = $weekStart->copy()->addDays($offset);
                    $dateKey = $date->toDateString();

                    $records = $weeklyRecords
                        ->get($dateKey, collect())
                        ->keyBy('status');

                    $statuses = [];

                    foreach (array_keys(Attendance::STATUSES) as $status) {
                        $statuses[$status] = (int) (
                            $records->get($status)->total ?? 0
                        );
                    }

                    return [
                        'date' => $dateKey,
                        'day' => $dayNames[$date->dayOfWeekIso],
                        'label' => $date->format('d/m'),
                        'statuses' => $statuses,
                        'total' => array_sum($statuses),
                    ];
                })
                ->values();

            // Mengambil seluruh jadwal pelajaran pada hari ini.
            if ($semester) {
                $data['todaySchedule'] = Schedule::with([
                    'teachingAssignment.subject',
                    'teachingAssignment.classroom',
                    'teachingAssignment.teacher',
                ])
                    ->where('day', $data['today'])
                    ->whereHas(
                        'teachingAssignment',
                        fn ($q) => $q->where('semester_id', $semester->id)
                    )
                    ->orderBy('start_time')
                    ->get();
            }
        }

        // Dashboard khusus Guru.
        if ($user->hasRole('guru') && $user->teacher && $semester) {
            $teacherId = $user->teacher->id;

            $data['todaySchedule'] = Schedule::with([
                'teachingAssignment.subject',
                'teachingAssignment.classroom',
            ])
                ->where('day', $data['today'])
                ->whereHas('teachingAssignment', fn ($q) => $q
                    ->where('teacher_id', $teacherId)
                    ->where('semester_id', $semester->id))
                ->orderBy('start_time')
                ->get();

            $data['extra']['Tugas menunggu dinilai'] = Submission::whereNull('graded_at')
                ->whereHas(
                    'assignment.teachingAssignment',
                    fn ($q) => $q->where('teacher_id', $teacherId)
                )
                ->count();
        }

        // Dashboard khusus Siswa dan Orang Tua.
        if ($user->hasRole('siswa', 'ortu')) {
            [$students, $student] = $this->pickStudent($request);

            $classroomId = $student?->activeEnrollment?->classroom_id;

            $data['students'] = $students;
            $data['student'] = $student;
            $data['classroom'] = $student?->activeEnrollment?->classroom;

            if ($classroomId && $semester) {
                $data['todaySchedule'] = Schedule::with([
                    'teachingAssignment.subject',
                    'teachingAssignment.teacher',
                ])
                    ->where('day', $data['today'])
                    ->whereHas('teachingAssignment', fn ($q) => $q
                        ->where('classroom_id', $classroomId)
                        ->where('semester_id', $semester->id))
                    ->orderBy('start_time')
                    ->get();

                $data['pendingTasks'] = Assignment::with('teachingAssignment.subject')
                    ->whereHas('teachingAssignment', fn ($q) => $q
                        ->where('classroom_id', $classroomId)
                        ->where('semester_id', $semester->id))
                    ->whereDoesntHave('submissions', fn ($q) => $q
                        ->where('student_id', $student->id))
                    ->where(fn ($q) => $q
                        ->whereNull('due_at')
                        ->orWhere('due_at', '>=', now()))
                    ->orderBy('due_at')
                    ->limit(5)
                    ->get();
            }
        }

        return view('modules.dashboard.index', $data);
    }
}