<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="params" content="{{ json_encode($params) }}">
    <meta name="env" content="{{ json_encode(collect($_ENV)->concat(getenv())->filter(fn($value,$key) => str_starts_with($key, 'VITE_'))->all()) }}">
    @php
        $pageInt = explode(".", $page);
        $css = explode("_", $pageInt[sizeof($pageInt) - 1])[0];
    @endphp
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(file_exists(resource_path('css/'.$css.'.css')))
        @vite(['resources/css/'.$css.'.css'])
    @endif

    <link rel="icon" href="{{ asset('assets/icon.png')}}">
    <title>{{ $title ?? 'IOT Project' }}</title>
</head>
<body>
    @include('layouts.navbar')

    @include($page, $params)

    @include('layouts.footer')
</body>
</html>
