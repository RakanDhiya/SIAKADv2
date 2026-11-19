@php $activeSemester = \App\Models\Semester::active(); @endphp
<header class="flex items-center justify-between gap-3 border-b border-board-100 bg-white px-4 py-3 lg:px-8 print:hidden">
    <button type="button" class="rounded p-1.5 text-board-700 hover:bg-board-100 lg:hidden"
            aria-label="Buka menu"
            onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <div class="hidden text-sm text-board-600 lg:block">
        @if ($activeSemester)
            Semester aktif: <span class="font-medium text-board-900">{{ $activeSemester->label }}</span>
        @else
            Belum ada semester aktif
        @endif
    </div>

    <div class="flex items-center gap-3 text-sm">
        <div class="text-right leading-tight">
            <div class="font-medium">{{ auth()->user()->name }}</div>
            <div class="text-xs text-board-600">{{ auth()->user()->roleLabel() }}</div>
        </div>
        <a href="{{ route('account.password.edit') }}" class="rounded px-2 py-1 text-board-700 hover:bg-board-100">Ganti password</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="secondary" size="sm">Keluar</x-ui.button>
        </form>
    </div>
</header>
