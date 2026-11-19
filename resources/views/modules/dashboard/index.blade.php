<x-layouts.app :title="'Selamat datang, '.auth()->user()->name" :breadcrumbs="[]">
    {{-- Ringkasan data sekolah --}}
    <div class="grid grid-cols-2 divide-x divide-board-100 rounded-md border border-board-100 bg-white lg:grid-cols-4">
        @foreach ($counts as $label => $total)
            <div class="px-5 py-4">
                <div class="font-display text-4xl font-semibold text-board-800">{{ $total }}</div>
                <div class="text-sm text-board-600">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <p class="mt-3 text-sm text-board-600">
        Tahun ajaran aktif: <span class="font-medium text-board-900">{{ $year->name ?? 'belum diatur' }}</span>
        · Semester aktif: <span class="font-medium text-board-900">{{ $semester->label ?? 'belum diatur' }}</span>
    </p>

    @if (auth()->user()->hasRole('siswa', 'ortu'))
        @include('modules.me.partials.student-picker', ['students' => $students, 'student' => $student, 'action' => route('dashboard')])
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Panel khusus admin --}}
        @if (auth()->user()->hasRole('admin'))
            <x-ui.card title="Siswa aktif per kelas" :flush="true">
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-board-100">
                        @forelse ($classSummary as $c)
                            <tr>
                                <td class="px-5 py-2.5">{{ $c->name }}</td>
                                <td class="px-5 py-2.5 text-right font-medium">{{ $c->students_count }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-5 py-6 text-board-600">Belum ada kelas pada tahun ajaran aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-ui.card>

            <x-ui.card title="Absensi hari ini">
                @if ($attendanceToday->isEmpty())
                    <p class="text-sm text-board-600">Belum ada absensi yang dicatat hari ini.</p>
                @else
                    <dl class="grid grid-cols-4 gap-3 text-center">
                        @foreach (\App\Models\Attendance::STATUSES as $key => $label)
                            <div>
                                <dt class="text-xs text-board-600">{{ $label }}</dt>
                                <dd class="font-display text-2xl font-semibold">{{ $attendanceToday[$key] ?? 0 }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </x-ui.card>
        @endif

        {{-- Guru: ringkasan tambahan --}}
        @foreach ($extra as $label => $total)
            <x-ui.card>
                <div class="font-display text-3xl font-semibold text-board-800">{{ $total }}</div>
                <div class="text-sm text-board-600">{{ $label }}</div>
            </x-ui.card>
        @endforeach

        {{-- Jadwal hari ini (guru, siswa, orang tua) --}}
        @unless (auth()->user()->hasRole('admin'))
            <x-ui.card :title="'Jadwal hari ini ('.(\App\Models\Schedule::DAYS[$today] ?? 'Minggu').')'" :flush="true">
                @if ($todaySchedule->isEmpty())
                    <p class="p-5 text-sm text-board-600">Tidak ada jadwal hari ini.</p>
                @else
                    <ul class="divide-y divide-board-100 text-sm">
                        @foreach ($todaySchedule as $s)
                            <li class="flex items-center justify-between gap-3 px-5 py-2.5">
                                <span>
                                    <span class="font-medium">{{ $s->teachingAssignment->subject->name }}</span>
                                    <span class="text-board-600">
                                        — {{ auth()->user()->hasRole('guru') ? $s->teachingAssignment->classroom->name : $s->teachingAssignment->teacher->name }}
                                    </span>
                                </span>
                                <span class="whitespace-nowrap text-board-600">{{ $s->time_range }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        @endunless

        @if (isset($pendingTasks))
            <x-ui.card title="Tugas yang belum dikumpulkan" :flush="true">
                @if ($pendingTasks->isEmpty())
                    <p class="p-5 text-sm text-board-600">Tidak ada tugas yang menunggu.</p>
                @else
                    <ul class="divide-y divide-board-100 text-sm">
                        @foreach ($pendingTasks as $t)
                            <li class="flex items-center justify-between gap-3 px-5 py-2.5">
                                <a class="font-medium text-board-700 hover:underline"
                                   href="{{ route('me.assignments.show', [$t->id, 'student' => $student?->id]) }}">{{ $t->title }}</a>
                                <span class="whitespace-nowrap text-board-600">{{ $t->teachingAssignment->subject->name }} · {{ $t->due_at?->format('d/m H:i') ?? 'tanpa tenggat' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        @endif

        <x-ui.card title="Pengumuman terbaru" :flush="true" class="lg:col-span-2">
            @forelse ($announcements as $a)
                <article class="border-b border-board-100 px-5 py-3 last:border-0">
                    <h3 class="font-medium">{{ $a->title }}</h3>
                    <p class="text-xs text-board-600">{{ $a->published_at?->format('d/m/Y H:i') }}</p>
                    <p class="mt-1 text-sm">{{ \Illuminate\Support\Str::limit($a->body, 160) }}</p>
                </article>
            @empty
                <p class="p-5 text-sm text-board-600">Belum ada pengumuman.</p>
            @endforelse
        </x-ui.card>
    </div>
</x-layouts.app>
