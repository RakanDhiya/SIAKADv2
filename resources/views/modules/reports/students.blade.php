<x-layouts.app title="Daftar siswa" :breadcrumbs="['Laporan' => null, 'Daftar siswa' => null]">
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2 print:hidden">
        <x-forms.field label="Kelas" name="classroom_id" class="min-w-56"><x-forms.select name="classroom_id" :options="$classrooms" :value="request('classroom_id')" placeholder="Semua kelas" /></x-forms.field>
        <x-forms.field label="Status" name="status" class="min-w-40"><x-forms.select name="status" :options="$statuses" :value="request('status')" placeholder="Semua status" /></x-forms.field>
        <x-ui.button type="submit" variant="secondary">Terapkan</x-ui.button>
        <x-ui.button type="button" onclick="window.print()">Cetak</x-ui.button>
    </form>

    <x-ui.card :flush="true">
        <table class="min-w-full text-sm">
            <thead class="bg-board-50 text-left"><tr>
                <th class="px-4 py-2.5 font-semibold">No</th><th class="px-4 py-2.5 font-semibold">NIS</th><th class="px-4 py-2.5 font-semibold">Nama</th>
                <th class="px-4 py-2.5 font-semibold">JK</th><th class="px-4 py-2.5 font-semibold">Kelas</th><th class="px-4 py-2.5 font-semibold">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-board-100">
                @forelse ($students as $s)
                    <tr>
                        <td class="px-4 py-2">{{ $loop->iteration }}</td><td class="px-4 py-2">{{ $s->nis }}</td><td class="px-4 py-2">{{ $s->name }}</td>
                        <td class="px-4 py-2">{{ $s->gender ?? '—' }}</td><td class="px-4 py-2">{{ $s->activeEnrollment?->classroom?->name ?? '—' }}</td>
                        <td class="px-4 py-2">{{ \App\Models\Student::STATUSES[$s->status] ?? $s->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-board-600">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>
    <p class="mt-2 text-xs text-board-600">Total: {{ $students->count() }} siswa</p>
</x-layouts.app>
