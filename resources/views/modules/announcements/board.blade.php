<x-layouts.app title="Pengumuman" :breadcrumbs="['Pengumuman' => null]">
    <div class="max-w-3xl space-y-4">
        @forelse ($items as $item)
            <x-ui.card>
                <h2 class="font-display text-xl font-semibold">{{ $item->title }}</h2>
                <p class="mt-0.5 text-xs text-board-600">
                    {{ $item->published_at?->format('d/m/Y H:i') }} · untuk {{ \App\Models\Announcement::AUDIENCES[$item->audience] ?? $item->audience }}
                </p>
                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed">{{ $item->body }}</p>
            </x-ui.card>
        @empty
            <x-ui.card><p class="text-sm text-board-600">Belum ada pengumuman.</p></x-ui.card>
        @endforelse

        {{ $items->links() }}
    </div>
</x-layouts.app>
