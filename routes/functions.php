<?php
use Illuminate\Contracts\View\View;

function renderPage($page = "index", $parametri = [
    'title' => 'NaHida'
]) : View {
    return view($page, [
        'version' => env('VERSION', '1.0.0'),
        'title' => $parametri["title"] ?? config("app.name", "NaHida"),
        'name' => config("app.name", "NaHida"),
        'params' => $parametri
    ]);
};
