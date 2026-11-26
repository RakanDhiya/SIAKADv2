@php
    $activeSemester = \App\Models\Semester::active();
    $user = auth()->user();

    $initial = collect(explode(' ', trim($user->name)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');
@endphp

<header class="sticky top-0 z-30 flex h-[68px] shrink-0 items-center justify-between gap-4 border-b border-[#E8ECE9] bg-white px-4 sm:px-6 lg:px-8 print:hidden">

    {{-- Kiri: Informasi semester --}}
    <div class="flex min-w-0 items-center gap-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E5F2ED] text-[#159B82]">
            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 10h18"/>
            </svg>
        </div>

        <div class="min-w-0">
            <p class="text-[9px] font-semibold uppercase tracking-[0.13em] text-[#91A099]">
                Semester aktif
            </p>

            <p class="mt-0.5 truncate text-xs font-semibold text-[#263F35] sm:text-sm">
                {{ $activeSemester?->label ?? 'Belum ada semester aktif' }}
            </p>
        </div>
    </div>

    {{-- Kanan: Profil pengguna --}}
    <div class="flex shrink-0 items-center gap-3 sm:gap-5">

        {{-- Profil --}}
        <div class="flex min-w-0 items-center gap-2.5 sm:gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#E5F2ED] text-xs font-bold text-[#159B82] ring-1 ring-[#D5E8DF]">
                {{ $initial }}
            </div>

            <div class="hidden min-w-0 sm:block">
                <p class="max-w-[160px] truncate text-xs font-semibold text-[#263F35]">
                    {{ $user->name }}
                </p>

                <p class="mt-0.5 text-[10px] text-[#89938F]">
                    {{ $user->roleLabel() }}
                </p>
            </div>
        </div>

        {{-- Pemisah --}}
        <div class="hidden h-8 w-px bg-[#E8ECE9] sm:block"></div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                aria-label="Keluar"
                title="Keluar"
                class="flex h-9 w-9 items-center justify-center rounded-xl text-[#89938F] transition duration-200 hover:bg-[#FCEFEB] hover:text-[#D66B58]"
            >
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path d="M10 17l5-5-5-5M15 12H3"/>
                    <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
                </svg>
            </button>
        </form>
    </div>
</header>