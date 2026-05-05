<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'NaHida'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <meta name="params" content="{{ json_encode($params ?? ['null' => 0]) }}">
    <meta name="env" content="{{ json_encode(collect($_ENV)->concat(getenv())->filter(fn($value,$key) => str_starts_with($key, 'VITE_'))->all()) }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen">

    @include('layouts.sidebar')

    <div class="md:ml-60 flex flex-col min-h-screen">
        <header class="navbar bg-base-100 border-b border-base-300 px-4 sticky top-0 z-30">
            <div class="flex-1">
                <span class="text-base font-semibold">@yield('title', config('app.name', 'NaHida'))</span>
            </div>
        </header>

        <main class="flex-1 p-4 pb-24 md:pb-6">
            @yield('content')
        </main>

    </div>

    @include('layouts.dock')

</body>
</html>
