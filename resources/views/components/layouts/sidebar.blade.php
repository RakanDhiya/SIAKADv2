@php
    /**
     * Struktur navigasi berdasarkan role pengguna.
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

    $menus['ortu'] = $menus['siswa'];

    $groups = $menus[$role] ?? [];
@endphp

{{-- Sidebar utama --}}
<aside
    id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex h-screen w-[240px] shrink-0 -translate-x-full flex-col overflow-hidden border-r border-[#E8ECE9] bg-white text-[#52635C] shadow-xl transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:translate-x-0 lg:shadow-none print:hidden"
>
    {{-- Brand --}}
    <div class="flex h-[68px] shrink-0 items-center gap-2.5 border-b border-[#E8ECE9] px-5">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#159B82] text-white">
            <span class="text-sm font-bold">S</span>
        </div>

        <div class="min-w-0">
            <h2 class="font-display text-[20px] font-semibold leading-none tracking-tight text-[#159B82]">
                SIAKAD
            </h2>

            <p class="mt-1 text-[8px] font-medium uppercase tracking-[0.16em] text-[#8B9992]">
                Academic System
            </p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav
        class="sidebar-scrollbar min-h-0 flex-1 overflow-y-auto px-3 py-5"
        aria-label="Navigasi utama"
    >
        @foreach ($groups as $group)
            <div class="mb-5 last:mb-2">

                {{-- Label kategori --}}
                @if ($group['label'])
                    <p class="mb-2.5 px-3 pt-2 text-[9px] font-semibold uppercase tracking-[0.16em] text-[#91A099]">
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
                                class="group flex min-h-[40px] items-center gap-3 rounded-lg px-3 py-2 transition-all duration-200
                                    {{ $active
                                        ? 'bg-[#159B82] text-white shadow-sm'
                                        : 'text-[#64736C] hover:bg-[#EDF6F2] hover:text-[#12836E]' }}"
                            >
                                {{-- Icon --}}
                                <span class="flex h-[18px] w-[18px] shrink-0 items-center justify-center">

                                    @switch($label)

                                        @case('Dashboard')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                                                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                                                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                                                <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                                            </svg>
                                            @break

                                        @case('Pengumuman')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M4 11V7a2 2 0 0 1 2-2h2l10-3v20L8 19H6a2 2 0 0 1-2-2v-2"/>
                                                <path d="M8 19l2 4"/>
                                            </svg>
                                            @break

                                        @case('Tahun ajaran')
                                        @case('Semester')
                                        @case('Mata pelajaran')
                                        @case('Komponen penilaian')
                                        @case('Materi')
                                        @case('Tugas')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <rect x="4" y="4" width="16" height="16" rx="2"/>
                                                <path d="M8 8h8M8 12h8M8 16h5"/>
                                            </svg>
                                            @break

                                        @case('Siswa')
                                        @case('Guru')
                                        @case('Orang tua / wali')
                                        @case('Daftar siswa')
                                        @case('Daftar guru')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <circle cx="12" cy="8" r="3.5"/>
                                                <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                                            </svg>
                                            @break

                                        @case('Kelas')
                                        @case('Jurusan')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M3 21h18M5 21V7l7-4 7 4v14"/>
                                                <path d="M9 21v-5h6v5"/>
                                            </svg>
                                            @break

                                        @case('Penempatan siswa')
                                        @case('Penugasan guru')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                <circle cx="10" cy="7" r="4"/>
                                                <path d="m17 11 2 2 4-4"/>
                                            </svg>
                                            @break

                                        @case('Jadwal')
                                        @case('Jadwal pelajaran')
                                        @case('Jadwal mengajar')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <rect x="3" y="5" width="18" height="16" rx="2"/>
                                                <path d="M16 3v4M8 3v4M3 10h18"/>
                                            </svg>
                                            @break

                                        @case('Absensi')
                                        @case('Rekap absensi')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="m9 11 3 3 8-8"/>
                                                <path d="M20 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h10"/>
                                            </svg>
                                            @break

                                        @case('Nilai siswa')
                                        @case('Nilai')
                                        @case('Rapor')
                                        @case('Rekap nilai')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M4 19V5M4 19h17"/>
                                                <path d="m7 15 4-4 3 2 6-7"/>
                                            </svg>
                                            @break

                                        @case('Kenaikan kelas')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M12 19V5"/>
                                                <path d="m6 11 6-6 6 6"/>
                                                <path d="M5 21h14"/>
                                            </svg>
                                            @break

                                        @case('Kelola pengumuman')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M12 3v18M3 12h18"/>
                                                <rect x="4" y="4" width="16" height="16" rx="2"/>
                                            </svg>
                                            @break

                                        @case('Akun pengguna')
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <circle cx="12" cy="8" r="3.5"/>
                                                <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                                            </svg>
                                            @break

                                        @default
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                <path d="M6 3h9l4 4v14H6z"/>
                                                <path d="M14 3v5h5M9 13h7M9 17h7"/>
                                            </svg>

                                    @endswitch
                                </span>

                                {{-- Label menu --}}
                                <span class="min-w-0 flex-1 truncate text-[11px] font-medium tracking-[0.01em]">
                                    {{ $label }}
                                </span>

                                {{-- Active indicator --}}
                                @if ($active)
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-white"></span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="shrink-0 border-t border-[#E8ECE9] px-5 py-4">
        <p class="text-[11px] font-semibold tracking-wide text-[#52635C]">
            SIAKAD V2
        </p>

        <p class="mt-1 text-[9px] text-[#98A39E]">
            Sistem Informasi Akademik
        </p>
    </div>
</aside>

{{-- Mobile overlay --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 z-40 hidden bg-slate-950/40 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 lg:hidden"
    aria-hidden="true"
></div>

{{-- Sidebar scrollbar --}}
<style>
    .sidebar-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #DCE5E0 transparent;
    }

    .sidebar-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-scrollbar::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #DCE5E0;
    }

    .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #B8C9BF;
    }
</style>