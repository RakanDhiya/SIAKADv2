<x-layouts.app title="Ganti password" :breadcrumbs="['Ganti password' => null]">
    <x-ui.card class="max-w-md">
        <form method="POST" action="{{ route('account.password.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <x-forms.field label="Password saat ini" name="current_password" :required="true">
                <x-forms.input type="password" name="current_password" autocomplete="current-password" />
            </x-forms.field>

            <x-forms.field label="Password baru" name="password" :required="true" hint="Minimal 8 karakter.">
                <x-forms.input type="password" name="password" autocomplete="new-password" />
            </x-forms.field>

            <x-forms.field label="Ulangi password baru" name="password_confirmation" :required="true">
                <x-forms.input type="password" name="password_confirmation" autocomplete="new-password" />
            </x-forms.field>

            <x-ui.button type="submit">Simpan password</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.app>
