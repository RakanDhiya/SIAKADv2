<x-layouts.guest title="Halaman tidak ditemukan">
    <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center p-6">
        <h1 class="font-display text-3xl font-semibold">Halaman tidak ditemukan</h1>
        <p class="mt-2 text-board-700">Data atau halaman yang Anda cari tidak ada, atau tidak dapat Anda akses.</p>
        <div class="mt-6"><x-ui.button :href="url('/')">Kembali ke beranda</x-ui.button></div>
    </div>
</x-layouts.guest>
