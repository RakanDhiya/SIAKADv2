<x-layouts.app title="Kenaikan kelas" :breadcrumbs="['Kenaikan kelas' => null]">
    <x-ui.card class="mb-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <x-forms.field label="Kelas asal" name="from_classroom_id" class="min-w-64">
                <x-forms.select name="from_classroom_id" :options="$options" :value="request('from_classroom_id')" />
            </x-forms.field>
            <x-ui.button type="submit">Tampilkan siswa</x-ui.button>
        </form>
    </x-ui.card>

    @if (request('from_classroom_id'))
        <form method="POST" action="{{ route('promotion.store') }}">
            @csrf
            <input type="hidden" name="from_classroom_id" value="{{ request('from_classroom_id') }}">

            <x-ui.card title="Kelas tujuan" class="mb-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <x-forms.field label="Kelas tujuan untuk siswa naik" name="to_classroom_id" :required="true" hint="Harus berada di tahun ajaran yang berbeda.">
                        <x-forms.select name="to_classroom_id" :options="$options" />
                    </x-forms.field>
                    <x-forms.field label="Kelas untuk siswa tinggal kelas" name="stay_classroom_id" hint="Wajib diisi bila ada siswa yang tinggal kelas.">
                        <x-forms.select name="stay_classroom_id" :options="$options" />
                    </x-forms.field>
                </div>
            </x-ui.card>

            <x-ui.card title="Hasil per siswa" :flush="true">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-board-50 text-left">
                            <tr>
                                <th class="px-4 py-2.5 font-semibold">Siswa</th>
                                <th class="px-4 py-2.5 font-semibold">Hasil</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-board-100">
                            @forelse ($enrollments as $e)
                                <tr>
                                    <td class="px-4 py-2.5">{{ $e->student->name }} <span class="text-board-600">({{ $e->student->nis }})</span></td>
                                    <td class="px-4 py-2.5">
                                        <select name="result[{{ $e->id }}]" class="rounded border border-board-200 bg-white px-2 py-1.5">
                                            <option value="naik">Naik kelas</option>
                                            <option value="tinggal">Tinggal kelas</option>
                                            <option value="lulus">Lulus</option>
                                            <option value="">Lewati</option>
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-4 py-8 text-center text-board-600">Tidak ada siswa aktif di kelas ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($enrollments->isNotEmpty())
                    <div class="border-t border-board-100 p-4">
                        <x-ui.button type="submit" onclick="return confirm('Proses kenaikan kelas untuk siswa di atas?')">Proses kenaikan kelas</x-ui.button>
                    </div>
                @endif
            </x-ui.card>
        </form>
    @endif
</x-layouts.app>
