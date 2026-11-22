@php
    /**
     * Menentukan struktur navigasi berdasarkan role pengguna.
     *
     * Setiap kelompok menu memiliki:
     * - label: Nama kategori navigasi.
     * - items: Daftar menu yang tersedia dalam kategori tersebut.
     *
     * Setiap item menu terdiri dari:
     * [Nama menu, nama route, pola route aktif].
     *
     * @var array<string, array<int, array<string, mixed>>> $menus
     */
    $role = auth()->user()->role;

    $menus = [
        'admin' => [
            [
                'label' => null,
                'items' => [
                    ['Dashboard', 'dashboard', 'dashboard'],
                    ['Pengumuman', 'announcements.board', 'announcements.board'],
                ],
            ],
            [
                'label' => 'Data master',
                'items' => [
                    ['Tahun ajaran', 'academic-years.index', 'academic-years.*'],
                    ['Semester', 'semesters.index', 'semesters.*'],
                    ['Siswa', 'students.index', 'students.*'],
                    ['Guru', 'teachers.index', 'teachers.*'],
                    ['Orang tua / wali', 'guardians.index', 'guardians.*'],
                    ['Kelas', 'classrooms.index', 'classrooms.*'],
                    ['Jurusan', 'majors.index', 'majors.*'],
                    ['Mata pelajaran', 'subjects.index', 'subjects.*'],
                ],
            ],
            [
                'label' => 'Akademik',
                'items' => [
                    ['Penempatan siswa', 'enrollments.index', 'enrollments.*'],
                    ['Penugasan guru', 'teaching-assignments.index', 'teaching-assignments.*'],
                    ['Jadwal pelajaran', 'schedules.index', 'schedules.*'],
                    ['Absensi', 'attendance.index', 'attendance.*'],
                    ['Komponen penilaian', 'components.index', 'components.*'],
                    ['Nilai siswa', 'scores.index', 'scores.*'],
                    ['Rapor', 'report-card', 'report-card'],
                    ['Kenaikan kelas', 'promotion.index', 'promotion.*'],
                ],
            ],
            [
                'label' => 'E-learning',
                'items' => [
                    ['Materi', 'materials.index', 'materials.*'],
                    ['Tugas', 'assignments.index', 'assignments.*'],
                    [
                        'Kelola pengumuman',
                        'announcements.index',
                        ['announcements.index', 'announcements.create', 'announcements.edit'],
                    ],
                ],
            ],
            [
                'label' => 'Laporan',
                'items' => [
                    ['Daftar siswa', 'reports.students', 'reports.students'],
                    ['Daftar guru', 'reports.teachers', 'reports.teachers'],
                    ['Rekap absensi', 'reports.attendance', 'reports.attendance'],
                    ['Rekap nilai', 'reports.scores', 'reports.scores'],
                ],
            ],
            [
                'label' => 'Pengaturan',
                'items' => [
                    ['Akun pengguna', 'users.index', 'users.*'],
                ],
            ],
        ],

        'guru' => [
            [
                'label' => null,
                'items' => [
                    ['Dashboard', 'dashboard', 'dashboard'],
                    ['Pengumuman', 'announcements.board', 'announcements.board'],
                ],
            ],
            [
                'label' => 'Mengajar',
                'items' => [
                    ['Jadwal mengajar', 'schedules.index', 'schedules.*'],
                    ['Absensi', 'attendance.index', 'attendance.*'],
                    ['Komponen penilaian', 'components.index', 'components.*'],
                    ['Nilai siswa', 'scores.index', 'scores.*'],
                ],
            ],
            [
                'label' => 'E-learning',
                'items' => [
                    ['Materi', 'materials.index', 'materials.*'],
                    ['Tugas', 'assignments.index', 'assignments.*'],
                ],
            ],
            [
                'label' => 'Laporan',
                'items' => [
                    ['Daftar siswa', 'reports.students', 'reports.students'],
                    ['Rekap absensi', 'reports.attendance', 'reports.attendance'],
                    ['Rekap nilai', 'reports.scores', 'reports.scores'],
                ],
            ],
        ],

        'siswa' => [
            [
                'label' => null,
                'items' => [
                    ['Dashboard', 'dashboard', 'dashboard'],
                    ['Pengumuman', 'announcements.board', 'announcements.board'],
                ],
            ],
            [
                'label' => 'Belajar',
                'items' => [
                    ['Jadwal', 'me.schedule', 'me.schedule'],
                    ['Materi', 'me.materials', 'me.materials'],
                    ['Tugas', 'me.assignments', 'me.assignments*'],
                    ['Nilai', 'me.scores', 'me.scores'],
                    ['Absensi', 'me.attendance', 'me.attendance'],
                    ['Rapor', 'report-card', 'report-card'],
                ],
            ],
        ],
    ];

    /**
     * Role orang tua menggunakan struktur navigasi yang sama
     * dengan role siswa.
     */
    $menus['ortu'] = $menus['siswa'];

    /**
     * Mengambil kelompok navigasi sesuai role pengguna.
     * Jika role tidak ditemukan, navigasi ditampilkan dalam keadaan kosong.
     *
     * @var array<int, array<string, mixed>> $groups
     */
    $groups = $menus[$role] ?? [];
