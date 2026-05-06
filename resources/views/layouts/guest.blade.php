<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'NaHida'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <meta name="params" content="{{ json_encode($params ?? ['null' => 0]) }}">
    <meta name="env"
          content="{{ json_encode(collect($_ENV)->concat(getenv())->filter(fn($value,$key) => str_starts_with($key, 'VITE_'))->all()) }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen flex items-center justify-center p-4">

    @include('components.theme_toggle')

    <div class="w-full max-w-sm">
        <div class="flex flex-col items-center gap-2 mb-8">
            <img src="{{ asset('assets/NaHida_Logo.png') }}" alt="Logo" class="w-16 h-16 object-contain">
            <span class="text-xl font-semibold tracking-tight">{{ config('app.name', 'NaHida') }}</span>
        </div>

        <div class="card bg-base-100 shadow-md">
            <main class="flex-1 p-4 pb-24 md:pb-6">
                @yield('content')
            </main>
        </div>

    </div>

</body>
</html>
