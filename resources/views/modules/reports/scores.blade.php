<x-layouts.app title="Rekap nilai" :breadcrumbs="['Laporan' => null, 'Rekap nilai' => null]">
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2 print:hidden">
        <x-forms.field label="Mata pelajaran dan kelas" name="teaching_assignment_id" class="min-w-72 flex-1">
            <x-forms.select name="teaching_assignment_id" :options="$options" :value="request('teaching_assignment_id')" />
        </x-forms.field>
        <x-ui.button type="submit" variant="secondary">Tampilkan</x-ui.button>
        @if ($ta)<x-ui.button type="button" onclick="window.print()">Cetak</x-ui.button>@endif
    </form>

    @if ($ta)
        <x-ui.card :title="$ta->subject->name.' — '.$ta->classroom->name.' ('.$ta->semester->label.')'" :flush="true">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-board-50 text-left"><tr>
                        <th class="px-4 py-2.5 font-semibold">Siswa</th>
                        @foreach ($components as $c)<th class="px-4 py-2.5 text-right font-semibold">{{ $c->name }}</th>@endforeach
                        <th class="px-4 py-2.5 text-right font-semibold">Nilai akhir</th>
                        <th class="px-4 py-2.5 font-semibold">Predikat</th>
                    </tr></thead>
                    <tbody class="divide-y divide-board-100">
                        @forelse ($students as $s)
                            @php $final = $finals->get($s->id); @endphp
                            <tr>
                                <td class="px-4 py-2">{{ $s->name }}</td>
                                @foreach ($components as $c)
                                    <td class="px-4 py-2 text-right">{{ $scores->get($s->id)?->get($c->id)?->score ?? '—' }}</td>
                                @endforeach
                                <td class="px-4 py-2 text-right font-medium">{{ $final->final_score ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $final->predicate ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $components->count() + 3 }}" class="px-4 py-8 text-center text-board-600">Belum ada siswa aktif di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @endif
</x-layouts.app>
