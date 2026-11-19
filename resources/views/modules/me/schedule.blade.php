<x-layouts.app title="Jadwal pelajaran" :breadcrumbs="['Jadwal' => null]">
    @include('modules.me.partials.student-picker', ['action' => route('me.schedule')])

    @if (! $student)
        <x-ui.alert type="warning" class="mt-4">Belum ada data siswa yang terhubung dengan akun ini. Hubungi admin sekolah.</x-ui.alert>
    @elseif (! $classroomId || ! $semester)
        <x-ui.alert type="info" class="mt-4">Jadwal belum tersedia: siswa belum ditempatkan di kelas atau belum ada semester aktif.</x-ui.alert>
    @else
        <p class="my-4 text-sm text-board-600">{{ $student->name }} · {{ $semester->label }}</p>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach (\App\Models\Schedule::DAYS as $day => $dayName)
                <x-ui.card :title="$dayName" :flush="true">
                    @forelse ($schedules->get($day, collect()) as $s)
                        <div class="flex items-start justify-between gap-3 border-b border-board-100 px-5 py-2.5 text-sm last:border-0">
                            <div>
                                <div class="font-medium">{{ $s->teachingAssignment->subject->name }}</div>
                                <div class="text-board-600">{{ $s->teachingAssignment->teacher->name }}{{ $s->room ? ' · '.$s->room : '' }}</div>
                            </div>
                            <div class="whitespace-nowrap text-board-600">{{ $s->time_range }}</div>
                        </div>
                    @empty
                        <p class="px-5 py-4 text-sm text-board-600">Tidak ada pelajaran.</p>
                    @endforelse
                </x-ui.card>
            @endforeach
        </div>
    @endif
</x-layouts.app>
