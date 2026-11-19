@props(['name', 'type' => 'text', 'value' => null])
<input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
       value="{{ $type === 'password' ? '' : old($name, $value) }}"
       {{ $attributes->merge(['class' => 'w-full rounded border bg-white px-3 py-2 text-sm focus:border-board-600 focus:outline-none focus:ring-2 focus:ring-pencil-400/50 '.($errors->has($name) ? 'border-red-500' : 'border-board-200')]) }}>
