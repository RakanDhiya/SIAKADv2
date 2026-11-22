@props([
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => '— Pilih —',
    'multiple' => false,
])

@php
    /**
     * Mengambil nilai pilihan sebelumnya atau nilai default.
     *
     * Konversi ke string memastikan perbandingan nilai option
     * tetap konsisten, termasuk ketika field menggunakan multiple.
     */
    $selectedValues = array_map(
        'strval',
        (array) old($name, $value)
    );

    /**
     * Menentukan border berdasarkan status validasi field.
     */
    $borderClass = $errors->has($name)
        ? 'border-red-500'
        : 'border-board-200';

    /**
     * Menentukan nama field untuk mendukung pilihan tunggal
     * maupun pilihan lebih dari satu.
     */
    $fieldName = $multiple ? "{$name}[]" : $name;
@endphp

<select
    id="{{ $name }}"
    name="{{ $fieldName }}"
    @if ($multiple)
        multiple
        size="6"
    @endif
    {{ $attributes->merge([
        'class' => "
            w-full
            rounded-xl
            border
            bg-white
            px-3.5
            py-2.5
            text-sm
            text-board-900
            transition-colors
            duration-200
            focus:border-board-600
            focus:outline-none
            focus:ring-2
            focus:ring-pencil-400/40
            disabled:cursor-not-allowed
            disabled:bg-board-50
            disabled:text-board-400
            {$borderClass}
        "
    ]) }}
>
    {{-- Placeholder hanya digunakan pada pilihan tunggal --}}
    @unless ($multiple)
        <option value="">
            {{ $placeholder }}
        </option>
    @endunless

    {{-- Menampilkan seluruh pilihan yang tersedia --}}
    @foreach ($options as $key => $label)
        <option
            value="{{ $key }}"
            @selected(in_array((string) $key, $selectedValues, true))
        >
            {{ $label }}
        </option>
    @endforeach
</select>