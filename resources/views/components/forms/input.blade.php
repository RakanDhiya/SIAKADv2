@props([
    'name',
    'type' => 'text',
    'value' => null,
])

@php
       /**
       * Menentukan nilai input berdasarkan old input atau nilai default.
       * Field password selalu dikosongkan demi keamanan.
       */
       $inputValue = $type === 'password'
              ? ''
              : old($name, $value);

       /**
       * Menyesuaikan warna border berdasarkan status validasi.
       */
       $borderClass = $errors->has($name)
              ? 'border-red-500'
              : 'border-board-200';
@endphp

<input
       id="{{ $name }}"
       name="{{ $name }}"
       type="{{ $type }}"
       value="{{ $inputValue }}"
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
              placeholder:text-board-400
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