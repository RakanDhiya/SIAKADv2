<x-layouts.app :title="'Pengumpulan: '.$assignment->title"
               :breadcrumbs="['Tugas' => route('assignments.index'), 'Pengumpulan' => null]">
    <p class="mb-4 text-sm text-board-600">
        {{ $assignment->teachingAssignment->subject->name }} — {{ $assignment->teachingAssignment->classroom->name }}
        · Tenggat {{ $assignment->due_at?->format('d/m/Y H:i') ?? 'tidak ada' }} · Nilai maksimum {{ $assignment->max_score }}
    </p>

    <form method="POST" action="{{ route('assignments.submissions.grade', $assignment->id) }}">
        @csrf
        <x-ui.card :flush="true">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-board-50 text-left">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold">Siswa</th>
                            <th class="px-4 py-2.5 font-semibold">Pengumpulan</th>
                            <th class="px-4 py-2.5 font-semibold">Nilai</th>
                            <th class="px-4 py-2.5 font-semibold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-board-100 align-top">
                        @forelse ($students as $s)
                            @php $sub = $submissions->get($s->id); @endphp
                            <tr>
                                <td class="px-4 py-2.5">{{ $s->name }}</td>
                                <td class="px-4 py-2.5">
                                    @if (! $sub)
                                        <x-ui.badge color="amber">Belum mengumpulkan</x-ui.badge>
                                    @else
                                        <div class="text-xs text-board-600">{{ $sub->submitted_at->format('d/m/Y H:i') }} @if ($sub->isLate()) <x-ui.badge color="red">Terlambat</x-ui.badge> @endif</div>
                                        @if ($sub->content)
                                            <p class="mt-1 max-w-md whitespace-pre-line">{{ $sub->content }}</p>
                                        @endif
                                        @if ($sub->file_path)
                                            <a class="font-medium text-board-700 underline" target="_blank" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($sub->file_path) }}">Berkas</a>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    @if ($sub)
                                        <input type="number" min="0" max="{{ $assignment->max_score }}" step="0.01" name="score[{{ $sub->id }}]"
                                               value="{{ old('score.'.$sub->id, $sub->score) }}"
                                               class="w-24 rounded border border-board-200 px-2 py-1.5 focus:border-board-600 focus:outline-none">
                                    @else — @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    @if ($sub)
                                        <input type="text" maxlength="500" name="feedback[{{ $sub->id }}]" value="{{ old('feedback.'.$sub->id, $sub->feedback) }}"
                                               class="w-full min-w-48 rounded border border-board-200 px-2 py-1.5 focus:border-board-600 focus:outline-none">
                                    @else — @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-board-600">Belum ada siswa aktif di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($submissions->isNotEmpty())
                <div class="border-t border-board-100 p-4"><x-ui.button type="submit">Simpan nilai tugas</x-ui.button></div>
            @endif
        </x-ui.card>
    </form>
</x-layouts.app>
