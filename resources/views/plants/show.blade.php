@extends("layouts.app")
@section('title', $title)
@section('content')
    <div class="lg:px-6 lg:pt-6 lg:grid lg:grid-cols-[1fr_1.1fr] lg:gap-6 lg:items-center">

        <div class="lg:sticky lg:top-20 flex flex-col gap-4">

            <!-- Card modello -->
            <div class="relative overflow-hidden bg-base-100 lg:rounded-2xl lg:shadow-md">

                <!-- Sfondo decorativo sfumato -->
                <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse at 50% 90%, color-mix(in srgb, var(--color-primary) 20%, transparent), transparent 65%), radial-gradient(ellipse at 85% 15%, color-mix(in srgb, var(--color-accent) 14%, transparent), transparent 55%);"></div>

                <!-- Header: nome + tasto modifica -->
                <div class="relative flex items-center justify-between px-5 pt-5 pb-1">
                    <div class="flex items-center gap-2">
                        <img src="assets/NaHida_Icon_Heart.png" class="w-5 h-5 object-contain" alt="" onerror="this.style.display='none'">
                        <h1 class="text-lg font-bold text-base-content">Monstera Deliciosa</h1>
                    </div>
                    <button class="btn btn-ghost btn-sm gap-1" onclick="document.getElementById('modal_edit_plant').showModal()">
                        <img src="assets/NaHida_Icon_Edit.png" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                        <span class="text-xs">Modifica</span>
                    </button>
                </div>

                <!-- Canvas Live2D -->
                <div class="relative flex justify-center px-6 pt-2 pb-4">
                    <div class="relative w-full max-w-xs" style="aspect-ratio: 1;">
                        <div id="model-skeleton" class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>
                        <canvas id="live2d-canvas" class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>
                    </div>
                </div>

                <!-- Badge stato -->
                <div class="relative flex justify-center pb-5">
                    <div class="flex items-center gap-2 bg-error/12 border border-error/25 rounded-full px-4 py-1.5">
                        <img src="assets/NaHida_Emoji_Sad.png" class="w-5 h-5 object-contain" alt="" onerror="this.style.display='none'">
                        <span class="text-sm font-bold text-error">Condizioni pessime</span>
                    </div>
                </div>

            </div>

            <!-- Prossima annaffiata — sotto il modello su desktop, nella colonna destra su mobile -->
            <div class="hidden lg:block">
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2 px-1">Prossima annaffiata</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="assets/NaHida_Icon_Clock.png" class="w-12 h-12 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                        <div class="flex-1">
                            <p class="font-bold text-base-content">Mercoledì 14 maggio</p>
                            <p class="text-sm text-base-content/60">alle 09:00 — tra 3 giorni</p>
                        </div>
                        <button class="btn btn-primary btn-sm flex-shrink-0" onclick="document.getElementById('modal_watered').showModal()">
                            Annaffiato!
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- ===== COLONNA DESTRA: CARDS ===== -->
        <div class="flex flex-col gap-5 px-4 pt-5 lg:px-0 lg:pt-0 pb-6">

            <!-- STATO SENSORI -->
            <div>
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Stato sensori</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 gap-3">
                        <div class="grid grid-cols-2 gap-3">

                            <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                <div class="flex items-center gap-1.5">
                                    <img src="assets/NaHida_Icon_Temp.png" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                    <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Temperatura</span>
                                </div>
                                <p class="text-2xl font-bold text-error">32°C</p>
                                <p class="text-xs text-base-content/40">Ottimale: 18 — 26°C</p>
                            </div>

                            <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                <div class="flex items-center gap-1.5">
                                    <img src="assets/NaHida_Icon_Humidity.png" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                    <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Umidità aria</span>
                                </div>
                                <p class="text-2xl font-bold text-warning">28%</p>
                                <p class="text-xs text-base-content/40">Ottimale: 50 — 80%</p>
                            </div>

                            <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                <div class="flex items-center gap-1.5">
                                    <img src="assets/NaHida_Icon_Soil.png" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                    <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Umidità suolo</span>
                                </div>
                                <p class="text-2xl font-bold text-success">55%</p>
                                <p class="text-xs text-base-content/40">Ottimale: 40 — 70%</p>
                            </div>

                            <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                <div class="flex items-center gap-1.5">
                                    <img src="assets/NaHida_Icon_Light.png" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                    <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Luminosità</span>
                                </div>
                                <p class="text-2xl font-bold text-success">620 lx</p>
                                <p class="text-xs text-base-content/40">Ottimale: 400 — 800 lx</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- PROSSIMA ANNAFFIATA — solo mobile -->
            <div class="lg:hidden">
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Prossima annaffiata</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="assets/NaHida_Icon_Clock.png" class="w-12 h-12 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                        <div class="flex-1">
                            <p class="font-bold text-base-content">Mercoledì 14 maggio</p>
                            <p class="text-sm text-base-content/60">alle 09:00 — tra 3 giorni</p>
                        </div>
                        <button class="btn btn-primary btn-sm flex-shrink-0" onclick="document.getElementById('modal_watered').showModal()">
                            Annaffiato!
                        </button>
                    </div>
                </div>
            </div>

            <!-- AZIONI (bento 2 colonne) -->
            <div>
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Azioni</p>
                <div class="grid grid-cols-2 gap-3">

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer"
                            onclick="document.getElementById('modal_history').showModal()">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="assets/NaHida_Icon_Clock.png" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
                            <span class="text-sm font-bold text-base-content">Storico</span>
                        </div>
                    </button>

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer"
                            onclick="document.getElementById('modal_device').showModal()">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="assets/NaHida_Icon_Device.png" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
                            <span class="text-sm font-bold text-base-content">Collega dispositivo</span>
                        </div>
                    </button>

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer"
                            onclick="document.getElementById('modal_music').showModal()">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="assets/NaHida_Icon_Music.png" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
                            <span class="text-sm font-bold text-base-content">Musica</span>
                        </div>
                    </button>

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer"
                            onclick="document.getElementById('modal_conditions').showModal()">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="assets/NaHida_Icon_Settings.png" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
                            <span class="text-sm font-bold text-base-content">Condizioni ottimali</span>
                        </div>
                    </button>

                    <!-- Note: tutta la larghezza, layout orizzontale -->
                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer col-span-2"
                            onclick="document.getElementById('modal_notes').showModal()">
                        <div class="card-body p-4 flex-row items-center gap-3">
                            <img src="assets/NaHida_Icon_Notes.png" class="w-9 h-9 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                            <div class="text-left flex-1">
                                <span class="text-sm font-bold text-base-content block">Note</span>
                                <span class="text-xs text-base-content/50">Pianta comprata il 3 marzo...</span>
                            </div>
                            <svg class="w-4 h-4 text-base-content/30 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                <path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/>
                            </svg>
                        </div>
                    </button>

                </div>
            </div>

        </div>
    </div>

    @vite(['resources/js/pages/plants_show.js'])
@endsection
