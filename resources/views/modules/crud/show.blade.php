<x-layouts.app :title="'Detail '.mb_strtolower($singular)"
               :breadcrumbs="[$title => route($routeName.'.index'), 'Detail' => null]">
    <x-ui.card class="max-w-3xl">
        <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
            @foreach ($rows as $label => $value)
                <div>
                    <dt class="text-sm text-board-600">{{ $label }}</dt>
                    <dd class="mt-0.5 font-medium text-board-900">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
        <div class="mt-6 flex gap-2">
            @if ($canWrite)
                <x-ui.button :href="route($routeName.'.edit', $model->getKey())">Edit</x-ui.button>
            @endif
            <x-ui.button :href="route($routeName.'.index')" variant="secondary">Kembali</x-ui.button>
        </div>
    </x-ui.card>
</x-layouts.app>
