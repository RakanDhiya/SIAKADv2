<x-layouts.app title="Materi pembelajaran" :breadcrumbs="['Materi' => null]">
    @include('modules.me.partials.student-picker', ['action' => route('me.materials')])

    @if (! $student)
        <x-ui.alert type="warning" class="mt-4">Belum ada data siswa yang terhubung dengan akun ini.</x-ui.alert>
    @else
        <div class="mt-4 max-w-3xl space-y-4">
            @forelse ($materials as $m)
                <x-ui.card>
                    <p class="text-xs text-board-600">{{ $m->teachingAssignment->subject->name }} · {{ $m->created_at->format('d/m/Y') }}</p>
                    <h2 class="font-display text-xl font-semibold">{{ $m->title }}</h2>
                    @if ($m->description)
                        <p class="mt-2 whitespace-pre-line text-sm">{{ $m->description }}</p>
                    @endif
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if ($m->file_path)
                            <x-ui.button :href="\Illuminate\Support\Facades\Storage::disk('public')->url($m->file_path)" variant="secondary" size="sm" target="_blank">Unduh berkas</x-ui.button>
                        @endif
                        @if ($m->link_url)
                            <x-ui.button :href="$m->link_url" variant="secondary" size="sm" target="_blank" rel="noopener">Buka tautan</x-ui.button>
                        @endif
                    </div>
                </x-ui.card>
            @empty
                <x-ui.card><p class="text-sm text-board-600">Belum ada materi untuk kelas ini.</p></x-ui.card>
            @endforelse
        </div>
    @endif
</x-layouts.app>
