{{-- Pilihan anak untuk orang tua (dan admin pada rapor). Tidak ditampilkan jika hanya ada satu siswa. --}}
@if ($students->count() > 1)
    <form method="GET" action="{{ $action ?? url()->current() }}" class="mt-4 flex flex-wrap items-end gap-2 print:hidden">
        @foreach (request()->except(['student', 'page']) as $key => $val)
            @if (is_scalar($val))
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endif
        @endforeach
        <x-forms.field label="Siswa" name="student" class="min-w-56">
            <x-forms.select name="student" :options="$students->mapWithKeys(fn ($s) => [$s->id => $s->name.' ('.$s->nis.')'])->all()" :value="$student?->id" placeholder="— Pilih siswa —" />
        </x-forms.field>
        <x-ui.button type="submit" variant="secondary">Tampilkan</x-ui.button>
    </form>
@endif
