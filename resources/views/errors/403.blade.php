<x-layouts.guest title="Akses ditolak">
    <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center p-6">
        <h1 class="font-display text-3xl font-semibold">Akses ditolak</h1>
        <p class="mt-2 text-board-700">{{ $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}</p>
        <div class="mt-6"><x-ui.button :href="url('/')">Kembali ke beranda</x-ui.button></div>
    </div>
</x-layouts.guest>
