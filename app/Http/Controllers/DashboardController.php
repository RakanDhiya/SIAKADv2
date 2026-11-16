<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

/**
 * Dashboard Controller
 *
 * Mengelola pengambilan dan penyajian data dashboard
 * berdasarkan peran pengguna yang sedang login.
 *
 * Role yang didukung:
 * - Admin
 * - Guru
 * - Siswa
 * - Orang Tua
 *
 * @package App\Http\Controllers
 */

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard sesuai peran pengguna.
     *
     * Mengambil data akademik, informasi pengguna, jadwal,
     * pengumuman, serta aktivitas berdasarkan hak akses.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */

    // Mengambil pengguna yang sedang login dan semester aktif.
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $semester = Semester::active();

        // Data umum yang akan ditampilkan di dashboard.
        $data = [
            'counts' => [
                'Siswa aktif'   => Student::where('status', 'aktif')->count(),
                'Guru'          => Teacher::count(),
                'Kelas'         => Classroom::whereHas('academicYear', fn ($q) => $q->where('is_active', true))->count(),
                'Mata pelajaran' => Subject::count(),
            ],

            'year'          => AcademicYear::active(),
            'semester'      => $semester,
            'announcements' => Announcement::visibleTo($user)->latest('published_at')->limit(5)->get(),
            'today'         => now()->dayOfWeekIso,
            'todaySchedule' => collect(),
            'extra'         => [],
        ];

        // Ringakasan akademik khusus untuk Admin
        if ($user->hasRole('admin')) {
            $data['classSummary'] = Classroom::whereHas ('academicYear', fn ($q) => $q->where('is_active', true))
                ->withCount(['enrollments as students_count' => fn ($q) => $q->where('status', 'aktif')])
                ->orderBy('name')->get();

            $data['attendanceToday'] = Attendance::whereDate('date', today())
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        }

        // Jadwal mengajar dan tugas yang perlu dinilai oleh Guru.
        if ($user->hasRole('guru') && $user->teacher && $semester) {
            $teacherId = $user->teacher->id;

            $data['todaySchedule'] = Schedule::with(['teachingAssignment.subject', 'teachingAssignment.classroom'])
                ->where('day', $data['today'])
                ->whereHas('teachingAssignment', fn ($q) => $q->where('teacher_id', $teacherId)->where('semester_id', $semester->id))
                ->orderBy('start_time')->get();

            $data['extra']['Tugas menunggu dinilai'] = Submission::whereNull('graded_at')
                ->whereHas('assignment.teachingAssignment', fn ($q) => $q->where('teacher_id', $teacherId))->count();
        }

        // Mengambil jadwal dan tugas jika siswa memiliki kelas aktif.
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