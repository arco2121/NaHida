@extends('layouts.guest')
@section('title', 'Verifica Email')
@section('content')
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row">

            <div class="w-64 h-64 mb-[-2rem] z-10 relative">
                <div id="model-skeleton" class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>
                <canvas id="live2d-canvas"
                        class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>
            </div>

            <div class="relative z-20 mt-5">
                <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4">
                    <legend class="fieldset-legend">Verifica la tua email</legend>

                    <p class="text-sm text-base-content/70 mb-4">
                        Grazie per esserti registrato! Prima di iniziare, verifica il tuo indirizzo email cliccando
                        sul link che ti abbiamo inviato. Se non hai ricevuto l'email, te ne mandiamo un'altra.
                    </p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mb-4">
                            <span>Un nuovo link di verifica è stato inviato al tuo indirizzo email.</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-neutral w-full">
                            Reinvia email di verifica
                        </button>
                    </form>

                    <div class="divider text-xs text-base-content/40">oppure</div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm w-full text-base-content/60">
                            Esci dall'account
                        </button>
                    </form>
                </fieldset>
            </div>
        </div>
    </div>
@endsection
