@extends('layouts.guest')
@section('title', 'Reimposta Password')
@section('content')
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row">

            <div class="w-64 h-64 mb-[-2rem] z-10 relative">
                <div id="model-skeleton" class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>
                <canvas id="live2d-canvas"
                        class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4 relative z-20">
                    <legend class="fieldset-legend">Nuova password</legend>

                    <label class="label">Email</label>
                    <input type="email" name="email" class="input w-full" placeholder="Email"
                           value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"/>

                    <label class="label mt-2">Nuova Password</label>
                    <input type="password" name="password" id="passwordInput" class="input w-full"
                           placeholder="Nuova password" required autocomplete="new-password"/>

                    <label class="label mt-2">Conferma Password</label>
                    <input type="password" name="password_confirmation" class="input w-full"
                           placeholder="Conferma password" required autocomplete="new-password"/>

                    @if ($errors->any())
                        <div class="p-2 text-center">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-error text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button class="btn btn-neutral mt-4 w-full" type="submit">Reimposta password</button>
                </fieldset>
            </form>
        </div>
    </div>

    @vite(["resources/js/pages/login_register.js"])
@endsection
