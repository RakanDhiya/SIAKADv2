@props(['title' => 'SIAKAD', 'breadcrumbs' => []])
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
<div class="min-h-screen lg:flex">
    <x-layouts.sidebar />

    <div class="flex min-w-0 flex-1 flex-col">
        <x-layouts.navbar />

        <main class="flex-1 p-4 lg:p-8">
            <nav aria-label="Breadcrumb" class="mb-3 text-sm text-board-600 print:hidden">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a></li>
                    @foreach ($breadcrumbs as $label => $url)
                        <li aria-hidden="true">/</li>
                        <li>
                            @if ($url)
                                <a href="{{ $url }}" class="hover:underline">{{ $label }}</a>
                            @else
                                <span class="text-board-900">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h1 class="mb-5 font-display text-2xl font-semibold text-board-900">{{ $title }}</h1>

            @if (session('success'))
                <x-ui.alert type="success" class="mb-4 print:hidden">{{ session('success') }}</x-ui.alert>
            @endif
            @if (session('error'))
                <x-ui.alert type="error" class="mb-4 print:hidden">{{ session('error') }}</x-ui.alert>
            @endif
            @if ($errors->any())
                <x-ui.alert type="error" class="mb-4 print:hidden">Data belum bisa disimpan. Periksa isian yang ditandai merah.</x-ui.alert>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
