@extends('layouts.guest')
@section('title', 'Conferma Password')
@section('content')
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row">

            <div class="w-64 h-64 mb-[-2rem] z-10 relative">
                <div id="model-skeleton" class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>
                <canvas id="live2d-canvas"
                        class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="mt-5">
                @csrf

                <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4 relative z-20">
                    <legend class="fieldset-legend">Conferma la tua password</legend>

                    <p class="text-sm text-base-content/70 mb-4">
                        Quest'area è protetta. Inserisci la tua password per continuare.
                    </p>

                    <label class="label">Password</label>
                    <input type="password" name="password" id="passwordInput" class="input w-full" placeholder="Password" required autocomplete="current-password"/>

                    @if ($errors->any())
                        <div class="p-2 text-center">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-error text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button class="btn btn-neutral mt-4 w-full" type="submit">Conferma</button>
                </fieldset>
            </form>
        </div>
    </div>

    @vite(["resources/js/pages/login_register.js"])
@endsection
