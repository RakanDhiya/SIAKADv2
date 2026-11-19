<x-layouts.app title="Nilai" :breadcrumbs="['Nilai' => null]">
    @include('modules.me.partials.student-picker', ['action' => route('me.scores')])

    @if (! $student)
        <x-ui.alert type="warning" class="mt-4">Belum ada data siswa yang terhubung dengan akun ini.</x-ui.alert>
    @elseif ($assignments->isEmpty())
        <x-ui.alert type="info" class="mt-4">Belum ada nilai untuk semester aktif.</x-ui.alert>
    @else
        <p class="my-4 text-sm text-board-600">{{ $student->name }} · {{ $semester->label }}</p>
        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($assignments as $a)
                @php $final = $finals->get($a->subject_id); @endphp
                <x-ui.card :title="$a->subject->name" :flush="true">
                    <x-slot:actions>
                        @if ($final)
                            <span class="text-sm">Nilai akhir <strong>{{ $final->final_score }}</strong> ({{ $final->predicate }}) · {{ $final->remarks }}</span>
                        @else
                            <span class="text-sm text-board-600">Nilai akhir belum dihitung</span>
                        @endif
                    </x-slot:actions>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-board-100">
                            @forelse ($a->components as $c)
                                <tr>
                                    <td class="px-5 py-2">{{ $c->name }} <span class="text-board-600">({{ rtrim(rtrim($c->weight, '0'), '.') }}%)</span></td>
                                    <td class="px-5 py-2 text-right font-medium">{{ $c->scores->first()?->score ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td class="px-5 py-3 text-board-600">Belum ada komponen penilaian.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-ui.card>
            @endforeach
        </div>
    @endif
</x-layouts.app>
