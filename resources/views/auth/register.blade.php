@extends('layouts.guest')
@section('title', $title)
@section('content')
    <div class="hero bg-base-200 min-h-screen">
    <div class="hero-content flex-col lg:flex-row">
        <!-- Contenitore del modello! -->
        <div class="w-64 h-64 mb-[-2rem] z-10 relative">

            <!-- Skeleton di caricamento -->
            <div id="model-skeleton" class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>

            <canvas id="live2d-canvas"
                    class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>

        </div>

        <form method="POST" action="{{ route('register') }}" class="mt-5">
            @csrf

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4 relative z-20">
                <legend class="fieldset-legend">Registarti a NaHida</legend>

                <label class="label">Nome</label>
                <input type="text" name="first_name" class="input" placeholder="Nome" />

                <label class="label">Cognome</label>
                <input type="text" name="last_name" class="input" placeholder="Cognome" />

                <label class="label">Email</label>
                <input type="email" name="email" class="input" placeholder="Email" />

                <label class="label">Password</label>
                <input type="password" name="password" class="input" id="passwordInput" placeholder="Password"/>

                <label class="label">Conferma Password</label>
                <input type="password" name="password_confirmation" class="input" id="passwordInput" placeholder="Password"/>

                <a href="{{ route('login') }}" class="label-text-alt link link-hover">Hai un account? Loggati</a>

                @if ($errors->any())
                    <div class="p-2 text-center">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li style="color: red;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button class="btn btn-neutral mt-4" type="submit">Registrati</button>
            </fieldset>
        </form>
    </div>
</div>

@vite(["resources/js/pages/login_register.js"])
@endsection
