<x-layouts.guest title="Masuk">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="hidden flex-col justify-between bg-board-800 p-12 text-board-100 lg:flex">
            <div class="flex items-center gap-2.5">
                <span class="inline-block h-6 w-6 rounded-sm bg-pencil-400" aria-hidden="true"></span>
                <span class="font-display text-xl font-semibold text-white">SMKN 5 TANGERANG SELATAN</span>
            </div>
            <div>
                <p class="font-display text-4xl font-semibold leading-tight text-white">Sistem informasi akademik sekolah</p>
                <p class="mt-4 max-w-md text-board-200">Jadwal, absensi, nilai, rapor, dan e-learning dalam satu tempat untuk admin, guru, siswa, dan orang tua.</p>
            </div>
            <p class="text-sm text-board-300">Akun dibuat oleh Admin.</p>
        </div>

        <div class="flex items-center justify-center p-6">
            <form method="POST" action="{{ route('login.store') }}" class="w-full max-w-sm space-y-5">
                @csrf
                <div>
                    <h1 class="font-display text-3xl font-semibold">Masuk</h1>
                    <p class="mt-1 text-sm text-board-600">Gunakan email dan password akun Anda.</p>
                </div>

                <x-forms.field label="Email" name="email" :required="true">
                    <x-forms.input type="email" name="email" autocomplete="username" autofocus />
                </x-forms.field>

                <x-forms.field label="Password" name="password" :required="true">
                    <x-forms.input type="password" name="password" autocomplete="current-password" />
                </x-forms.field>

                <x-forms.checkbox name="remember" label="Ingat saya di perangkat ini" />

                <x-ui.button type="submit" class="w-full">Masuk</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.guest>
