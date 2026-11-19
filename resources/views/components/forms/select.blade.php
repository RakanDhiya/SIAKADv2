@props(['name', 'options' => [], 'value' => null, 'placeholder' => '— Pilih —', 'multiple' => false])
@php
    $selected = array_map('strval', (array) old($name, $value));
@endphp
<select id="{{ $name }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}" @if ($multiple) multiple size="6" @endif
        {{ $attributes->merge(['class' => 'w-full rounded border bg-white px-3 py-2 text-sm focus:border-board-600 focus:outline-none focus:ring-2 focus:ring-pencil-400/50 '.($errors->has($name) ? 'border-red-500' : 'border-board-200')]) }}>
    @unless ($multiple)
        <option value="">{{ $placeholder }}</option>
    @endunless
    @foreach ($options as $key => $label)
        <option value="{{ $key }}" @selected(in_array((string) $key, $selected, true))>{{ $label }}</option>
    @endforeach
</select>
