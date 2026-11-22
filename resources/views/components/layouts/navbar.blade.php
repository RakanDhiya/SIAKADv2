@php
    /**
     * Mengambil informasi semester aktif untuk ditampilkan
     * pada bagian navigasi utama.
     */
    $activeSemester = \App\Models\Semester::active();
@endphp

<header class="sticky top-0 z-30 flex h-18 items-center justify-between gap-4 border-b border-line bg-white px-4 lg:px-8 print:hidden">

    {{-- Tombol navigasi mobile dan informasi semester --}}
    <div class="flex min-w-0 items-center gap-3">
        <button
            id="sidebar-toggle"
            type="button"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-board-600 transition-colors hover:bg-board-50 hover:text-board-900 focus:outline-none focus:ring-2 focus:ring-board-400 lg:hidden"
            aria-label="Buka menu navigasi"
            aria-controls="sidebar"
            aria-expanded="false"
        >
            <svg
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>
        </button>

        {{-- Informasi semester aktif --}}
        <div class="hidden min-w-0 sm:block">
            <p class="text-[11px] font-medium text-board-400">
                Semester aktif
            </p>

            <p class="truncate text-sm font-semibold text-board-800">
                {{ $activeSemester?->label ?? 'Belum ada semester aktif' }}
            </p>
        </div>
    </div>

    {{-- Informasi akun dan tindakan pengguna --}}
    <div class="flex min-w-0 items-center gap-2 sm:gap-4">

        {{-- Informasi pengguna --}}
        <div class="min-w-0 text-right">
            <p class="truncate text-sm font-semibold text-board-800">
                {{ auth()->user()->name }}
            </p>

            <p class="truncate text-xs text-board-500">
                {{ auth()->user()->roleLabel() }}
            </p>
        </div>

        {{-- Akses pengaturan akun --}}
        <a
            href="{{ route('account.password.edit') }}"
            class="hidden rounded-lg px-3 py-2 text-xs font-semibold text-board-600 transition-colors hover:bg-board-50 hover:text-board-900 sm:inline-flex"
        >
            Ganti password
        </a>

        {{-- Form logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-ui.button type="submit" variant="secondary" size="sm">
                Keluar
            </x-ui.button>
        </form>
    </div>
</header>