@php
    $action = $model->exists ? 'Edit' : 'Tambah';
@endphp

<x-layouts.app
    :title="$action . ' ' . mb_strtolower($singular)"
    :breadcrumbs="[
        $title => route($routeName . '.index'),
        $action => null,
    ]"
>
    <x-ui.card class="max-w-4xl">
        <form
            method="POST"
            enctype="multipart/form-data"
            action="{{ $model->exists
                ? route($routeName . '.update', $model->getKey())
                : route($routeName . '.store') }}"
            class="grid gap-5 md:grid-cols-2"
        >
            @csrf

            @if ($model->exists)
                @method('PUT')
            @endif

            @foreach ($fields as $f)
                @php
                    $type = $f['type'] ?? 'text';
                    $name = $f['name'];
                    $value = $values[$name] ?? null;
                    $wide = in_array($type, ['textarea', 'multiselect'], true);
                @endphp

                @if ($type === 'checkbox')
                    <div class="md:col-span-2">
                        <x-forms.checkbox
                            :name="$name"
                            :label="$f['label']"
                            :checked="(bool) $value"
                        />

                        @error($name)
                            <p class="mt-1 text-xs font-medium text-red-700">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @else
                    <x-forms.field
                        :label="$f['label']"
                        :name="$name"
                        :required="$f['required'] ?? false"
                        :hint="$f['hint'] ?? null"
                        :class="$wide ? 'md:col-span-2' : ''"
                    >
                        @if ($type === 'select')
                            <x-forms.select
                                :name="$name"
                                :options="$f['options'] ?? []"
                                :value="$value"
                            />
                        @elseif ($type === 'multiselect')
                            <x-forms.select
                                :name="$name"
                                :options="$f['options'] ?? []"
                                :value="$value"
                                :multiple="true"
                            />
                        @elseif ($type === 'textarea')
                            <x-forms.textarea
                                :name="$name"
                                :value="$value"
                            />
                        @elseif ($type === 'file')
                            <input
                                type="file"
                                id="{{ $name }}"
                                name="{{ $name }}"
                                class="block w-full text-sm file:mr-3 file:rounded file:border-0 file:bg-board-100 file:px-3 file:py-2 file:text-board-800"
                            >

                            @if ($model->exists && $model->{$name})
                                <p class="text-xs">
                                    <a
                                        class="font-medium text-board-700 underline"
                                        target="_blank"
                                        href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($model->{$name}) }}"
                                    >
                                        Berkas saat ini
                                    </a>
                                    — unggah berkas baru untuk menggantinya.
                                </p>
                            @endif
                        @else
                            <x-forms.input
                                :type="$type === 'datetime' ? 'datetime-local' : $type"
                                :name="$name"
                                :value="$value"
                                :min="$f['min'] ?? null"
                                :max="$f['max'] ?? null"
                                :step="$f['step'] ?? null"
                            />
                        @endif
                    </x-forms.field>
                @endif
            @endforeach

            <div class="flex items-center gap-2 md:col-span-2">
                <x-ui.button type="submit">
                    Simpan
                </x-ui.button>

                <x-ui.button
                    :href="route($routeName . '.index')"
                    variant="ghost"
                >
                    Batal
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>