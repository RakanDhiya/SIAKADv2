@props(['title' => null, 'flush' => false])
<section {{ $attributes->merge(['class' => 'rounded-md border border-board-100 bg-white']) }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-board-100 px-5 py-3">
            <h2 class="font-semibold text-board-900">{{ $title }}</h2>
            {{ $actions ?? '' }}
        </header>
    @endif
    <div class="{{ $flush ? '' : 'p-5' }}">{{ $slot }}</div>
</section>
