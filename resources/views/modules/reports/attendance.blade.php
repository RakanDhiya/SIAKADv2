<x-layouts.app title="Rekap absensi" :breadcrumbs="['Laporan' => null, 'Rekap absensi' => null]">
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2 print:hidden">
        <x-forms.field label="Mata pelajaran dan kelas" name="teaching_assignment_id" class="min-w-72 flex-1">
            <x-forms.select name="teaching_assignment_id" :options="$options" :value="request('teaching_assignment_id')" />
        </x-forms.field>
        <x-forms.field label="Dari" name="from"><x-forms.input type="date" name="from" :value="$from" /></x-forms.field>
        <x-forms.field label="Sampai" name="to"><x-forms.input type="date" name="to" :value="$to" /></x-forms.field>
        <x-ui.button type="submit" variant="secondary">Tampilkan</x-ui.button>
        @if ($ta)<x-ui.button type="button" onclick="window.print()">Cetak</x-ui.button>@endif
    </form>

    @if ($ta)
        <x-ui.card :title="$ta->subject->name.' — '.$ta->classroom->name.' · '.\Illuminate\Support\Carbon::parse($from)->format('d/m/Y').' s.d. '.\Illuminate\Support\Carbon::parse($to)->format('d/m/Y')" :flush="true">
            <table class="min-w-full text-sm">
                <thead class="bg-board-50 text-left"><tr>
                    <th class="px-4 py-2.5 font-semibold">Siswa</th>
                    @foreach ($statuses as $label)<th class="px-4 py-2.5 text-right font-semibold">{{ $label }}</th>@endforeach
                </tr></thead>
                <tbody class="divide-y divide-board-100">
                    @forelse ($students as $s)
                        <tr>
                            <td class="px-4 py-2">{{ $s->name }}</td>
                            @foreach ($statuses as $key => $label)
                                <td class="px-4 py-2 text-right">{{ $counts->get($s->id)?->get($key) ?? 0 }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-board-600">Belum ada siswa aktif di kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.card>
    @endif
</x-layouts.app>
