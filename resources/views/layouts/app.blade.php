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
<body class="font-sans antialiased bg-base-200 min-h-screen">

{{-- ===================== SIDEBAR (solo desktop md+) ===================== --}}
<aside class="hidden md:flex flex-col fixed top-0 left-0 h-full w-60 bg-base-100 border-r border-base-300 z-40 py-6 px-4 gap-2">

    {{-- Logo / nome app --}}
    <div class="flex items-center gap-3 px-2 mb-6">
        <img src="{{ asset('assets/NaHida_Logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
        <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'PlantApp') }}</span>
    </div>

    {{-- Voci di navigazione --}}
    <nav class="flex flex-col gap-1 flex-1">

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-primary text-primary-content' : 'hover:bg-base-200 text-base-content' }}">
            {{-- Icona Home --}}
            <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="1 11 12 2 23 11"></polyline>
                <path d="M5 13v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"></path>
                <line x1="12" y1="22" x2="12" y2="18"></line>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('plants.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('plants.index') ? 'bg-primary text-primary-content' : 'hover:bg-base-200 text-base-content' }}">
            {{-- Icona Piante (leaf) --}}
            <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22V12"></path>
                <path d="M5 12C5 7 8 3 12 3c4 0 7 4 7 9-2 0-5-1-7-3-2 2-5 3-7 3Z"></path>
            </svg>
            Le mie piante
        </a>

        <a href="{{ route('plants.create') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('plants.create') ? 'bg-primary text-primary-content' : 'hover:bg-base-200 text-base-content' }}">
            {{-- Icona Aggiungi --}}
            <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Aggiungi pianta
        </a>

        <a href="{{ route('settings') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('settings') ? 'bg-primary text-primary-content' : 'hover:bg-base-200 text-base-content' }}">
            {{-- Icona Impostazioni --}}
            <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"></path>
            </svg>
            Impostazioni
        </a>

    </nav>

    {{-- Utente + logout in fondo --}}
    <div class="border-t border-base-300 pt-4 mt-2">
        <div class="flex items-center gap-3 px-2 mb-3">
            <div class="avatar placeholder">
                <div class="bg-primary text-primary-content rounded-full w-8 text-xs flex items-center justify-center">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-base-content/50 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-3 w-full px-3 py-2 rounded-xl text-sm text-error hover:bg-error/10 transition-colors">
                <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Esci
            </button>
        </form>
    </div>

</aside>

{{-- ===================== CONTENUTO PRINCIPALE ===================== --}}
<div class="md:ml-60 flex flex-col min-h-screen">

    {{-- Top navbar (titolo pagina) --}}
    <header class="navbar bg-base-100 border-b border-base-300 px-4 sticky top-0 z-30">
        <div class="flex-1">
            <span class="text-base font-semibold">{{ $title ?? config('app.name', 'PlantApp') }}</span>
        </div>
    </header>

    {{-- Contenuto view --}}
    <main class="flex-1 p-4 pb-24 md:pb-6">
        {{ $slot }}
    </main>

</div>

{{-- ===================== DOCK (solo mobile, sotto md) ===================== --}}
<div class="dock md:hidden">

    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? 'dock-active' : '' }}">
        <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="1 11 12 2 23 11"></polyline>
            <path d="M5 13v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"></path>
            <line x1="12" y1="22" x2="12" y2="18"></line>
        </svg>
        <span class="dock-label">Home</span>
    </a>

    <a href="{{ route('plants.index') }}"
       class="{{ request()->routeIs('plants.index') ? 'dock-active' : '' }}">
        <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22V12"></path>
            <path d="M5 12C5 7 8 3 12 3c4 0 7 4 7 9-2 0-5-1-7-3-2 2-5 3-7 3Z"></path>
        </svg>
        <span class="dock-label">Piante</span>
    </a>

    <a href="{{ route('plants.create') }}"
       class="{{ request()->routeIs('plants.create') ? 'dock-active' : '' }}">
        <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="16"></line>
            <line x1="8" y1="12" x2="16" y2="12"></line>
        </svg>
        <span class="dock-label">Aggiungi</span>
    </a>

    <a href="{{ route('settings') }}"
       class="{{ request()->routeIs('settings') ? 'dock-active' : '' }}">
        <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"></path>
        </svg>
        <span class="dock-label">Impostazioni</span>
    </a>

</div>

</body>
</html>
