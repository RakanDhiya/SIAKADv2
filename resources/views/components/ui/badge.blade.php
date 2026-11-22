
@props([
    'color' => 'gray',
])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-700',
        'green' => 'bg-green-100 text-green-800',
        'red' => 'bg-red-100 text-red-800',
        'amber' => 'bg-amber-100 text-amber-800',
        'blue' => 'bg-blue-100 text-blue-800',
    ];
@endphp

<span
    {{ $attributes->merge([
        'class' => 'inline-block rounded px-2 py-0.5 text-xs font-medium '
            . ($colors[$color] ?? $colors['gray']),
    ]) }}
>
    {{ $slot }}
</span>