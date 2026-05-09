@extends('layouts.guest')
@section('title', 'Password Dimenticata')
@section('content')
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row">

            <div class="w-64 h-64 mb-[-2rem] z-10 relative">
                <div id="model-skeleton" class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>
                <canvas id="live2d-canvas"
                        class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>
            </div>

            <form method="POST" action="{{ route('password.email') }}" class="mt-5">
                @csrf

                <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4 relative z-20">
                    <legend class="fieldset-legend">Recupera password</legend>

                    <p class="text-sm text-base-content/70 mb-4">
                        Inserisci la tua email e ti manderemo un link per reimpostare la password.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <label class="label">Email</label>
                    <input type="email" name="email" class="input w-full" placeholder="Email"
                           value="{{ old('email') }}" required autofocus />

                    @if ($errors->any())
                        <div class="p-2 text-center">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-error text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button class="btn btn-neutral mt-4 w-full" type="submit">Invia link di reset</button>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="link link-hover text-sm text-base-content/60">
                            Torna al login
                        </a>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
@endsection
