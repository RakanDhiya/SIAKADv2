<x-layouts.app :title="'Selamat datang, '.auth()->user()->name" :breadcrumbs="[]">

    @php
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $calendarStart = now()->startOfMonth();
        $calendarDays = $calendarStart->daysInMonth;
        $calendarOffset = $calendarStart->dayOfWeek;
        $calendarToday = now()->day;
        $calendarMonthLabel = $monthNames[now()->month].' '.now()->year;

        $eventCategoryColors = [
            'Akademik' => 'bg-[#E5EEE9] text-[#034C34] border-[#CCDCD3]',
            'Ujian' => 'bg-[#F3E7E1] text-[#985D40] border-[#E9D5CA]',
            'Libur' => 'bg-[#F5F6F7] text-[#666266] border-[#C2BEC0]',
            'Kegiatan Sekolah' => 'bg-[#E5EEE9] text-[#28664A] border-[#CCDCD3]',
            'Pembagian Rapor' => 'bg-[#F3E7E1] text-[#B5734F] border-[#E9D5CA]',
            'Lainnya' => 'bg-[#F5F6F7] text-[#034C34] border-[#C2BEC0]',
        ];

        $eventCategoryCalendarColors = [
            'Akademik' => [
                'day' => 'bg-[#E5EEE9] text-[#034C34]',
                'dot' => 'bg-[#159B82]',
            ],
            'Ujian' => [
                'day' => 'bg-[#F3E7E1] text-[#985D40]',
                'dot' => 'bg-[#B5734F]',
            ],
            'Libur' => [
                'day' => 'bg-[#F0F1F1] text-[#666266]',
                'dot' => 'bg-[#92989A]',
            ],
            'Kegiatan Sekolah' => [
                'day' => 'bg-[#E5EEE9] text-[#28664A]',
                'dot' => 'bg-[#28664A]',
            ],
            'Pembagian Rapor' => [
                'day' => 'bg-[#F3E7E1] text-[#B5734F]',
                'dot' => 'bg-[#C77A55]',
            ],
            'Lainnya' => [
                'day' => 'bg-[#F0F1F1] text-[#52635C]',
                'dot' => 'bg-[#8A958F]',
            ],
        ];

        $statAccents = [
            'Siswa aktif' => [
                'icon' => 'S',
                'background' => 'bg-[#E5EEE9]',
                'color' => 'text-[#034C34]',
                'line' => 'bg-[#034C34]',
            ],
            'Guru' => [
                'icon' => 'G',
                'background' => 'bg-[#F3E7E1]',
                'color' => 'text-[#B5734F]',
                'line' => 'bg-[#B5734F]',
            ],
            'Kelas' => [
                'icon' => 'K',
                'background' => 'bg-[#E5EEE9]',
                'color' => 'text-[#034C34]',
                'line' => 'bg-[#28664A]',
            ],
            'Mata pelajaran' => [
                'icon' => 'M',
                'background' => 'bg-[#F5F6F7]',
                'color' => 'text-[#034C34]',
                'line' => 'bg-[#C2BEC0]',
            ],
        ];
    @endphp

    {{-- GRID DASHBOARD: KONTEN UTAMA + PANEL KANAN --}}
    <div class="grid min-w-0 grid-cols-1 items-start xl:grid-cols-[minmax(0,1fr)_300px]">

        {{-- KONTEN UTAMA --}}
        <main class="order-2 min-w-0 space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8 xl:order-1 xl:pr-6">

            {{-- Section Sambutan --}}
            <section class="flex flex-col justify-between gap-5 py-1 sm:flex-row sm:items-center">
                <div class="min-w-0">
                    <p class="text-[10px] font-medium uppercase tracking-[0.14em] text-[#7D8C85]">
                        Sistem Informasi Akademik
                    </p>

                    <h2 class="mt-2 font-display text-2xl font-semibold tracking-tight text-[#173F34] sm:text-[28px]">
                        Selamat datang, {{ auth()->user()->name }}!
                    </h2>

                    <p class="mt-2 text-xs leading-relaxed text-[#7C8983] sm:text-sm">
                        Pantau aktivitas dan perkembangan akademik sekolah.
                    </p>
                </div>

                <div class="grid shrink-0 grid-cols-2 gap-2">
                    <div class="min-w-[125px] rounded-xl border border-[#E5EAE7] bg-white px-3.5 py-3">
                        <p class="text-[9px] font-medium uppercase tracking-[0.1em] text-[#91A099]">
                            Tahun ajaran
                        </p>
                        <p class="mt-1.5 text-xs font-semibold text-[#263F35]">
                            {{ $year->name ?? 'Belum diatur' }}
                        </p>
                    </div>

                    <div class="min-w-[125px] rounded-xl border border-[#E5EAE7] bg-white px-3.5 py-3">
                        <p class="text-[9px] font-medium uppercase tracking-[0.1em] text-[#91A099]">
                            Semester
                        </p>
                        <p class="mt-1.5 text-xs font-semibold text-[#263F35]">
                            {{ $semester->label ?? 'Belum diatur' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Pemilihan siswa untuk siswa dan orang tua --}}
            @if (auth()->user()->hasRole('siswa', 'ortu'))
                <section>
                    @include('modules.me.partials.student-picker', [
                        'students' => $students,
                        'student' => $student,
                        'action' => route('dashboard')
                    ])
                </section>
            @endif

            {{-- Statistik sekolah --}}
            <section>
                <div class="mb-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#B5734F]">
                        Overview
                    </p>

                    <div class="mt-1 flex flex-wrap items-end justify-between gap-2">
                        <h2 class="font-display text-2xl font-semibold tracking-tight text-[#034C34]">
                            Ringkasan Sekolah
                        </h2>

                        <span class="text-xs text-[#8A8587]">
                            Data terkini
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4 sm:gap-4">
                    @foreach ($counts as $label => $total)
                        @php
                            $accent = $statAccents[$label] ?? [
                                'icon' => '•',
                                'background' => 'bg-[#E5EEE9]',
                                'color' => 'text-[#034C34]',
                                'line' => 'bg-[#034C34]',
                            ];
                        @endphp

                        <article class="group min-w-0 rounded-xl border border-[#E5EAE7] bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:border-[#B8D9CD] hover:shadow-sm sm:p-5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-[#777276] sm:text-sm">
                                        {{ $label }}
                                    </p>

                                    <p class="mt-3 font-display text-3xl font-semibold tracking-tight text-[#124B3C] sm:text-4xl">
                                        {{ number_format($total, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div @class([
                                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold sm:h-10 sm:w-10',
                                    $accent['background'],
                                    $accent['color'],
                                ])>
                                    {{ $accent['icon'] }}
                                </div>
                            </div>

                            <div @class([
                                'mt-4 h-1 w-8 rounded-full transition-all duration-200 group-hover:w-14',
                                $accent['line'],
                            ])></div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- Ringkasan visual khusus Admin --}}
            @if (auth()->user()->hasRole('admin'))
                @php
                    $maxStudents = $classSummary->max('students_count') ?: 1;

                    $attendanceTotal = collect(\App\Models\Attendance::STATUSES)
                        ->keys()
                        ->sum(fn ($key) => (int) ($attendanceToday[$key] ?? 0));

                    $statusColors = [
                        'hadir' => '#159B82',
                        'izin' => '#F2BD4F',
                        'sakit' => '#F07862',
                        'alpa' => '#D65B5B',
                    ];
                @endphp

                <section class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                    {{-- Grafik siswa per kelas --}}
                    <article class="overflow-hidden rounded-xl border border-[#E4E1E2] bg-white">
                        <div class="flex items-center justify-between border-b border-[#E4E1E2] px-5 py-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5734F]">
                                    Class Overview
                                </p>

                                <h2 class="mt-1 font-display text-lg font-semibold text-[#034C34]">
                                    Siswa Aktif per Kelas
                                </h2>
                            </div>

                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#E5EEE9] text-[#034C34]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M3 21h18M5 21V7l7-4 7 4v14"/>
                                    <path d="M9 21v-5h6v5"/>
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-5 p-5">
                            @forelse ($classSummary as $c)
                                @php
                                    $percentage = round(($c->students_count / $maxStudents) * 100);
                                @endphp

                                <div>
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <span class="truncate text-xs font-medium text-[#555158]">
                                            {{ $c->name }}
                                        </span>

                                        <span class="shrink-0 text-xs font-semibold tabular-nums text-[#034C34]">
                                            {{ number_format($c->students_count, 0, ',', '.') }} siswa
                                        </span>
                                    </div>

                                    <div class="h-2 overflow-hidden rounded-full bg-[#F0F1F1]">
                                        <div
                                            class="h-full rounded-full bg-[#159B82] transition-all duration-500"
                                            style="width: {{ $percentage }}%"
                                        ></div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-10 text-center">
                                    <p class="text-sm text-[#034C34]">
                                        Belum ada data kelas.
                                    </p>
                                    <p class="mt-1 text-xs text-[#8A8587]">
                                        Data kelas akan ditampilkan setelah tersedia.
                                    </p>
                                </div>
                            @endforelse
                        </div>

                        <div class="border-t border-[#E4E1E2] bg-[#FAFAFA] px-5 py-3">
                            <p class="text-[10px] text-[#8A8587]">
                                Jumlah siswa berdasarkan kelas pada tahun ajaran aktif.
                            </p>
                        </div>
                    </article>

                    {{-- Grafik absensi --}}
                    <article class="overflow-hidden rounded-xl border border-[#E4E1E2] bg-white">
                        <div class="flex items-center justify-between border-b border-[#E4E1E2] px-5 py-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5734F]">
                                    Attendance Overview
                                </p>

                                <h2 class="mt-1 font-display text-lg font-semibold text-[#034C34]">
                                    Absensi Hari Ini
                                </h2>

                                <p class="mt-1 text-[10px] text-[#8A8587]">
                                    Rekap kehadiran siswa hari ini.
                                </p>
                            </div>

                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#E5EEE9] text-[#034C34]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M9 11l3 3 8-8"/>
                                    <path d="M20 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h10"/>
                                </svg>
                            </div>
                        </div>

                        <div class="p-5">
                            @if ($attendanceTotal > 0)
                                <div class="mb-5 rounded-xl bg-[#E5EEE9] p-4">
                                    <p class="text-xs font-medium text-[#28664A]">
                                        Total absensi tercatat
                                    </p>

                                    <div class="mt-1 flex items-end justify-between">
                                        <h3 class="font-display text-3xl font-semibold text-[#034C34]">
                                            {{ number_format($attendanceTotal, 0, ',', '.') }}
                                        </h3>

                                        <span class="text-[10px] text-[#28664A]">
                                            Data hari ini
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-5 flex h-3 overflow-hidden rounded-full bg-[#F0F1F1]">
                                    @foreach (\App\Models\Attendance::STATUSES as $key => $label)
                                        @php
                                            $count = (int) ($attendanceToday[$key] ?? 0);
                                            $percentage = ($count / $attendanceTotal) * 100;
                                            $color = $statusColors[$key] ?? '#159B82';
                                        @endphp

                                        @if ($count > 0)
                                            <div
                                                style="width: {{ $percentage }}%; background-color: {{ $color }}"
                                                title="{{ $label }}: {{ $count }}"
                                            ></div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    @foreach (\App\Models\Attendance::STATUSES as $key => $label)
                                        @php
                                            $count = (int) ($attendanceToday[$key] ?? 0);
                                            $percentage = ($count / $attendanceTotal) * 100;
                                            $color = $statusColors[$key] ?? '#159B82';
                                        @endphp

                                        <div class="flex items-center gap-2.5">
                                            <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $color }}"></span>

                                            <div class="min-w-0">
                                                <p class="text-[10px] text-[#777276]">
                                                    {{ $label }}
                                                </p>

                                                <p class="text-sm font-semibold text-[#034C34]">
                                                    {{ $count }}
                                                    <span class="text-[10px] font-normal text-[#8A8587]">
                                                        ({{ number_format($percentage, 1, ',', '.') }}%)
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex min-h-[220px] flex-col items-center justify-center text-center">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[#E5EEE9] text-[#034C34]">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M9 11l3 3 8-8"/>
                                            <path d="M20 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h10"/>
                                        </svg>
                                    </div>

                                    <p class="text-sm font-semibold text-[#034C34]">
                                        Belum ada data absensi
                                    </p>

                                    <p class="mt-1 max-w-xs text-xs leading-relaxed text-[#8A8587]">
                                        Rekap kehadiran siswa akan muncul setelah absensi dicatat.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="border-t border-[#E4E1E2] bg-[#FAFAFA] px-5 py-3">
                            <p class="text-[10px] text-[#8A8587]">
                                Statistik berdasarkan data absensi hari ini.
                            </p>
                        </div>
                    </article>
                </section>
            @endif

            {{-- Grafik absensi mingguan khusus Admin --}}
            @if (auth()->user()->hasRole('admin') && isset($weeklyAttendance))
                @php
                    $weeklyMax = $weeklyAttendance->max('total') ?: 1;
                    $weeklyTotal = $weeklyAttendance->sum('total');

                    $weeklyColors = [
                        'hadir' => '#159B82',
                        'izin' => '#F2BD4F',
                        'sakit' => '#F07862',
                        'alpa' => '#D65B5B',
                    ];
                @endphp

                <section class="overflow-hidden rounded-xl border border-[#E4E1E2] bg-white">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#E4E1E2] px-5 py-5">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5734F]">
                                Attendance Analytics
                            </p>

                            <h2 class="mt-1 font-display text-xl font-semibold text-[#034C34]">
                                Statistik Kehadiran Mingguan
                            </h2>

                            <p class="mt-1 text-xs text-[#8A8587]">
                                Rekap kehadiran siswa selama satu minggu.
                            </p>
                        </div>

                        <div class="min-w-[110px] rounded-xl bg-[#E5EEE9] px-4 py-3">
                            <p class="text-[10px] font-medium text-[#28664A]">
                                Total absensi
                            </p>
                            <p class="mt-1 font-display text-2xl font-semibold text-[#034C34]">
                                {{ number_format($weeklyTotal, 0, ',', '.') }}
                            </p>
                            <p class="mt-0.5 text-[9px] text-[#64816F]">
                                Minggu ini
                            </p>
                        </div>
                    </div>

                    <div class="p-5">
                        @if ($weeklyTotal > 0)
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-0 flex flex-col justify-between pb-9 pt-2">
                                    <div class="border-t border-dashed border-[#EAE8E9]"></div>
                                    <div class="border-t border-dashed border-[#EAE8E9]"></div>
                                    <div class="border-t border-dashed border-[#EAE8E9]"></div>
                                    <div class="border-t border-[#E4E1E2]"></div>
                                </div>

                                <div class="relative grid grid-cols-7 gap-1.5 sm:gap-4">
                                    @foreach ($weeklyAttendance as $day)
                                        @php
                                            $barHeight = ($day['total'] / $weeklyMax) * 100;
                                        @endphp

                                        <div class="flex min-w-0 flex-col items-center">
                                            <p class="mb-2 text-[10px] font-semibold tabular-nums text-[#034C34]">
                                                {{ $day['total'] }}
                                            </p>

                                            <div class="flex h-28 w-full items-end justify-center sm:h-44">
                                                <div
                                                    class="flex w-full max-w-10 flex-col-reverse overflow-hidden rounded-t-md bg-[#F5F6F7]"
                                                    style="height: {{ $barHeight }}%; min-height: {{ $day['total'] > 0 ? '5px' : '0' }}"
                                                    title="Total {{ $day['total'] }} absensi"
                                                >
                                                    @foreach ($day['statuses'] as $status => $count)
                                                        @if ($count > 0)
                                                            <div
                                                                style="height: {{ ($count / $day['total']) * 100 }}%; background-color: {{ $weeklyColors[$status] ?? '#159B82' }}"
                                                                title="{{ ucfirst($status) }}: {{ $count }}"
                                                            ></div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="mt-3 text-center">
                                                <p class="text-[10px] font-semibold text-[#555158]">
                                                    {{ $day['day'] }}
                                                </p>
                                                <p class="mt-0.5 text-[8px] text-[#A09A9D] sm:text-[9px]">
                                                    {{ $day['label'] ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-3 border-t border-[#E4E1E2] pt-4">
                                @foreach (\App\Models\Attendance::STATUSES as $key => $label)
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-sm" style="background-color: {{ $weeklyColors[$key] ?? '#159B82' }}"></span>
                                        <span class="text-[10px] font-medium text-[#777276]">
                                            {{ $label }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex min-h-[190px] flex-col items-center justify-center text-center">
                                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[#E5EEE9] text-[#034C34]">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M3 3v18h18"/>
                                        <path d="m7 14 4-4 4 3 6-7"/>
                                    </svg>
                                </div>

                                <p class="text-sm font-semibold text-[#034C34]">
                                    Belum ada data kehadiran mingguan
                                </p>

                                <p class="mt-1 max-w-xs text-xs leading-relaxed text-[#8A8587]">
                                    Grafik akan muncul setelah absensi siswa dicatat.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Ringkasan tambahan Guru --}}
            @foreach ($extra as $label => $total)
                <article class="rounded-xl border border-[#E4E1E2] bg-white p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5734F]">
                        Teaching Overview
                    </p>

                    <p class="mt-2 text-sm font-medium text-[#777276]">
                        {{ $label }}
                    </p>

                    <p class="mt-2 font-display text-3xl font-semibold text-[#034C34]">
                        {{ number_format($total, 0, ',', '.') }}
                    </p>
                </article>
            @endforeach

            {{-- Tugas yang belum dikumpulkan --}}
            @if (isset($pendingTasks))
                <section class="overflow-hidden rounded-xl border border-[#E4E1E2] bg-white">
                    <div class="border-b border-[#E4E1E2] px-5 py-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5734F]">
                            Learning Progress
                        </p>

                        <h2 class="mt-1.5 font-display text-xl font-semibold text-[#034C34]">
                            Tugas yang Belum Dikumpulkan
                        </h2>

                        <p class="mt-1.5 text-xs text-[#777276]">
                            Daftar tugas yang masih menunggu pengumpulan.
                        </p>
                    </div>

                    @if ($pendingTasks->isEmpty())
                        <div class="px-5 py-9 text-center">
                            <p class="text-sm text-[#777276]">
                                Tidak ada tugas yang menunggu.
                            </p>
                        </div>
                    @else
                        <ul class="divide-y divide-[#E4E1E2]">
                            @foreach ($pendingTasks as $t)
                                <li class="flex flex-col gap-2 px-5 py-4 transition-colors hover:bg-[#F5F6F7] sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <a
                                            class="font-medium text-[#034C34] transition-colors hover:text-[#B5734F] hover:underline"
                                            href="{{ route('me.assignments.show', [$t->id, 'student' => $student?->id]) }}"
                                        >
                                            {{ $t->title }}
                                        </a>

                                        <p class="mt-1 text-xs text-[#777276]">
                                            {{ $t->teachingAssignment->subject->name }}
                                        </p>
                                    </div>

                                    <span class="shrink-0 text-xs text-[#8A8587]">
                                        {{ $t->due_at?->format('d/m H:i') ?? 'Tanpa tenggat' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endif

            {{-- Informasi akademik --}}
            <div class="grid grid-cols-1 items-stretch gap-5 xl:grid-cols-2">

                {{-- Agenda akademik --}}
                <section class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-[#E5EAE7] bg-white shadow-[0_2px_8px_rgba(23,63,52,0.025)]">

                    {{-- Header --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#EEF1EF] px-5 py-5 sm:px-6">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#B5734F]"></span>
                                <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-[#B5734F]">
                                    School Activities
                                </p>
                            </div>

                            <h2 class="mt-2 font-display text-lg font-semibold tracking-tight text-[#034C34] sm:text-xl">
                                Agenda Akademik Terdekat
                            </h2>

                            <p class="mt-1.5 text-[11px] leading-relaxed text-[#89938D] sm:text-xs">
                                Agenda dan kegiatan sekolah pada tahun ajaran aktif.
                            </p>
                        </div>

                        <a
                            href="{{ route('academic-calendar.index') }}"
                            class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-[#D5E4DC] bg-[#EDF4F0] px-3 py-2 text-[10px] font-semibold text-[#034C34] transition hover:border-[#B8D2C4] hover:bg-[#E2EEE7] sm:text-xs"
                        >
                            Lihat Kalender
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>

                    {{-- Daftar agenda --}}
                    @if ($upcomingEvents->isEmpty())
                        <div class="flex flex-1 flex-col items-center justify-center px-6 py-10 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EDF4F0] text-[#28664A]">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                                    <path d="M16 3v4M8 3v4M3 11h18"/>
                                    <path d="m9 16 2 2 4-4"/>
                                </svg>
                            </div>

                            <h3 class="mt-4 text-sm font-semibold text-[#034C34]">
                                Belum ada agenda akademik
                            </h3>

                            <p class="mt-1 max-w-xs text-[11px] leading-relaxed text-[#89938D]">
                                Agenda kegiatan sekolah akan muncul di sini setelah ditambahkan ke kalender akademik.
                            </p>

                            <a
                                href="{{ route('academic-calendar.index') }}"
                                class="mt-4 text-xs font-semibold text-[#28664A] transition hover:text-[#B5734F]"
                            >
                                Buka Kalender Akademik →
                            </a>
                        </div>
                    @else
                        <div class="flex-1 divide-y divide-[#EEF1EF]">
                            @foreach ($upcomingEvents as $event)
                                <article class="group flex flex-col gap-4 px-5 py-5 transition-colors hover:bg-[#FCFDFC] sm:flex-row sm:items-start sm:px-6">

                                    {{-- Date card --}}
                                    <div class="flex shrink-0 items-center gap-3 rounded-xl border border-[#E5EAE7] bg-[#F7F9F8] px-3 py-2.5 sm:w-[100px] sm:flex-col sm:items-center sm:justify-center sm:gap-1.5 sm:px-2 sm:py-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#E5EEE9] font-display text-xl font-semibold text-[#034C34] sm:h-auto sm:w-auto sm:bg-transparent sm:text-[30px] sm:leading-none">
                                            {{ $event->start_date->format('d') }}
                                        </div>

                                        <div class="min-w-0 sm:text-center">
                                            <p class="text-xs font-semibold text-[#034C34]">
                                                {{ $event->start_date->translatedFormat('F') }}
                                            </p>

                                            <p class="text-[10px] text-[#7F8B85]">
                                                {{ $event->start_date->format('Y') }}
                                            </p>

                                            <p class="mt-1 hidden border-t border-[#E1E8E3] pt-1.5 text-[10px] text-[#89938D] sm:block">
                                                @if ($event->start_date->isSameDay($event->end_date))
                                                    {{ $event->start_date->translatedFormat('l') }}
                                                @else
                                                    {{ $event->start_date->format('d/m') }}
                                                    – {{ $event->end_date->format('d/m') }}
                                                @endif
                                            </p>
                                        </div>

                                        <span class="ml-auto text-[10px] text-[#89938D] sm:hidden">
                                            {{ $event->start_date->translatedFormat('l') }}
                                        </span>
                                    </div>

                                    {{-- Event information --}}
                                    <div class="min-w-0 flex-1">

                                        {{-- Category and status --}}
                                        <div class="mb-2.5 flex flex-wrap items-center gap-2">
                                            <span @class([
                                                'inline-flex items-center rounded-md border px-2 py-1 text-[9px] font-semibold',
                                                $eventCategoryColors[$event->category] ?? $eventCategoryColors['Lainnya'],
                                            ])>
                                                {{ $event->category }}
                                            </span>

                                            @if ($event->start_date->lte(today()) && $event->end_date->gte(today()))
                                                <span class="inline-flex items-center gap-1.5 rounded-md bg-[#E5F4F0] px-2 py-1 text-[9px] font-semibold text-[#16816B]">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-[#159B82]"></span>
                                                    Sedang berlangsung
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Title --}}
                                        <h3 class="text-sm font-semibold leading-snug text-[#174B3B] transition-colors group-hover:text-[#28664A]">
                                            {{ $event->title }}
                                        </h3>

                                        {{-- Academic year --}}
                                        <p class="mt-2 text-[10px] leading-relaxed text-[#89938D]">
                                            Tahun Ajaran
                                            <span class="font-medium text-[#65746C]">
                                                {{ $event->academicYear->name ?? '-' }}
                                            </span>

                                            @if ($event->semester)
                                                <span class="mx-1 text-[#C2C9C4]">·</span>
                                                Semester
                                                <span class="font-medium text-[#65746C]">
                                                    {{ $event->semester->name }}
                                                </span>
                                            @endif
                                        </p>

                                        {{-- Location --}}
                                        @if ($event->location)
                                            <div class="mt-2 flex items-center gap-1.5 text-[10px] text-[#7F8B85]">
                                                <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                                    <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                                    <circle cx="12" cy="10" r="2.5"/>
                                                </svg>
                                                <span class="truncate">{{ $event->location }}</span>
                                            </div>
                                        @endif

                                        {{-- Description --}}
                                        @if ($event->description)
                                            <p class="mt-2.5 text-[11px] leading-relaxed text-[#89938D]">
                                                {{ \Illuminate\Support\Str::limit($event->description, 140) }}
                                            </p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Pengumuman terbaru --}}
                <section class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-[#E5EAE7] bg-white shadow-[0_2px_8px_rgba(23,63,52,0.025)]">

                    {{-- Header --}}
                    <div class="flex items-center justify-between gap-3 border-b border-[#EEF1EF] px-5 py-5 sm:px-6">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#B5734F]"></span>
                                <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-[#B5734F]">
                                    Latest Information
                                </p>
                            </div>

                            <h2 class="mt-2 font-display text-lg font-semibold tracking-tight text-[#034C34] sm:text-xl">
                                Pengumuman Terbaru
                            </h2>

                            <p class="mt-1.5 text-[11px] text-[#89938D] sm:text-xs">
                                Informasi penting dari pihak sekolah.
                            </p>
                        </div>

                        <span class="shrink-0 rounded-lg bg-[#EDF4F0] px-3 py-2 text-[10px] font-semibold text-[#28664A] sm:text-xs">
                            {{ $announcements->count() }} Pengumuman
                        </span>
                    </div>

                    {{-- Daftar pengumuman --}}
                    @forelse ($announcements as $a)
                        <article class="group border-b border-[#EEF1EF] px-5 py-5 transition-colors last:border-0 hover:bg-[#FCFDFC] sm:px-6">
                            <div class="flex items-start gap-3.5">

                                {{-- Announcement icon --}}
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E5EEE9] text-[#28664A] transition-colors group-hover:bg-[#DCEBE3]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 16v-4"/>
                                        <path d="M12 8h.01"/>
                                        <circle cx="12" cy="12" r="9"/>
                                    </svg>
                                </div>

                                {{-- Announcement content --}}
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-semibold leading-snug text-[#174B3B] transition-colors group-hover:text-[#28664A]">
                                        {{ $a->title }}
                                    </h3>

                                    <div class="mt-2 flex items-center gap-1.5 text-[10px] text-[#9AA39E]">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                                            <path d="M16 3v4M8 3v4M3 11h18"/>
                                        </svg>

                                        {{ $a->published_at?->format('d/m/Y H:i') ?? 'Tanggal tidak tersedia' }}
                                    </div>

                                    <p class="mt-3 text-xs leading-[1.8] text-[#737F78]">
                                        {{ \Illuminate\Support\Str::limit($a->body, 180) }}
                                    </p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EDF4F0] text-[#28664A]">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                    <circle cx="12" cy="12" r="9"/>
                                </svg>
                            </div>

                            <h3 class="mt-4 text-sm font-semibold text-[#034C34]">
                                Belum ada pengumuman
                            </h3>

                            <p class="mt-1 max-w-xs text-[11px] leading-relaxed text-[#89938D]">
                                Informasi terbaru dari sekolah akan ditampilkan di bagian ini.
                            </p>
                        </div>
                    @endforelse
                </section>
            </div>
        </main>

        {{-- PANEL KANAN: KALENDER + JADWAL --}}
        <aside class="order-1 mx-3 my-3 min-w-0 rounded-2xl border border-[#E4E1E2] bg-white px-4 py-5 shadow-sm sm:mx-6 sm:px-6 xl:sticky xl:top-[84px] xl:order-2 xl:mx-3 xl:my-5 xl:h-[calc(100vh-108px)] xl:overflow-y-auto xl:px-5 xl:py-6">

            {{-- Kalender akademik --}}
            <section class="border-b border-[#E9EEEB] pb-5">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <h2 class="truncate text-base font-semibold tracking-tight text-[#202124]">
                            {{ now()->translatedFormat('F, j l') }}
                        </h2>

                        <p class="mt-0.5 text-[10px] text-[#8A8587]">
                            {{ $calendarMonthLabel }}
                        </p>
                    </div>

                    <a
                        href="{{ route('academic-calendar.index') }}"
                        aria-label="Buka kalender akademik"
                        title="Buka kalender akademik"
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[#777276] transition hover:bg-[#F5F6F7] hover:text-[#034C34]"
                    >
                        <span aria-hidden="true">›</span>
                    </a>
                </div>

                {{-- Grid kalender --}}
                <div class="grid grid-cols-7 gap-y-2 text-center">
                    @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                        <div class="py-1.5 text-[9px] font-medium text-[#8A8587]">
                            {{ $dayName }}
                        </div>
                    @endforeach

                    @for ($blank = 0; $blank < $calendarOffset; $blank++)
                        <div class="aspect-square"></div>
                    @endfor

                    @for ($day = 1; $day <= $calendarDays; $day++)
                        @php
                            $date = $calendarStart->copy()->addDays($day - 1);
                            $isToday = $day === $calendarToday;

                            $dayEvents = $calendarEvents->filter(
                                fn ($event) =>
                                    $event->start_date->toDateString() <= $date->toDateString()
                                    && $event->end_date->toDateString() >= $date->toDateString()
                            );

                            $dayCategories = $dayEvents
                                ->pluck('category')
                                ->unique()
                                ->values();

                            $hasEvent = $dayCategories->isNotEmpty();

                            $firstCategory = $dayCategories->first();

                            $eventDayColor = $eventCategoryCalendarColors[$firstCategory]
                                ?? $eventCategoryCalendarColors['Lainnya'];
                        @endphp

                        <div class="flex aspect-square items-center justify-center">
                            <div class="flex h-9 w-full flex-col items-center justify-center">
                                <span
                                    @class([
                                        'flex h-7 w-7 items-center justify-center rounded-full text-[10px] font-medium transition',
                                        'bg-[#159B82] font-bold text-white shadow-sm' => $isToday,
                                        $eventDayColor['day'] . ' font-bold' => !$isToday && $hasEvent,
                                        'text-[#303238] hover:bg-[#F5F6F7]' => !$isToday && !$hasEvent,
                                    ])
                                >
                                    {{ $day }}
                                </span>

                                {{-- Indikator kategori agenda --}}
                                @if ($hasEvent)
                                    <span class="mt-0.5 flex h-1 items-center justify-center gap-0.5">
                                        @foreach ($dayCategories->take(3) as $category)
                                            <span
                                                class="h-1 w-1 rounded-full {{ $eventCategoryCalendarColors[$category]['dot'] ?? $eventCategoryCalendarColors['Lainnya']['dot'] }}"
                                                title="{{ $category }}"
                                            ></span>
                                        @endforeach
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Legenda warna kalender --}}
                <div class="mt-4 border-t border-[#F0F1F1] pt-3">
                    <p class="mb-2.5 text-[9px] font-semibold uppercase tracking-[0.12em] text-[#89938F]">
                        Keterangan Agenda
                    </p>

                    <div class="grid grid-cols-2 gap-x-2.5 gap-y-2">
                        @foreach ($eventCategoryCalendarColors as $category => $colors)
                            <div class="flex min-w-0 items-center gap-2">
                                <span class="h-2 w-2 shrink-0 rounded-full {{ $colors['dot'] }}"></span>

                                <span class="truncate text-[9px] font-medium text-[#64736C]">
                                    {{ $category }}
                                </span>
                            </div>
                        @endforeach

                        <div class="col-span-2 flex items-center gap-2 border-t border-[#F0F1F1] pt-2">
                            <span class="h-2 w-2 shrink-0 rounded-full bg-[#159B82]"></span>

                            <span class="text-[9px] font-medium text-[#64736C]">
                                Hari ini
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Jadwal hari ini --}}
            <section class="pt-5">
                <div class="mb-3">
                    <h2 class="text-base font-semibold tracking-tight text-[#202124]">
                        Today's Schedule
                    </h2>

                    <p class="mt-1 text-[10px] text-[#8A8587]">
                        {{ \App\Models\Schedule::DAYS[$today] ?? 'Minggu' }},
                        {{ now()->translatedFormat('d F Y') }}
                    </p>
                </div>

                @if ($todaySchedule->isEmpty())
                    <div class="rounded-xl bg-[#F5F6F7] px-3 py-5 text-center">
                        <p class="text-xs font-medium text-[#034C34]">
                            Belum ada jadwal hari ini
                        </p>

                        <p class="mt-1 text-[10px] leading-relaxed text-[#777276]">
                            Jadwal pelajaran untuk hari ini belum tersedia.
                        </p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach ($todaySchedule as $index => $s)
                            @php
                                $scheduleColors = [
                                    ['bg' => 'bg-[#159B82]', 'soft' => 'bg-[#E5F4F0]'],
                                    ['bg' => 'bg-[#F2BD4F]', 'soft' => 'bg-[#FFF6DF]'],
                                    ['bg' => 'bg-[#F07862]', 'soft' => 'bg-[#FDECE8]'],
                                    ['bg' => 'bg-[#159B82]', 'soft' => 'bg-[#E5F4F0]'],
                                ];

                                $scheduleColor = $scheduleColors[$index % count($scheduleColors)];
                            @endphp

                            <article class="flex min-w-0 items-stretch overflow-hidden rounded-lg bg-[#F7F8F9]">
                                <div @class([
                                    'flex w-1 shrink-0',
                                    $scheduleColor['bg'],
                                ])></div>

                                <div class="flex min-w-0 flex-1 items-start gap-2.5 px-2.5 py-3">
                                    <div @class([
                                        'flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white',
                                        $scheduleColor['bg'],
                                    ])>
                                        {{ mb_substr($s->teachingAssignment->subject->name ?? 'M', 0, 1) }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h3 class="truncate text-[11px] font-semibold text-[#303238]">
                                            {{ $s->teachingAssignment->subject->name ?? 'Mata pelajaran' }}
                                        </h3>

                                        <p class="mt-1 text-[9px] text-[#777276]">
                                            {{ $s->time_range }}
                                        </p>

                                        @if (auth()->user()->hasRole('admin'))
                                            <p class="mt-1 truncate text-[9px] text-[#8A8587]">
                                                {{ $s->teachingAssignment->classroom->name ?? '-' }}
                                                <span class="mx-0.5">·</span>
                                                {{ $s->teachingAssignment->teacher->name ?? '-' }}
                                            </p>
                                        @elseif (auth()->user()->hasRole('guru'))
                                            <p class="mt-1 truncate text-[9px] text-[#8A8587]">
                                                {{ $s->teachingAssignment->classroom->name ?? '-' }}
                                            </p>
                                        @else
                                            <p class="mt-1 truncate text-[9px] text-[#8A8587]">
                                                {{ $s->teachingAssignment->teacher->name ?? '-' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </aside>
    </div>
</x-layouts.app>