@props(['title' => 'SIAKAD', 'breadcrumbs' => []])

@php
    $isDashboard = request()->routeIs('dashboard');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · SIAKAD</title>
    <x-layouts.head />
</head>

<body class="bg-paper font-sans text-board-900 antialiased print:bg-white">
    <div class="min-h-screen bg-[#F7F9F8] lg:flex">

        {{-- Sidebar navigasi --}}
        <x-layouts.sidebar />

        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Navbar utama --}}
            <x-layouts.navbar />

            {{-- Konten halaman --}}
            <main
                id="main-content"
                @class([
                    'flex-1',
                    'p-0' => $isDashboard,
                    'px-4 py-6 sm:px-6 lg:px-8 lg:py-8' => !$isDashboard,
                ])
            >
                <div @class([
                    'w-full' => $isDashboard,
                    'mx-auto w-full max-w-[1600px]' => !$isDashboard,
                ])>

                    {{-- Breadcrumb dan judul halaman --}}
                    @unless ($isDashboard)
                        <nav
                            aria-label="Breadcrumb"
                            class="mb-4 text-xs font-medium text-board-400 print:hidden"
                        >
                            <ol class="flex flex-wrap items-center gap-2">
                                <li>
                                    <a
                                        href="{{ route('dashboard') }}"
                                        class="transition-colors hover:text-board-700"
                                    >
                                        Beranda
                                    </a>
                                </li>

                                @foreach ($breadcrumbs as $label => $url)
                                    <li aria-hidden="true" class="text-board-300">
                                        /
                                    </li>

                                    <li>
                                        @if ($url)
                                            <a
                                                href="{{ $url }}"
                                                class="transition-colors hover:text-board-700"
                                            >
                                                {{ $label }}
                                            </a>
                                        @else
                                            <span
                                                class="font-semibold text-board-800"
                                                aria-current="page"
                                            >
                                                {{ $label }}
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </nav>

                        <h1 class="mb-6 font-display text-2xl font-semibold tracking-tight text-board-900 sm:text-3xl">
                            {{ $title }}
                        </h1>
                    @endunless

                    {{-- Notifikasi berhasil --}}
                    @if (session('success'))
                        <div @class(['px-4 pt-4' => $isDashboard])>
                            <x-ui.alert
                                type="success"
                                class="mb-5 print:hidden"
                            >
                                {{ session('success') }}
                            </x-ui.alert>
                        </div>
                    @endif

                    {{-- Notifikasi kesalahan --}}
                    @if (session('error'))
                        <div @class(['px-4 pt-4' => $isDashboard])>
                            <x-ui.alert
                                type="error"
                                class="mb-5 print:hidden"
                            >
                                {{ session('error') }}
                            </x-ui.alert>
                        </div>
                    @endif

                    {{-- Ringkasan kesalahan validasi --}}
                    @if ($errors->any())
                        <div @class(['px-4 pt-4' => $isDashboard])>
                            <x-ui.alert
                                type="error"
                                class="mb-5 print:hidden"
                            >
                                Data belum bisa disimpan. Periksa isian yang ditandai merah.
                            </x-ui.alert>
                        </div>
                    @endif

                    {{-- Konten utama setiap halaman --}}
                    {{ $slot }}

                </div>
            </main>
        </div>
    </div>
</body>
</html>