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
        $calendarOffset = $calendarStart->dayOfWeekIso - 1;
        $calendarToday = now()->day;
        $calendarMonthLabel = $monthNames[now()->month].' '.now()->year;

        $eventCategoryColors = [
            'Akademik' => 'bg-blue-50 text-blue-700 border-blue-200',
            'Ujian' => 'bg-rose-50 text-rose-700 border-rose-200',
            'Libur' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Kegiatan Sekolah' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Pembagian Rapor' => 'bg-violet-50 text-violet-700 border-violet-200',
            'Lainnya' => 'bg-board-50 text-board-700 border-board-200',
        ];
    @endphp

    {{-- Ringkasan data sekolah --}}
    <section class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($counts as $label => $total)
            <div class="rounded-xl border border-board-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-board-500">{{ $label }}</p>
                <p class="mt-2 font-display text-3xl font-semibold text-board-800">
                    {{ number_format($total, 0, ',', '.') }}
                </p>
                <div class="mt-3 h-1 w-12 rounded-full bg-pencil-400"></div>
            </div>
        @endforeach
    </section>

    {{-- Informasi tahun ajaran dan semester --}}
    <div class="mb-6 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-xl border border-board-200 bg-white px-5 py-4 shadow-sm">
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-board-500">Tahun Ajaran Aktif</p>
            <p class="mt-1 text-sm font-semibold text-board-800">
                {{ $year->name ?? 'Belum diatur' }}
            </p>
        </div>

        <div class="hidden h-9 w-px bg-board-200 sm:block"></div>

        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-board-500">Semester Aktif</p>
            <p class="mt-1 text-sm font-semibold text-board-800">
                {{ $semester->label ?? 'Belum diatur' }}
            </p>
        </div>
    </div>

    {{-- Pemilihan siswa untuk siswa dan orang tua --}}
    @if (auth()->user()->hasRole('siswa', 'ortu'))
        <div class="mb-6">
            @include('modules.me.partials.student-picker', [
                'students' => $students,
                'student' => $student,
                'action' => route('dashboard')
            ])
        </div>
    @endif

    {{-- Kalender dan Today Schedule --}}
    <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-5">

        {{-- Kalender bulanan --}}
        <div class="rounded-xl border border-board-200 bg-white p-5 shadow-sm xl:col-span-2">

            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-board-500">
                        Academic Calendar
                    </p>
                    <h2 class="mt-1 font-display text-xl font-semibold text-board-900">
                        {{ $calendarMonthLabel }}
                    </h2>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-board-100 text-board-700">
                    <span class="text-lg">▦</span>
                </div>
            </div>

            {{-- Hari dalam seminggu --}}
            <div class="grid grid-cols-7 gap-1 text-center">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                    <div class="py-2 text-xs font-semibold text-board-500">
                        {{ $dayName }}
                    </div>
                @endforeach

                {{-- Ruang kosong sebelum tanggal pertama --}}
                @for ($blank = 0; $blank < $calendarOffset; $blank++)
                    <div class="aspect-square"></div>
                @endfor

                {{-- Tanggal dalam bulan --}}
                @for ($day = 1; $day <= $calendarDays; $day++)
                    @php
                        $date = $calendarStart->copy()->addDays($day - 1);

                        $isToday = $day === $calendarToday;
                        $isSunday = $date->dayOfWeekIso === 7;

                        $hasEvent = $calendarEvents->contains(
                            fn ($event) =>
                                $event->start_date->toDateString() <= $date->toDateString()
                                && $event->end_date->toDateString() >= $date->toDateString()
                        );
                    @endphp

                    <div class="flex aspect-square flex-col items-center justify-center gap-0.5">
                        <span
                            @class([
                                'flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium transition',
                                'bg-board-700 text-white shadow-sm' => $isToday,
                                'text-rose-600' => $isSunday && !$isToday,
                                'text-board-700 hover:bg-board-100' => !$isToday && !$isSunday,
                            ])
                        >
                            {{ $day }}
                        </span>

                        @if ($hasEvent)
                            <span
                                @class([
                                    'h-1 w-1 rounded-full',
                                    'bg-pencil-300' => $isToday,
                                    'bg-pencil-500' => !$isToday,
                                ])
                            ></span>
                        @else
                            <span class="h-1 w-1"></span>
                        @endif
                    </div>
                @endfor
            </div>

            {{-- Keterangan kalender --}}
            <div class="mt-5 space-y-3 border-t border-board-200 pt-4">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-board-700"></span>
                        <span class="text-xs text-board-500">Hari ini</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-pencil-500"></span>
                        <span class="text-xs text-board-500">Ada agenda akademik</span>
                    </div>
                </div>

                <p class="text-xs font-medium text-board-600">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- Today Schedule --}}
        <div class="overflow-hidden rounded-xl border border-board-200 bg-white shadow-sm xl:col-span-3">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-board-200 px-5 py-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-board-500">
                        Daily Overview
                    </p>
                    <h2 class="mt-1 font-display text-xl font-semibold text-board-900">
                        Today Schedule
                    </h2>
                    <p class="mt-1 text-xs text-board-500">
                        {{ \App\Models\Schedule::DAYS[$today] ?? 'Minggu' }},
                        {{ now()->format('d/m/Y') }}
                    </p>
                </div>

                <span class="rounded-lg bg-board-100 px-3 py-2 text-xs font-semibold text-board-700">
                    {{ $todaySchedule->count() }} Jadwal
                </span>
            </div>

            @if ($todaySchedule->isEmpty())
                <div class="flex min-h-64 flex-col items-center justify-center px-5 py-10 text-center">
                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-board-50 text-2xl">
                        <span>▦</span>
                    </div>
                    <p class="font-semibold text-board-800">Belum ada jadwal hari ini</p>
                    <p class="mt-1 max-w-xs text-sm text-board-500">
                        Jadwal pelajaran untuk hari ini belum tersedia.
                    </p>
                </div>
            @else
                <div class="divide-y divide-board-100">
                    @foreach ($todaySchedule as $s)
                        <div class="flex flex-col gap-3 px-5 py-4 transition-colors hover:bg-board-50 sm:flex-row sm:items-center">

                            {{-- Waktu pelajaran --}}
                            <div class="shrink-0 sm:w-28">
                                <p class="text-sm font-bold text-board-800">
                                    {{ $s->time_range }}
                                </p>
                                <p class="mt-1 text-xs text-board-400">
                                    Jam pelajaran
                                </p>
                            </div>

                            {{-- Informasi mata pelajaran --}}
                            <div class="min-w-0 flex-1 border-l-2 border-board-200 pl-4">
                                <p class="truncate font-semibold text-board-800">
                                    {{ $s->teachingAssignment->subject->name ?? 'Mata pelajaran' }}
                                </p>

                                @if (auth()->user()->hasRole('admin'))
                                    <p class="mt-1 text-xs text-board-500">
                                        Kelas:
                                        {{ $s->teachingAssignment->classroom->name ?? '-' }}
                                    </p>
                                    <p class="mt-1 text-xs text-board-500">
                                        Guru:
                                        {{ $s->teachingAssignment->teacher->name ?? '-' }}
                                    </p>
                                @elseif (auth()->user()->hasRole('guru'))
                                    <p class="mt-1 text-xs text-board-500">
                                        Kelas:
                                        {{ $s->teachingAssignment->classroom->name ?? '-' }}
                                    </p>
                                @else
                                    <p class="mt-1 text-xs text-board-500">
                                        Guru:
                                        {{ $s->teachingAssignment->teacher->name ?? '-' }}
                                    </p>
                                @endif
                            </div>

                            <div class="hidden shrink-0 sm:block">
                                <span class="rounded-lg bg-board-50 px-3 py-2 text-xs font-medium text-board-600">
                                    {{ $s->day_name }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Agenda akademik terdekat --}}
    <section class="mb-6 overflow-hidden rounded-xl border border-board-200 bg-white shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-board-200 px-5 py-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-board-500">
                    School Activities
                </p>
                <h2 class="mt-1 font-display text-xl font-semibold text-board-900">
                    Agenda Akademik Terdekat
                </h2>
                <p class="mt-1 text-xs text-board-500">
                    Agenda dan kegiatan sekolah pada tahun ajaran aktif.
                </p>
            </div>

            <a
                href="{{ route('academic-calendar.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-board-200 bg-board-50 px-4 py-2.5 text-xs font-semibold text-board-700 transition hover:bg-board-100"
            >
                Lihat Kalender
                <span aria-hidden="true">→</span>
            </a>
        </div>

        @if ($upcomingEvents->isEmpty())
            <div class="flex flex-col items-center justify-center px-5 py-10 text-center">
                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-board-50 text-2xl text-board-500">
                    <span>▦</span>
                </div>

                <p class="font-semibold text-board-800">
                    Belum ada agenda akademik
                </p>

                <p class="mt-1 max-w-sm text-sm text-board-500">
                    Agenda sekolah yang akan datang akan ditampilkan di bagian ini.
                </p>
            </div>
        @else
            <div class="divide-y divide-board-100">
                @foreach ($upcomingEvents as $event)
                    <article class="flex flex-col gap-4 px-5 py-5 transition-colors hover:bg-board-50 sm:flex-row sm:items-start">

                        {{-- Tanggal agenda --}}
                        <div class="flex shrink-0 items-center gap-3 sm:w-36 sm:flex-col sm:items-start sm:gap-1 sm:rounded-xl sm:border sm:border-board-200 sm:bg-board-50 sm:px-4 sm:py-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-board-700 font-display text-lg font-semibold text-white sm:h-auto sm:w-full sm:rounded-none sm:bg-transparent sm:text-2xl sm:text-board-800">
                                {{ $event->start_date->format('d') }}
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-board-800">
                                    {{ $event->start_date->translatedFormat('F Y') }}
                                </p>

                                <p class="mt-0.5 text-xs text-board-500">
                                    @if ($event->start_date->isSameDay($event->end_date))
                                        {{ $event->start_date->translatedFormat('l') }}
                                    @else
                                        {{ $event->start_date->format('d/m/Y') }}
                                        – {{ $event->end_date->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Informasi agenda --}}
                        <div class="min-w-0 flex-1">
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <span @class([
                                    'inline-flex rounded-md border px-2.5 py-1 text-[11px] font-semibold',
                                    $eventCategoryColors[$event->category] ?? $eventCategoryColors['Lainnya'],
                                ])>
                                    {{ $event->category }}
                                </span>

                                @if ($event->start_date->isPast() && $event->end_date->isFuture())
                                    <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                        Sedang berlangsung
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-semibold text-board-800">
                                {{ $event->title }}
                            </h3>

                            <p class="mt-1 text-xs text-board-500">
                                Tahun Ajaran:
                                {{ $event->academicYear->name ?? '-' }}

                                @if ($event->semester)
                                    <span class="mx-1">·</span>
                                    Semester {{ $event->semester->name }}
                                @endif
                            </p>

                            @if ($event->location)
                                <p class="mt-2 flex items-center gap-2 text-xs text-board-500">
                                    <span aria-hidden="true">⌖</span>
                                    {{ $event->location }}
                                </p>
                            @endif

                            @if ($event->description)
                                <p class="mt-2 text-sm leading-relaxed text-board-600">
                                    {{ \Illuminate\Support\Str::limit($event->description, 180) }}
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Informasi tambahan berdasarkan role --}}
    <section class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Ringkasan siswa per kelas khusus Admin --}}
        @if (auth()->user()->hasRole('admin'))
            <div class="overflow-hidden rounded-xl border border-board-200 bg-white shadow-sm">
                <div class="border-b border-board-200 px-5 py-4">
                    <h2 class="font-semibold text-board-800">Siswa Aktif per Kelas</h2>
                    <p class="mt-1 text-xs text-board-500">Distribusi siswa pada tahun ajaran aktif</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-board-50 text-left text-xs font-semibold uppercase tracking-wide text-board-500">
                            <tr>
                                <th class="px-5 py-3">Nama Kelas</th>
                                <th class="px-5 py-3 text-right">Jumlah Siswa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-board-100">
                            @forelse ($classSummary as $c)
                                <tr class="hover:bg-board-50">
                                    <td class="px-5 py-3 font-medium text-board-700">{{ $c->name }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-board-800">
                                        {{ $c->students_count }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-8 text-center text-board-500">
                                        Belum ada kelas pada tahun ajaran aktif.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Ringkasan absensi hari ini --}}
            <div class="rounded-xl border border-board-200 bg-white p-5 shadow-sm">
                <div class="mb-5">
                    <h2 class="font-semibold text-board-800">Absensi Hari Ini</h2>
                    <p class="mt-1 text-xs text-board-500">Rekap kehadiran siswa hari ini</p>
                </div>

                @if ($attendanceToday->isEmpty())
                    <div class="rounded-lg border border-dashed border-board-200 bg-board-50 px-4 py-8 text-center">
                        <p class="text-sm text-board-500">
                            Belum ada absensi yang dicatat hari ini.
                        </p>
                    </div>
                @else
                    <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach (\App\Models\Attendance::STATUSES as $key => $label)
                            <div class="rounded-xl border border-board-200 bg-board-50 p-4 text-center">
                                <dt class="text-xs font-medium text-board-500">{{ $label }}</dt>
                                <dd class="mt-2 font-display text-2xl font-semibold text-board-800">
                                    {{ $attendanceToday[$key] ?? 0 }}
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>
        @endif

        {{-- Ringkasan tambahan Guru --}}
        @foreach ($extra as $label => $total)
            <div class="rounded-xl border border-board-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-board-500">{{ $label }}</p>
                <p class="mt-2 font-display text-3xl font-semibold text-board-800">
                    {{ number_format($total, 0, ',', '.') }}
                </p>
            </div>
        @endforeach

        {{-- Tugas yang belum dikumpulkan --}}
        @if (isset($pendingTasks))
            <div class="overflow-hidden rounded-xl border border-board-200 bg-white shadow-sm">
                <div class="border-b border-board-200 px-5 py-4">
                    <h2 class="font-semibold text-board-800">Tugas yang Belum Dikumpulkan</h2>
                    <p class="mt-1 text-xs text-board-500">Daftar tugas yang masih menunggu pengumpulan</p>
                </div>

                @if ($pendingTasks->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-board-500">
                        Tidak ada tugas yang menunggu.
                    </p>
                @else
                    <ul class="divide-y divide-board-100">
                        @foreach ($pendingTasks as $t)
                            <li class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <a
                                        class="font-medium text-board-700 hover:text-board-900 hover:underline"
                                        href="{{ route('me.assignments.show', [$t->id, 'student' => $student?->id]) }}"
                                    >
                                        {{ $t->title }}
                                    </a>
                                    <p class="mt-1 text-xs text-board-500">
                                        {{ $t->teachingAssignment->subject->name }}
                                    </p>
                                </div>

                                <span class="shrink-0 text-xs text-board-500">
                                    {{ $t->due_at?->format('d/m H:i') ?? 'Tanpa tenggat' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

    </section>

    {{-- Pengumuman terbaru --}}
    <section class="overflow-hidden rounded-xl border border-board-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-board-200 px-5 py-4">
            <div>
                <h2 class="font-semibold text-board-800">Pengumuman Terbaru</h2>
                <p class="mt-1 text-xs text-board-500">Informasi terbaru dari sekolah</p>
            </div>

            <span class="rounded-lg bg-board-50 px-3 py-2 text-xs font-medium text-board-600">
                {{ $announcements->count() }} Pengumuman
            </span>
        </div>

        @forelse ($announcements as $a)
            <article class="border-b border-board-100 px-5 py-4 last:border-0 hover:bg-board-50">
                <h3 class="font-semibold text-board-800">{{ $a->title }}</h3>
                <p class="mt-1 text-xs text-board-500">
                    {{ $a->published_at?->format('d/m/Y H:i') }}
                </p>
                <p class="mt-2 text-sm leading-relaxed text-board-600">
                    {{ \Illuminate\Support\Str::limit($a->body, 160) }}
                </p>
            </article>
        @empty
            <p class="px-5 py-8 text-center text-sm text-board-500">
                Belum ada pengumuman.
            </p>
        @endforelse
    </section>

</x-layouts.app>