<x-layouts.app title="Tugas" :breadcrumbs="['Tugas' => null]">
    @include('modules.me.partials.student-picker', ['action' => route('me.assignments')])

    @if (! $student)
        <x-ui.alert type="warning" class="mt-4">Belum ada data siswa yang terhubung dengan akun ini.</x-ui.alert>
    @else
        <x-ui.card :flush="true" class="mt-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-board-50 text-left">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold">Tugas</th>
                            <th class="px-4 py-2.5 font-semibold">Mata pelajaran</th>
                            <th class="px-4 py-2.5 font-semibold">Tenggat</th>
                            <th class="px-4 py-2.5 font-semibold">Status</th>
                            <th class="px-4 py-2.5 font-semibold">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-board-100">
                        @forelse ($assignments as $a)
                            @php $sub = $a->submissions->first(); @endphp
                            <tr>
                                <td class="px-4 py-2.5">
                                    <a class="font-medium text-board-700 hover:underline" href="{{ route('me.assignments.show', [$a->id, 'student' => $student->id]) }}">{{ $a->title }}</a>
                                </td>
                                <td class="px-4 py-2.5">{{ $a->teachingAssignment->subject->name }}</td>
                                <td class="px-4 py-2.5">{{ $a->due_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-2.5">
                                    @if (! $sub)
                                        <x-ui.badge color="amber">Belum dikumpulkan</x-ui.badge>
                                    @elseif ($sub->graded_at)
                                        <x-ui.badge color="green">Sudah dinilai</x-ui.badge>
                                    @else
                                        <x-ui.badge color="blue">Terkumpul{{ $sub->isLate() ? ' (terlambat)' : '' }}</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">{{ $sub?->score ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-board-600">Belum ada tugas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @endif
</x-layouts.app>
