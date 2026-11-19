<x-layouts.app title="Daftar guru" :breadcrumbs="['Laporan' => null, 'Daftar guru' => null]">
    <div class="mb-4 print:hidden"><x-ui.button type="button" onclick="window.print()">Cetak</x-ui.button></div>
    <x-ui.card :flush="true">
        <table class="min-w-full text-sm">
            <thead class="bg-board-50 text-left"><tr>
                <th class="px-4 py-2.5 font-semibold">No</th><th class="px-4 py-2.5 font-semibold">NIP</th><th class="px-4 py-2.5 font-semibold">Nama</th>
                <th class="px-4 py-2.5 font-semibold">JK</th><th class="px-4 py-2.5 font-semibold">Telepon</th>
            </tr></thead>
            <tbody class="divide-y divide-board-100">
                @forelse ($teachers as $t)
                    <tr>
                        <td class="px-4 py-2">{{ $loop->iteration }}</td><td class="px-4 py-2">{{ $t->nip ?? '—' }}</td><td class="px-4 py-2">{{ $t->name }}</td>
                        <td class="px-4 py-2">{{ $t->gender ?? '—' }}</td><td class="px-4 py-2">{{ $t->phone ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-board-600">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>
</x-layouts.app>
