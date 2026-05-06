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
            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4 relative z-20">
                <legend class="fieldset-legend">Accedi a NaHida</legend>

                <label class="label">Email</label>
                <input type="email" class="input" placeholder="Email" />

                <label class="label">Password</label>
                <input type="password" class="input" placeholder="Password" onfocus="PlantViewer.setPasswordMode(true)"
                       onblur="PlantViewer.setPasswordMode(false)" />

                <button class="btn btn-neutral mt-4">Accedi</button>
            </fieldset>
        </div>
    </div>
@endsection
