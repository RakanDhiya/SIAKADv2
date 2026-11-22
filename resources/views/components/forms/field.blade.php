@props([
    'label',
    'name',
    'required' => false,
    'hint' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    {{-- Label field dan indikator wajib diisi --}}
    <label
        for="{{ $name }}"
        class="block text-sm font-semibold text-board-900"
    >
        {{ $label }}

        @if ($required)
            <span class="text-red-600" aria-hidden="true">*</span>
        @endif
    </label>

    {{-- Konten utama field --}}
    {{ $slot }}

    {{-- Petunjuk tambahan untuk pengguna --}}
    @if ($hint)
        <p class="text-xs leading-relaxed text-board-500">
            {{ $hint }}
        </p>
    @endif

    {{-- Pesan kesalahan validasi Laravel --}}
    @error($name)
        <p
            id="{{ $name }}-error"
            class="text-xs font-medium text-red-600"
            role="alert"
        >
            {{ $message }}
        </p>
    @enderror
</div>