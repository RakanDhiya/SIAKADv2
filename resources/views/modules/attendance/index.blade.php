<x-layouts.app title="Absensi siswa" :breadcrumbs="['Absensi' => null]">
    <x-ui.card class="mb-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <x-forms.field label="Mata pelajaran dan kelas" name="teaching_assignment_id" class="min-w-72 flex-1">
                <x-forms.select name="teaching_assignment_id" :options="$options" :value="request('teaching_assignment_id')" />
            </x-forms.field>
            <x-forms.field label="Tanggal" name="date">
                <x-forms.input type="date" name="date" :value="$date" />
            </x-forms.field>
            <x-ui.button type="submit">Tampilkan siswa</x-ui.button>
        </form>
        @if (empty($options))
            <p class="mt-3 text-sm text-board-600">Belum ada penugasan mengajar. Admin perlu menambahkannya di menu Penugasan guru.</p>
        @endif
    </x-ui.card>

    @if ($ta)
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf
            <input type="hidden" name="teaching_assignment_id" value="{{ $ta->id }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <x-ui.card :title="$ta->subject->name.' — '.$ta->classroom->name.' · '.\Illuminate\Support\Carbon::parse($date)->format('d/m/Y')" :flush="true">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-board-50 text-left">
                            <tr>
                                <th class="w-12 px-4 py-2.5 font-semibold">No</th>
                                <th class="px-4 py-2.5 font-semibold">Siswa</th>
                                <th class="px-4 py-2.5 font-semibold">Kehadiran</th>
                                <th class="px-4 py-2.5 font-semibold">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-board-100">
                            @forelse ($students as $s)
                                @php
                                    $cur = $existing->get($s->id);
                                    $st = old('status.'.$s->id, $cur->status ?? 'hadir');
                                @endphp
                                <tr>
                                    <td class="px-4 py-2.5 text-board-600">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2.5">{{ $s->name }} <span class="text-board-600">({{ $s->nis }})</span></td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex flex-wrap gap-x-4 gap-y-1">
                                            @foreach ($statuses as $key => $label)
                                                <label class="inline-flex items-center gap-1.5">
                                                    <input type="radio" name="status[{{ $s->id }}]" value="{{ $key }}" @checked($st === $key)
                                                           class="text-board-700 focus:ring-pencil-400"> {{ $label }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <input type="text" name="note[{{ $s->id }}]" maxlength="150" value="{{ old('note.'.$s->id, $cur->note ?? '') }}"
                                               class="w-full min-w-40 rounded border border-board-200 px-2 py-1.5 text-sm focus:border-board-600 focus:outline-none">
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-board-600">Belum ada siswa aktif di kelas ini. Tempatkan siswa di menu Penempatan siswa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($students->isNotEmpty())
                    <div class="border-t border-board-100 p-4">
                        <x-ui.button type="submit">Simpan absensi</x-ui.button>
                    </div>
                @endif
            </x-ui.card>
        </form>
    @endif
</x-layouts.app>
