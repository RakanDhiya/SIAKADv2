@php
    $role = auth()->user()->role;

    $menus = [
        'admin' => [
            ['label' => null, 'items' => [
                ['Dashboard', 'dashboard', 'dashboard'],
                ['Pengumuman', 'announcements.board', 'announcements.board'],
            ]],
            ['label' => 'Data master', 'items' => [
                ['Tahun ajaran', 'academic-years.index', 'academic-years.*'],
                ['Semester', 'semesters.index', 'semesters.*'],
                ['Siswa', 'students.index', 'students.*'],
                ['Guru', 'teachers.index', 'teachers.*'],
                ['Orang tua / wali', 'guardians.index', 'guardians.*'],
                ['Kelas', 'classrooms.index', 'classrooms.*'],
                ['Jurusan', 'majors.index', 'majors.*'],
                ['Mata pelajaran', 'subjects.index', 'subjects.*'],
            ]],
            ['label' => 'Akademik', 'items' => [
                ['Penempatan siswa', 'enrollments.index', 'enrollments.*'],
                ['Penugasan guru', 'teaching-assignments.index', 'teaching-assignments.*'],
                ['Jadwal pelajaran', 'schedules.index', 'schedules.*'],
                ['Absensi', 'attendance.index', 'attendance.*'],
                ['Komponen penilaian', 'components.index', 'components.*'],
                ['Nilai siswa', 'scores.index', 'scores.*'],
                ['Rapor', 'report-card', 'report-card'],
                ['Kenaikan kelas', 'promotion.index', 'promotion.*'],
            ]],
            ['label' => 'E-learning', 'items' => [
                ['Materi', 'materials.index', 'materials.*'],
                ['Tugas', 'assignments.index', 'assignments.*'],
                ['Kelola pengumuman', 'announcements.index', ['announcements.index', 'announcements.create', 'announcements.edit']],
            ]],
            ['label' => 'Laporan', 'items' => [
                ['Daftar siswa', 'reports.students', 'reports.students'],
                ['Daftar guru', 'reports.teachers', 'reports.teachers'],
                ['Rekap absensi', 'reports.attendance', 'reports.attendance'],
                ['Rekap nilai', 'reports.scores', 'reports.scores'],
            ]],
            ['label' => 'Pengaturan', 'items' => [
                ['Akun pengguna', 'users.index', 'users.*'],
            ]],
        ],
        'guru' => [
            ['label' => null, 'items' => [
                ['Dashboard', 'dashboard', 'dashboard'],
                ['Pengumuman', 'announcements.board', 'announcements.board'],
            ]],
            ['label' => 'Mengajar', 'items' => [
                ['Jadwal mengajar', 'schedules.index', 'schedules.*'],
                ['Absensi', 'attendance.index', 'attendance.*'],
                ['Komponen penilaian', 'components.index', 'components.*'],
                ['Nilai siswa', 'scores.index', 'scores.*'],
            ]],
            ['label' => 'E-learning', 'items' => [
                ['Materi', 'materials.index', 'materials.*'],
                ['Tugas', 'assignments.index', 'assignments.*'],
            ]],
            ['label' => 'Laporan', 'items' => [
                ['Daftar siswa', 'reports.students', 'reports.students'],
                ['Rekap absensi', 'reports.attendance', 'reports.attendance'],
                ['Rekap nilai', 'reports.scores', 'reports.scores'],
            ]],
        ],
        'siswa' => [
            ['label' => null, 'items' => [
                ['Dashboard', 'dashboard', 'dashboard'],
                ['Pengumuman', 'announcements.board', 'announcements.board'],
            ]],
            ['label' => 'Belajar', 'items' => [
                ['Jadwal', 'me.schedule', 'me.schedule'],
                ['Materi', 'me.materials', 'me.materials'],
                ['Tugas', 'me.assignments', 'me.assignments*'],
                ['Nilai', 'me.scores', 'me.scores'],
                ['Absensi', 'me.attendance', 'me.attendance'],
                ['Rapor', 'report-card', 'report-card'],
            ]],
        ],
    ];
    $menus['ortu'] = $menus['siswa'];
    $groups = $menus[$role] ?? [];
@endphp

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-40 w-64 shrink-0 -translate-x-full overflow-y-auto bg-board-800 text-board-100 transition-transform lg:static lg:translate-x-0 print:hidden">
    <div class="flex items-center gap-2.5 border-b border-board-700 px-5 py-4">
        <span class="inline-block h-6 w-6 rounded-sm bg-pencil-400" aria-hidden="true"></span>
        <span class="font-display text-xl font-semibold text-white">SIAKAD</span>
    </div>

    <nav class="space-y-5 px-3 py-4 text-sm">
        @foreach ($groups as $group)
            <div>
                @if ($group['label'])
                    <p class="px-2 pb-1 text-xs text-board-300">{{ $group['label'] }}</p>
                @endif
                <ul class="space-y-0.5">
                    @foreach ($group['items'] as [$label, $route, $pattern])
                        @php $active = request()->routeIs($pattern); @endphp
                        <li>
                            <a href="{{ route($route) }}"
                               @if ($active) aria-current="page" @endif
                               class="block rounded px-2.5 py-1.5 transition {{ $active ? 'bg-board-700 font-medium text-white ring-1 ring-pencil-400/60' : 'text-board-100 hover:bg-board-700/70' }}">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>
</aside>
