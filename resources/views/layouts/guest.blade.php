<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'PlantApp') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-sm">

    {{-- Logo --}}
    <div class="flex flex-col items-center gap-2 mb-8">
        <img src="{{ asset('assets/NaHida_Logo.png') }}" alt="Logo" class="w-16 h-16 object-contain">
        <span class="text-xl font-semibold tracking-tight">{{ config('app.name', 'PlantApp') }}</span>
    </div>

    {{-- Card contenuto (login / register) --}}
    <div class="card bg-base-100 shadow-md">
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>

</div>

</body>
</html>
