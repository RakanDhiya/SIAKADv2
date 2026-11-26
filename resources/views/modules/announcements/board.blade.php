<x-layouts.app title="Pengumuman" :breadcrumbs="['Pengumuman' => null]">

    <div class="mx-auto max-w-5xl space-y-5">

        {{-- Informasi halaman --}}
        <section class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm leading-relaxed text-[#7C8983]">
                    Informasi dan pemberitahuan terbaru dari pihak sekolah.
                </p>
                <p class="mt-1 text-[11px] text-[#A0A9A3]">
                    Pastikan untuk selalu membaca informasi terbaru.
                </p>
            </div>

            <div class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-[#DCE8E1] bg-[#EDF4F0] px-3 py-2">
                <svg class="h-4 w-4 text-[#28664A]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path d="M12 16v-4"/>
                    <path d="M12 8h.01"/>
                    <circle cx="12" cy="12" r="9"/>
                </svg>

                <span class="text-xs font-semibold text-[#28664A]">
                    {{ $items->total() }} Pengumuman
                </span>
            </div>
        </section>

        {{-- Daftar pengumuman --}}
        <section class="space-y-3">

            @forelse ($items as $item)
                <article class="overflow-hidden rounded-xl border border-[#E4EAE6] bg-white transition duration-200 hover:border-[#C8DCD0] hover:shadow-sm">

                    <div class="p-5 sm:p-6">

                        {{-- Judul dan kategori --}}
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#E5EEE9] text-[#28664A]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                    <circle cx="12" cy="12" r="9"/>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="text-sm font-semibold leading-relaxed text-[#174B3B] sm:text-base">
                                    {{ $item->title }}
                                </h2>

                                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1.5">

                                    <span class="inline-flex items-center gap-1.5 text-[10px] text-[#89938D]">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                                            <path d="M16 3v4M8 3v4M3 11h18"/>
                                        </svg>

                                        {{ $item->published_at?->format('d/m/Y') ?? 'Tanggal tidak tersedia' }}
                                    </span>

                                    @if ($item->published_at)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] text-[#89938D]">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                                <circle cx="12" cy="12" r="9"/>
                                                <path d="M12 7v5l3 2"/>
                                            </svg>

                                            {{ $item->published_at->format('H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <span class="hidden shrink-0 rounded-md border border-[#DCE8E1] bg-[#F2F7F4] px-2.5 py-1 text-[9px] font-semibold text-[#28664A] sm:inline-flex">
                                {{ \App\Models\Announcement::AUDIENCES[$item->audience] ?? $item->audience }}
                            </span>
                        </div>

                        {{-- Informasi penerima untuk layar kecil --}}
                        <div class="mt-3 sm:hidden">
                            <span class="inline-flex rounded-md border border-[#DCE8E1] bg-[#F2F7F4] px-2.5 py-1 text-[9px] font-semibold text-[#28664A]">
                                {{ \App\Models\Announcement::AUDIENCES[$item->audience] ?? $item->audience }}
                            </span>
                        </div>

                        {{-- Isi pengumuman --}}
                        <div class="mt-5 border-t border-[#EEF1EF] pt-4 sm:ml-[52px]">
                            <p class="whitespace-pre-line break-words text-xs leading-[1.9] text-[#65746C] sm:text-sm">
                                {{ $item->body }}
                            </p>
                        </div>

                    </div>
                </article>

            @empty
                {{-- Kondisi kosong --}}
                <div class="rounded-xl border border-dashed border-[#DCE5DF] bg-white px-6 py-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#EDF4F0] text-[#28664A]">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                            <circle cx="12" cy="12" r="9"/>
                        </svg>
                    </div>

                    <h2 class="mt-4 text-sm font-semibold text-[#034C34]">
                        Belum Ada Pengumuman
                    </h2>

                    <p class="mx-auto mt-1.5 max-w-sm text-xs leading-relaxed text-[#89938D]">
                        Belum ada informasi atau pemberitahuan dari pihak sekolah.
                        Silakan periksa kembali nanti.
                    </p>
                </div>
            @endforelse

        </section>

        {{-- Pagination --}}
        @if ($items->hasPages())
            <div class="border-t border-[#E5EAE7] pt-4">
                {{ $items->links() }}
            </div>
        @endif

    </div>
</x-layouts.app>