@endphp


{{-- Sidebar utama aplikasi --}}
<aside
    id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-72 shrink-0 -translate-x-full flex-col overflow-y-auto border-r border-line bg-white text-board-900 shadow-xl transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none print:hidden"
>
    {{-- Identitas dan logo aplikasi --}}
    <div class="flex h-20 shrink-0 items-center gap-3 border-b border-line px-6">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-board-800">
            <span class="font-display text-xl font-bold text-pencil-400">
                S
            </span>
        </div>

        <div class="min-w-0">
            <h2 class="font-display text-2xl font-semibold tracking-wide text-board-900">
                SIAKAD
            </h2>

            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-board-500">
                Academic System
            </p>
        </div>
    </div>

    {{-- Daftar navigasi berdasarkan role pengguna --}}
    <nav class="flex-1 space-y-6 px-4 py-6 text-sm">
        @foreach ($groups as $group)
            <div>
                {{-- Label kategori menu --}}
                @if ($group['label'])
                    <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-board-400">
                        {{ $group['label'] }}
                    </p>
                @endif

                <ul class="space-y-1">
                    @foreach ($group['items'] as [$label, $route, $pattern])
                        @php
                            $active = request()->routeIs($pattern);
                        @endphp

                        <li>
                            <a
                                href="{{ route($route) }}"
                                @if ($active) aria-current="page" @endif
                                class="group flex min-h-10 items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition-colors duration-200
                                    {{ $active
                                        ? 'bg-board-100 text-board-800'
                                        : 'text-board-600 hover:bg-board-50 hover:text-board-900' }}"
                            >
                                @if ($active)
                                    <span
                                        class="h-5 w-1 shrink-0 rounded-full bg-board-600"
                                        aria-hidden="true"
                                    ></span>
                                @else
                                    <span
                                        class="h-5 w-1 shrink-0 rounded-full bg-transparent"
                                        aria-hidden="true"
                                    ></span>
                                @endif

                                <span class="truncate">
                                    {{ $label }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    {{-- Informasi versi aplikasi --}}
    <div class="shrink-0 border-t border-line px-6 py-4">
        <p class="text-xs font-semibold text-board-700">
            SIAKAD V2
        </p>

        <p class="mt-1 text-[10px] text-board-400">
            Sistem Informasi Akademik
        </p>
    </div>
</aside>

{{-- Overlay untuk interaksi sidebar pada perangkat mobile --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 z-40 hidden bg-board-900/30 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 lg:hidden"
    aria-hidden="true"
></div>