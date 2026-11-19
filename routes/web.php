<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssessmentComponentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\LearningMaterialController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeachingAssignmentController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureRole;
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;

// ---------- Tamu ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// ---------- Semua pengguna yang sudah login ----------
Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/akun/password', [AuthController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/akun/password', [AuthController::class, 'updatePassword'])->name('account.password.update');

    Route::get('/pengumuman', function () {
        $items = Announcement::visibleTo(auth()->user())->latest('published_at')->paginate(10);

        return view('modules.announcements.board', compact('items'));
    })->name('announcements.board');

    // ---------- Admin saja ----------
    Route::middleware(EnsureRole::class.':admin')->prefix('admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('academic-years', AcademicYearController::class)->except('show');
        Route::resource('semesters', SemesterController::class)->except('show');
        Route::resource('majors', MajorController::class)->except('show');
        Route::resource('subjects', SubjectController::class)->except('show');
        Route::resource('students', StudentController::class);
        Route::resource('teachers', TeacherController::class);
        Route::resource('guardians', GuardianController::class);
        Route::resource('classrooms', ClassroomController::class)->except('show');
        Route::resource('enrollments', EnrollmentController::class)->except('show');
        Route::resource('teaching-assignments', TeachingAssignmentController::class)->except('show');
        Route::resource('announcements', AnnouncementController::class)->except('show');

        // Jadwal: tulis hanya admin (daftar bisa dilihat guru, lihat di bawah)
        Route::resource('schedules', ScheduleController::class)->except(['index', 'show']);

        Route::get('promotion', [PromotionController::class, 'index'])->name('promotion.index');
        Route::post('promotion', [PromotionController::class, 'store'])->name('promotion.store');

        Route::get('reports/teachers', [ReportController::class, 'teachers'])->name('reports.teachers');
    });

    // ---------- Admin dan Guru ----------
    Route::middleware(EnsureRole::class.':admin,guru')->group(function () {
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');

        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');

        Route::resource('components', AssessmentComponentController::class)->except('show');
        Route::get('scores', [ScoreController::class, 'index'])->name('scores.index');
        Route::post('scores', [ScoreController::class, 'store'])->name('scores.store');
        Route::post('scores/compute', [ScoreController::class, 'compute'])->name('scores.compute');

        Route::resource('materials', LearningMaterialController::class)->except('show');
        Route::resource('assignments', AssignmentController::class)->except('show');
        Route::get('assignments/{assignment}/submissions', [SubmissionController::class, 'index'])->name('assignments.submissions');
        Route::post('assignments/{assignment}/submissions', [SubmissionController::class, 'grade'])->name('assignments.submissions.grade');

        Route::get('reports/students', [ReportController::class, 'students'])->name('reports.students');
        Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('reports/scores', [ReportController::class, 'scores'])->name('reports.scores');
    });

    // ---------- Siswa dan Orang Tua ----------
    Route::middleware(EnsureRole::class.':siswa,ortu')->prefix('saya')->group(function () {
        Route::get('jadwal', [MeController::class, 'schedule'])->name('me.schedule');
        Route::get('nilai', [MeController::class, 'scores'])->name('me.scores');
        Route::get('absensi', [MeController::class, 'attendance'])->name('me.attendance');
        Route::get('materi', [MeController::class, 'materials'])->name('me.materials');
        Route::get('tugas', [MeController::class, 'assignments'])->name('me.assignments');
        Route::get('tugas/{assignment}', [MeController::class, 'assignment'])->name('me.assignments.show');
    });

    // Mengumpulkan tugas: hanya siswa
    Route::post('saya/tugas/{assignment}', [MeController::class, 'submit'])
        ->middleware(EnsureRole::class.':siswa')->name('me.assignments.submit');

    // Rapor: admin, siswa (miliknya), orang tua (anaknya)
    Route::get('rapor', [ReportCardController::class, 'show'])
        ->middleware(EnsureRole::class.':admin,siswa,ortu')->name('report-card');
});