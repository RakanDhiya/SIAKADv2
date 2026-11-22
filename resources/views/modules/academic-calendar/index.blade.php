@php
    $isAdmin = auth()->user()->hasRole('admin');

    $monthNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $categoryColors = [
        'Akademik' => 'bg-emerald-100 text-emerald-800',
        'Ujian' => 'bg-amber-100 text-amber-800',
        'Libur' => 'bg-sky-100 text-sky-800',
        'Kegiatan Sekolah' => 'bg-violet-100 text-violet-800',
        'Pembagian Rapor' => 'bg-rose-100 text-rose-800',
        'Lainnya' => 'bg-board-100 text-board-700',
    ];

    $previousMonth = $startDate->copy()->subMonth();
    $nextMonth = $startDate->copy()->addMonth();

    $formEvent = $editEvent;
    $formAcademicYear = old(
        'academic_year_id',
        $formEvent?->academic_year_id ?? $activeAcademicYear?->id
    );
@endphp

<x-layouts.app
    title="Kalender Akademik"
    :breadcrumbs="['Kalender Akademik' => null]"
>
    {{-- Header kalender --}}
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm text-board-500">
                Informasi jadwal dan kegiatan akademik sekolah
            </p>

            @if ($activeAcademicYear)
                <p class="mt-1 text-sm font-semibold text-board-700">
                    Tahun Ajaran {{ $activeAcademicYear->name }}
                </p>
            @endif
        </div>

        @if ($isAdmin)
            <a
                href="{{ route('academic-calendar.index', ['month' => $month, 'year' => $year, 'create' => 1]) }}#form-agenda"
                class="inline-flex items-center justify-center rounded-lg bg-board-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-board-700"
            >
                + Tambah Agenda
            </a>
        @endif
    </div>

    {{-- Ringkasan --}}
    <div class="mb-6 grid gap-3 sm:grid-cols-3">
        <x-ui.card>
            <p class="text-xs font-medium uppercase tracking-wide text-board-500">
                Total Agenda Bulan Ini
            </p>
            <p class="mt-2 font-display text-3xl font-semibold text-board-900">
                {{ $events->count() }}
            </p>
            <p class="mt-1 text-xs text-board-500">
                Agenda yang berlangsung pada bulan ini
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-xs font-medium uppercase tracking-wide text-board-500">
                Agenda Mendatang
            </p>
            <p class="mt-2 font-display text-3xl font-semibold text-board-900">
                {{ $events->filter(fn ($event) => $event->end_date->toDateString() >= today()->toDateString())->count() }}
            </p>
            <p class="mt-1 text-xs text-board-500">
                Belum berakhir
            </p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-xs font-medium uppercase tracking-wide text-board-500">
                Jenis Kegiatan
            </p>
            <p class="mt-2 font-display text-3xl font-semibold text-board-900">
                {{ $events->pluck('category')->unique()->count() }}
            </p>
            <p class="mt-1 text-xs text-board-500">
                Kategori agenda bulan ini
            </p>
        </x-ui.card>
    </div>

    {{-- Kalender dan daftar agenda --}}
    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,0.8fr)]">

        {{-- Kalender bulanan --}}
        <x-ui.card class="min-w-0">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-display text-xl font-semibold text-board-900">
                        {{ $monthNames[$month] }} {{ $year }}
                    </h2>
                    <p class="mt-1 text-xs text-board-500">
                        Kalender kegiatan sekolah
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('academic-calendar.index', ['month' => $previousMonth->month, 'year' => $previousMonth->year]) }}"
                        aria-label="Bulan sebelumnya"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-board-200 text-board-700 transition hover:bg-board-50"
                    >
                        &larr;
                    </a>

                    <a
                        href="{{ route('academic-calendar.index') }}"
                        class="rounded-lg border border-board-200 px-3 py-2 text-xs font-semibold text-board-700 transition hover:bg-board-50"
                    >
                        Hari ini
                    </a>

                    <a
                        href="{{ route('academic-calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                        aria-label="Bulan berikutnya"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-board-200 text-board-700 transition hover:bg-board-50"
                    >
                        &rarr;
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-7 border-b border-board-200 pb-2">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                    <div class="py-2 text-center text-xs font-semibold text-board-500">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-7">
                @foreach ($calendarDays as $day)
                    @php
                        $dateString = $day->toDateString();
                        $isCurrentMonth = $day->month === $month;
                        $isToday = $dateString === today()->toDateString();

                        $dayEvents = $events->filter(
                            fn ($event) =>
                                $dateString >= $event->start_date->toDateString()
                                && $dateString <= $event->end_date->toDateString()
                        );
                    @endphp

                    <div
                        class="min-h-24 border-b border-r border-board-100 p-1.5 sm:min-h-28 sm:p-2
                            {{ $isCurrentMonth ? 'bg-white' : 'bg-board-50/60' }}"
                    >
                        <div class="mb-1 flex justify-center sm:justify-start">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold
                                {{ $isToday ? 'bg-board-800 text-white' : ($isCurrentMonth ? 'text-board-800' : 'text-board-300') }}">
                                {{ $day->day }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            @foreach ($dayEvents->take(2) as $event)
                                <div
                                    title="{{ $event->title }}"
                                    class="truncate rounded px-1 py-1 text-[9px] font-semibold leading-tight sm:text-[10px]
                                        {{ $categoryColors[$event->category] ?? $categoryColors['Lainnya'] }}"
                                >
                                    <span class="hidden sm:inline">
                                        {{ $event->title }}
                                    </span>
                                    <span class="sm:hidden">
                                        {{ \Illuminate\Support\Str::limit($event->title, 9) }}
                                    </span>
                                </div>
                            @endforeach

                            @if ($dayEvents->count() > 2)
                                <p class="text-center text-[9px] font-medium text-board-500">
                                    +{{ $dayEvents->count() - 2 }} agenda
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Legenda --}}
            <div class="mt-5 flex flex-wrap gap-x-4 gap-y-2">
                @foreach ($categoryColors as $category => $color)
                    <div class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-sm {{ explode(' ', $color)[0] }}"></span>
                        <span class="text-[11px] text-board-600">{{ $category }}</span>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        {{-- Daftar agenda --}}
        <div class="space-y-4">
            <x-ui.card>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="font-display text-lg font-semibold text-board-900">
                            Agenda Akademik
                        </h2>
                        <p class="mt-1 text-xs text-board-500">
                            {{ $monthNames[$month] }} {{ $year }}
                        </p>
                    </div>

                    <span class="rounded-full bg-board-100 px-2.5 py-1 text-xs font-semibold text-board-700">
                        {{ $events->count() }} agenda
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse ($events as $event)
                        <div class="border-l-2 border-board-300 pl-3">
                            <div class="mb-1 flex flex-wrap items-center gap-2">
                                <span class="rounded px-2 py-1 text-[10px] font-semibold
                                    {{ $categoryColors[$event->category] ?? $categoryColors['Lainnya'] }}">
                                    {{ $event->category }}
                                </span>
                            </div>

                            <h3 class="text-sm font-semibold text-board-900">
                                {{ $event->title }}
                            </h3>

                            <p class="mt-1 text-xs text-board-500">
                                {{ $event->start_date->format('d M Y') }}
                                @if ($event->start_date->toDateString() !== $event->end_date->toDateString())
                                    – {{ $event->end_date->format('d M Y') }}
                                @endif
                            </p>

                            @if ($event->location)
                                <p class="mt-1 text-xs text-board-500">
                                    Lokasi: {{ $event->location }}
                                </p>
                            @endif

                            @if ($event->description)
                                <p class="mt-2 whitespace-pre-line text-xs leading-relaxed text-board-600">
                                    {{ $event->description }}
                                </p>
                            @endif

                            <p class="mt-2 text-[11px] text-board-400">
                                {{ $event->academicYear->name }}
                                @if ($event->semester)
                                    · Semester {{ $event->semester->name }}
                                @endif
                            </p>

                            @if ($isAdmin)
                                <div class="mt-3 flex gap-2">
                                    <a
                                        href="{{ route('academic-calendar.index', ['month' => $month, 'year' => $year, 'edit' => $event->id]) }}#form-agenda"
                                        class="text-xs font-semibold text-board-700 underline"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('academic-calendar.destroy', $event) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus agenda ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-xs font-semibold text-red-700 underline"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-lg bg-board-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-board-700">
                                Belum ada agenda
                            </p>
                            <p class="mt-1 text-xs text-board-500">
                                Belum ada kegiatan akademik pada bulan ini.
                            </p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>

            @if ($isAdmin)
                <x-ui.card id="form-agenda">
                    <div class="mb-4">
                        <h2 class="font-display text-lg font-semibold text-board-900">
                            {{ $editEvent ? 'Edit Agenda' : 'Tambah Agenda' }}
                        </h2>
                        <p class="mt-1 text-xs text-board-500">
                            Kelola kegiatan akademik sekolah
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ $editEvent
                            ? route('academic-calendar.update', $editEvent)
                            : route('academic-calendar.store') }}"
                        class="space-y-4"
                    >
                        @csrf

                        @if ($editEvent)
                            @method('PUT')
                        @endif

                        <div>
                            <label for="title" class="mb-1 block text-xs font-semibold text-board-700">
                                Nama Agenda *
                            </label>
                            <input
                                id="title"
                                name="title"
                                type="text"
                                required
                                maxlength="150"
                                value="{{ old('title', $formEvent?->title) }}"
                                class="w-full rounded-lg border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-500 focus:outline-none focus:ring-2 focus:ring-pencil-400/30"
                                placeholder="Contoh: Ujian Tengah Semester"
                            >
                            @error('title')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="mb-1 block text-xs font-semibold text-board-700">
                                Kategori *
                            </label>
                            <select
                                id="category"
                                name="category"
                                required
                                class="w-full rounded-lg border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-500 focus:outline-none"
                            >
                                <option value="">Pilih kategori</option>
                                @foreach (array_keys($categoryColors) as $category)
                                    <option
                                        value="{{ $category }}"
                                        @selected(old('category', $formEvent?->category) === $category)
                                    >
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="academic_year_id" class="mb-1 block text-xs font-semibold text-board-700">
                                Tahun Ajaran *
                            </label>
                            <select
                                id="academic_year_id"
                                name="academic_year_id"
                                required
                                class="w-full rounded-lg border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-500 focus:outline-none"
                            >
                                <option value="">Pilih tahun ajaran</option>
                                @foreach ($academicYears as $academicYear)
                                    <option
                                        value="{{ $academicYear->id }}"
                                        @selected((string) $formAcademicYear === (string) $academicYear->id)
                                    >
                                        {{ $academicYear->name }}
                                        {{ $academicYear->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="semester_id" class="mb-1 block text-xs font-semibold text-board-700">
                                Semester
                            </label>
                            <select
                                id="semester_id"
                                name="semester_id"
                                class="w-full rounded-lg border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-500 focus:outline-none"
                            >
                                <option value="">Tidak terkait semester tertentu</option>
                                @foreach ($semesters as $semester)
                                    <option
                                        value="{{ $semester->id }}"
                                        @selected((string) old('semester_id', $formEvent?->semester_id) === (string) $semester->id)
                                    >
                                        {{ $semester->name }} — {{ $semester->academicYear->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('semester_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="start_date" class="mb-1 block text-xs font-semibold text-board-700">
                                Tanggal Mulai *
                            </label>
                            <input
                                id="start_date"
                                name="start_date"
                                type="date"
                                required
                                value="{{ old('start_date', $formEvent?->start_date?->format('Y-m-d')) }}"
                                class="w-full rounded-lg border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-500 focus:outline-none"
                            >
                            @error('start_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="mb-1 block text-xs font-semibold text-board-700">
                                Tanggal Selesai *
                            </label>
                            <input
                                id="end_date"
                                name="end_date"
                                type="date"
                                required
                                value="{{ old('end_date', $formEvent?->end_date?->format('Y-m-d')) }}"
                                class="w-full rounded-lg border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-500 focus:outline-none"
                            >
                            @error('end_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="location" class="mb-1 block text-xs font-semibold text-board-700">
                                Lokasi
                            </label>
                            <input
                                id="location"
                                name="location"
                                type="text"
                                maxlength="150"
                                value="{{ old('location', $formEvent?->location) }}"
                                class="w-full rounded-lg border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-500 focus:outline-none"
                                placeholder="Opsional"
                            >
                            @error('location')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="mb-1 block text-xs font-semibold text-board-700">
                                Deskripsi
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                class="w-full rounded-lg border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-500 focus:outline-none"
                                placeholder="Keterangan tambahan agenda..."
                            >{{ old('description', $formEvent?->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <button
                                type="submit"
                                class="rounded-lg bg-board-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-board-700"
                            >
                                {{ $editEvent ? 'Simpan Perubahan' : 'Simpan Agenda' }}
                            </button>

                            @if ($editEvent)
                                <a
                                    href="{{ route('academic-calendar.index', ['month' => $month, 'year' => $year]) }}"
                                    class="rounded-lg border border-board-200 px-4 py-2.5 text-sm font-semibold text-board-700 transition hover:bg-board-50"
                                >
                                    Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-layouts.app>