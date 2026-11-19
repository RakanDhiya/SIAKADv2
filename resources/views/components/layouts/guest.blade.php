@props(['title' => 'SIAKAD'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · SIAKAD</title>
    <x-layouts.head />
</head>
<body class="min-h-screen bg-paper font-sans text-board-900 antialiased">
    {{ $slot }}
</body>
</html>
