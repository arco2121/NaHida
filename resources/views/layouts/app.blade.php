<!DOCTYPE html>
<html class="scrollbar-hide" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
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
    @include('components.live2d_import')
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen">

    <div class="w-full p-4">
        <main class="flex-1 p-4 pb-24 md:pb-6">
            @yield('content')
        </main>

    </div>

    @include('components.dock')

</body>
</html>
