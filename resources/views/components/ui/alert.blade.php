@props(['type' => 'info'])
@php
    $colors = [
        'success' => 'border-green-500 bg-green-50 text-green-900',
        'error'   => 'border-red-500 bg-red-50 text-red-900',
        'warning' => 'border-amber-500 bg-amber-50 text-amber-900',
        'info'    => 'border-board-400 bg-board-50 text-board-900',
    ];
@endphp
<div role="alert" {{ $attributes->merge(['class' => 'rounded border-l-4 px-4 py-3 text-sm '.($colors[$type] ?? $colors['info'])]) }}>{{ $slot }}</div>
