@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
    'size' => 'md',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-pencil-400 focus-visible:ring-offset-1 ';

    $sizes = [
        'sm' => 'px-2.5 py-1 text-sm',
        'md' => 'px-4 py-2 text-sm',
    ];

    $variants = [
        'primary' => 'bg-board-700 text-white hover:bg-board-800',
        'secondary' => 'border border-board-200 bg-white text-board-800 hover:bg-board-50',
        'danger' => 'bg-red-700 text-white hover:bg-red-800',
        'ghost' => 'text-board-700 hover:bg-board-100',
    ];

    $class = $base
        . ($sizes[$size] ?? $sizes['md'])
        . ' '
        . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $class]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $class]) }}
    >
        {{ $slot }}
    </button>
@endif