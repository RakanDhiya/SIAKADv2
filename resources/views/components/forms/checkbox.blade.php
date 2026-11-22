@props([
    'name',
    'label',
    'checked' => false,
])

@php
    $isChecked = session()->hasOldInput()
        ? (bool) old($name)
        : (bool) $checked;
@endphp

<label class="inline-flex items-center gap-2 text-sm">
    <input
        type="checkbox"
        name="{{ $name }}"
        value="1"
        @checked($isChecked)
        class="h-4 w-4 rounded border-board-300 text-board-700 focus:ring-pencil-400"
    >

    {{ $label }}
</label>