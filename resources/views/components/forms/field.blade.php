@props(['label', 'name', 'required' => false, 'hint' => null])
<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-board-900">
        {{ $label }}@if ($required)<span class="text-red-700"> *</span>@endif
    </label>
    {{ $slot }}
    @if ($hint)
        <p class="text-xs text-board-600">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="text-xs font-medium text-red-700">{{ $message }}</p>
    @enderror
</div>
