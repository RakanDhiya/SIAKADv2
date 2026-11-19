<x-layouts.app title="Absensi" :breadcrumbs="['Absensi' => null]">
    @include('modules.me.partials.student-picker', ['action' => route('me.attendance')])

    @if (! $student)
        <x-ui.alert type="warning" class="mt-4">Belum ada data siswa yang terhubung dengan akun ini.</x-ui.alert>
    @else
        <div class="my-4 grid max-w-xl grid-cols-4 divide-x divide-board-100 rounded-md border border-board-100 bg-white text-center">
            @foreach (\App\Models\Attendance::STATUSES as $key => $label)
                <div class="px-3 py-3">
                    <div class="font-display text-2xl font-semibold">{{ $summary[$key] ?? 0 }}</div>
                    <div class="text-xs text-board-600">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        <x-ui.card :flush="true">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-board-50 text-left">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold">Tanggal</th>
                            <th class="px-4 py-2.5 font-semibold">Mata pelajaran</th>
                            <th class="px-4 py-2.5 font-semibold">Status</th>
                            <th class="px-4 py-2.5 font-semibold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-board-100">
                        @forelse ($rows as $r)
                            <tr>
                                <td class="px-4 py-2.5">{{ $r->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5">{{ $r->teachingAssignment->subject->name }}</td>
                                <td class="px-4 py-2.5">
                                    <x-ui.badge :color="['hadir' => 'green', 'izin' => 'blue', 'sakit' => 'amber', 'alpa' => 'red'][$r->status] ?? 'gray'">{{ \App\Models\Attendance::STATUSES[$r->status] ?? $r->status }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-2.5">{{ $r->note ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-board-600">Belum ada catatan absensi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
        <div class="mt-4">{{ $rows->links() }}</div>
    @endif
</x-layouts.app>
