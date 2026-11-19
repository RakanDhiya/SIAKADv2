<x-layouts.app :title="$title" :breadcrumbs="[$title => null]">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-2">
            @if ($searchable)
                <input type="search" name="q" value="{{ $q }}" placeholder="Cari…"
                       class="w-48 rounded border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-600 focus:outline-none focus:ring-2 focus:ring-pencil-400/50">
            @endif
            @foreach ($filters as $name => $filter)
                <select name="{{ $name }}" aria-label="{{ $filter['label'] }}"
                        class="max-w-52 rounded border border-board-200 bg-white px-3 py-2 text-sm focus:border-board-600 focus:outline-none">
                    <option value="">{{ $filter['label'] }}: semua</option>
                    @foreach ($filter['options'] as $key => $label)
                        <option value="{{ $key }}" @selected((string) request($name) === (string) $key)>{{ $label }}</option>
                    @endforeach
                </select>
            @endforeach
            @if ($searchable || $filters)
                <x-ui.button type="submit" variant="secondary">Terapkan</x-ui.button>
                @if (request()->query())
                    <x-ui.button :href="route($routeName.'.index')" variant="ghost">Atur ulang</x-ui.button>
                @endif
            @endif
        </form>

        @if ($canWrite)
            <x-ui.button :href="route($routeName.'.create')">Tambah {{ mb_strtolower($singular) }}</x-ui.button>
        @endif
    </div>

    <x-ui.card :flush="true">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-board-50 text-left text-board-800">
                    <tr>
                        <th class="w-12 px-4 py-2.5 font-semibold">No</th>
                        @foreach ($columns as $label => $key)
                            <th class="px-4 py-2.5 font-semibold">{{ $label }}</th>
                        @endforeach
                        <th class="px-4 py-2.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-board-100">
                    @forelse ($rows as $row)
                        <tr class="hover:bg-board-50/60">
                            <td class="px-4 py-2.5 text-board-600">{{ $rows->firstItem() + $loop->index }}</td>
                            @foreach ($columns as $label => $key)
                                @php
                                    $value = $key instanceof \Closure ? $key($row) : data_get($row, $key);
                                    if ($value instanceof \DateTimeInterface) {
                                        $value = $value->format('d/m/Y');
                                    }
                                @endphp
                                <td class="px-4 py-2.5">{{ $value === null || $value === '' ? '—' : $value }}</td>
                            @endforeach
                            <td class="whitespace-nowrap px-4 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @foreach ($rowActions($row) as $action)
                                        <x-ui.button :href="$action['url']" variant="secondary" size="sm">{{ $action['label'] }}</x-ui.button>
                                    @endforeach
                                    @if ($showable)
                                        <x-ui.button :href="route($routeName.'.show', $row->getKey())" variant="secondary" size="sm">Detail</x-ui.button>
                                    @endif
                                    @if ($canWrite)
                                        <x-ui.button :href="route($routeName.'.edit', $row->getKey())" variant="secondary" size="sm">Edit</x-ui.button>
                                        <form method="POST" action="{{ route($routeName.'.destroy', $row->getKey()) }}"
                                              onsubmit="return confirm('Hapus {{ mb_strtolower($singular) }} ini? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="danger" size="sm">Hapus</x-ui.button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 2 }}" class="px-4 py-10 text-center text-board-600">
                                Belum ada data{{ request()->query() ? ' yang cocok dengan pencarian atau filter' : '' }}.
                                @if ($canWrite && ! request()->query())
                                    <a href="{{ route($routeName.'.create') }}" class="font-medium text-board-700 underline">Tambah {{ mb_strtolower($singular) }} pertama.</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div class="mt-4">{{ $rows->links() }}</div>
</x-layouts.app>
