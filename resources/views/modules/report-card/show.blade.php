<x-layouts.app title="Rapor" :breadcrumbs="['Rapor' => null]">
    <form method="GET" action="{{ route('report-card') }}" class="mb-4 flex flex-wrap items-end gap-2 print:hidden">
        @if ($students->count() > 1)
            <x-forms.field label="Siswa" name="student" class="min-w-64">
                <x-forms.select name="student" :options="$students->mapWithKeys(fn ($s) => [$s->id => $s->name.' ('.$s->nis.')'])->all()" :value="$student?->id" placeholder="— Pilih siswa —" />
            </x-forms.field>
        @endif
        <x-forms.field label="Semester" name="semester" class="min-w-52">
            <x-forms.select name="semester" :options="$semesters->mapWithKeys(fn ($s) => [$s->id => $s->label])->all()" :value="$semester?->id" placeholder="— Pilih semester —" />
        </x-forms.field>
        <x-ui.button type="submit" variant="secondary">Tampilkan</x-ui.button>
        @if ($student && $semester)
            <x-ui.button type="button" onclick="window.print()">Cetak rapor</x-ui.button>
        @endif
    </form>

    @if (! $student)
        <x-ui.alert type="warning">Belum ada data siswa yang dapat ditampilkan untuk akun ini.</x-ui.alert>
    @elseif (! $semester)
        <x-ui.alert type="info">Belum ada semester. Admin perlu menambahkan tahun ajaran dan semester.</x-ui.alert>
    @else
        <article class="max-w-3xl rounded-md border border-board-100 bg-white p-6 print:max-w-none print:border-0 print:p-0">
            <header class="mb-5 border-b border-board-200 pb-4 text-center">
                <h2 class="font-display text-2xl font-semibold">Laporan Hasil Belajar Siswa</h2>
                <p class="text-sm text-board-600">Semester {{ $semester->label }}</p>
            </header>

            <dl class="mb-5 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm sm:grid-cols-[auto_1fr_auto_1fr]">
                <dt class="text-board-600">Nama</dt><dd class="font-medium">{{ $student->name }}</dd>
                <dt class="text-board-600">Kelas</dt><dd class="font-medium">{{ $classroom->name ?? '—' }}</dd>
                <dt class="text-board-600">NIS / NISN</dt><dd class="font-medium">{{ $student->nis }}{{ $student->nisn ? ' / '.$student->nisn : '' }}</dd>
                <dt class="text-board-600">Wali kelas</dt><dd class="font-medium">{{ $classroom?->homeroomTeacher?->name ?? '—' }}</dd>
            </dl>

            <table class="min-w-full text-sm">
                <thead class="border-y border-board-200 bg-board-50 text-left">
                    <tr>
                        <th class="px-3 py-2 font-semibold">No</th>
                        <th class="px-3 py-2 font-semibold">Mata pelajaran</th>
                        <th class="px-3 py-2 text-right font-semibold">KKM</th>
                        <th class="px-3 py-2 text-right font-semibold">Nilai akhir</th>
                        <th class="px-3 py-2 font-semibold">Predikat</th>
                        <th class="px-3 py-2 font-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-board-100">
                    @forelse ($grades as $g)
                        <tr>
                            <td class="px-3 py-2">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2">{{ $g->subject->name }}</td>
                            <td class="px-3 py-2 text-right">{{ $g->subject->kkm }}</td>
                            <td class="px-3 py-2 text-right font-medium">{{ $g->final_score }}</td>
                            <td class="px-3 py-2">{{ $g->predicate }}</td>
                            <td class="px-3 py-2">{{ $g->remarks }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-8 text-center text-board-600">Nilai rapor semester ini belum tersedia.</td></tr>
                    @endforelse
                </tbody>
                @if ($grades->isNotEmpty())
                    <tfoot class="border-t border-board-200">
                        <tr>
                            <td colspan="3" class="px-3 py-2 text-right font-medium">Rata-rata</td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($average, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>

            <section class="mt-5 text-sm">
                <h3 class="mb-1 font-semibold">Rekap kehadiran</h3>
                <p>
                    @foreach (\App\Models\Attendance::STATUSES as $key => $label)
                        {{ $label }}: <strong>{{ $attendance[$key] ?? 0 }}</strong>@if (! $loop->last) · @endif
                    @endforeach
                </p>
            </section>
        </article>
    @endif
</x-layouts.app>
