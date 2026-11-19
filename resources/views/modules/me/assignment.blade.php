<x-layouts.app :title="$assignment->title"
               :breadcrumbs="['Tugas' => route('me.assignments', ['student' => $student->id]), 'Detail' => null]">
    <div class="grid max-w-4xl gap-6 lg:grid-cols-2">
        <x-ui.card :title="$assignment->teachingAssignment->subject->name">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-board-600">Tenggat</dt><dd class="font-medium">{{ $assignment->due_at?->format('d/m/Y H:i') ?? 'Tidak ada tenggat' }}</dd></div>
                <div><dt class="text-board-600">Nilai maksimum</dt><dd class="font-medium">{{ $assignment->max_score }}</dd></div>
                <div><dt class="text-board-600">Instruksi</dt><dd class="whitespace-pre-line">{{ $assignment->description ?: '—' }}</dd></div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Pengumpulan">
            @if ($submission)
                <div class="mb-4 space-y-1 text-sm">
                    <p>Dikumpulkan {{ $submission->submitted_at->format('d/m/Y H:i') }}
                        @if ($submission->isLate()) <x-ui.badge color="red">Terlambat</x-ui.badge> @endif</p>
                    @if ($submission->content)
                        <p class="whitespace-pre-line rounded bg-board-50 p-3">{{ $submission->content }}</p>
                    @endif
                    @if ($submission->file_path)
                        <p><a class="font-medium text-board-700 underline" target="_blank" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($submission->file_path) }}">Berkas terkirim</a></p>
                    @endif
                    @if ($submission->graded_at)
                        <p class="pt-2 font-medium">Nilai: {{ $submission->score }} / {{ $assignment->max_score }}</p>
                        @if ($submission->feedback)
                            <p class="text-board-700">Catatan guru: {{ $submission->feedback }}</p>
                        @endif
                    @endif
                </div>
            @endif

            @if (auth()->user()->hasRole('siswa') && ! ($submission && $submission->graded_at))
                <form method="POST" enctype="multipart/form-data" action="{{ route('me.assignments.submit', $assignment->id) }}" class="space-y-4">
                    @csrf
                    <x-forms.field label="Jawaban" name="content">
                        <x-forms.textarea name="content" :value="$submission?->content" />
                    </x-forms.field>
                    <x-forms.field label="Berkas (maks. 5 MB)" name="file" hint="Mengunggah berkas baru akan menggantikan berkas sebelumnya.">
                        <input type="file" id="file" name="file" class="block w-full text-sm file:mr-3 file:rounded file:border-0 file:bg-board-100 file:px-3 file:py-2">
                    </x-forms.field>
                    <x-ui.button type="submit">{{ $submission ? 'Perbarui pengumpulan' : 'Kumpulkan tugas' }}</x-ui.button>
                </form>
            @elseif (auth()->user()->hasRole('ortu'))
                <p class="text-sm text-board-600">Hanya siswa yang dapat mengumpulkan tugas.</p>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
