<x-layouts.app title="Nilai siswa" :breadcrumbs="['Nilai' => null]">
    <x-ui.card class="mb-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <x-forms.field label="Mata pelajaran dan kelas" name="teaching_assignment_id" class="min-w-72 flex-1">
                <x-forms.select name="teaching_assignment_id" :options="$options" :value="request('teaching_assignment_id')" />
            </x-forms.field>
            <x-ui.button type="submit">Tampilkan</x-ui.button>
        </form>
    </x-ui.card>

    @if ($ta)
        @if ($components->isEmpty())
            <x-ui.alert type="warning">
                Belum ada komponen penilaian untuk penugasan ini.
                <a class="font-medium underline" href="{{ route('components.create') }}">Tambah komponen</a> terlebih dahulu.
            </x-ui.alert>
        @else
            @php $totalWeight = $components->sum('weight'); @endphp
            @if ((float) $totalWeight !== 100.0)
                <x-ui.alert type="warning" class="mb-4">Total bobot komponen saat ini {{ rtrim(rtrim(number_format($totalWeight, 2, '.', ''), '0'), '.') }}%. Sebaiknya 100%.</x-ui.alert>
            @endif

            <x-ui.card :title="$ta->subject->name.' — '.$ta->classroom->name.' ('.$ta->semester->label.')'" :flush="true">
                <x-slot:actions>
                    <form method="POST" action="{{ route('scores.compute') }}">
                        @csrf
                        <input type="hidden" name="teaching_assignment_id" value="{{ $ta->id }}">
                        <x-ui.button type="submit" variant="secondary" size="sm">Hitung nilai rapor</x-ui.button>
                    </form>
                </x-slot:actions>

                <form method="POST" action="{{ route('scores.store') }}">
                    @csrf
                    <input type="hidden" name="teaching_assignment_id" value="{{ $ta->id }}">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-board-50 text-left">
                                <tr>
                                    <th class="px-4 py-2.5 font-semibold">Siswa</th>
                                    @foreach ($components as $c)
                                        <th class="px-4 py-2.5 font-semibold">{{ $c->name }} <span class="font-normal text-board-600">({{ rtrim(rtrim($c->weight, '0'), '.') }}%)</span></th>
                                    @endforeach
                                    <th class="px-4 py-2.5 font-semibold">Nilai akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-board-100">
                                @forelse ($students as $s)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-2">{{ $s->name }}</td>
                                        @foreach ($components as $c)
                                            <td class="px-4 py-2">
                                                <input type="number" min="0" max="100" step="0.01"
                                                       name="scores[{{ $s->id }}][{{ $c->id }}]"
                                                       value="{{ old('scores.'.$s->id.'.'.$c->id, $scores->get($s->id)?->get($c->id)?->score) }}"
                                                       class="w-24 rounded border border-board-200 px-2 py-1.5 focus:border-board-600 focus:outline-none">
                                            </td>
                                        @endforeach
                                        <td class="whitespace-nowrap px-4 py-2">
                                            @if ($finals->get($s->id))
                                                <strong>{{ $finals->get($s->id)->final_score }}</strong> ({{ $finals->get($s->id)->predicate }})
                                            @else
                                                <span class="text-board-600">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ $components->count() + 2 }}" class="px-4 py-8 text-center text-board-600">Belum ada siswa aktif di kelas ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($students->isNotEmpty())
                        <div class="border-t border-board-100 p-4">
                            <x-ui.button type="submit">Simpan nilai</x-ui.button>
                            <span class="ml-2 text-xs text-board-600">Setelah menyimpan, klik "Hitung nilai rapor" untuk memperbarui nilai akhir.</span>
                        </div>
                    @endif
                </form>
            </x-ui.card>
        @endif
    @endif
</x-layouts.app>
