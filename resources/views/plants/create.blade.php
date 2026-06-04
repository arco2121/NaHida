@extends('layouts.app')
@section('title', $title)
@section('content')

    {{-- Form principale: wrappa tutto il contenuto --}}
    <form id="plant-form" method="POST" action="{{ route('plants.store') }}">
        @csrf

        {{-- Hidden inputs aggiornati da JS prima dell'invio --}}
        <input type="hidden" name="watering_cycle" id="watering_cycle_hidden" value="">

        <div class="px-4 pt-6 flex flex-col gap-4 lg:grid lg:grid-cols-[1fr_1.2fr] lg:items-start lg:gap-6">

            {{-- ===== COLONNA SINISTRA: Preview + Personalizzazione ===== --}}
            <div class="flex flex-col gap-4 lg:sticky lg:top-20">

                <div class="relative overflow-hidden bg-base-100 rounded-2xl shadow">
                    <div class="absolute inset-0 pointer-events-none"
                         style="background: radial-gradient(ellipse at 50% 90%, color-mix(in srgb, var(--color-primary) 20%, transparent), transparent 65%), radial-gradient(ellipse at 85% 15%, color-mix(in srgb, var(--color-accent) 14%, transparent), transparent 55%);"></div>
                    <p class="relative text-xs font-bold text-base-content/50 uppercase tracking-wider px-5 pt-5 pb-1">
                        Anteprima</p>
                    <div class="relative flex justify-center px-6 pt-2 pb-4">
                        <div class="relative w-full max-w-xs" style="aspect-ratio:1;">
                            <div id="model-skeleton"
                                 class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>
                            <canvas id="live2d-canvas"
                                    class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow">
                    <div class="card-body p-5 gap-5">
                        <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider -mb-2">
                            Personalizzazione</p>

                        <div>
                            <div class="flex justify-between items-baseline mb-1">
                                <label class="text-sm font-bold">Variante pianta</label>
                                <span id="lbl_variant" class="text-sm font-bold text-primary">0</span>
                            </div>
                            <input type="range" id="range_variant" name="plant_variant"
                                   min="0" max="7" value="0" step="1" class="range w-full range-primary range-sm"/>
                        </div>

                        <div>
                            <div class="flex justify-between items-baseline mb-1">
                                <label class="text-sm font-bold">Colore pianta</label>
                                <span id="lbl_plant_color" class="text-sm font-bold text-accent">0</span>
                            </div>
                            <input type="range" id="range_plant_color" name="plant_color"
                                   min="0" max="5" value="0" step="1" class="range w-full range-accent range-sm"/>
                        </div>

                        <div>
                            <div class="flex justify-between items-baseline mb-1">
                                <label class="text-sm font-bold">Colore fiore</label>
                                <span id="lbl_flower" class="text-sm font-bold text-secondary">0</span>
                            </div>
                            <input type="range" id="range_flower" name="flower_color"
                                   min="0" max="6" value="0" step="1" class="range w-full range-secondary range-sm"/>
                        </div>

                        <div>
                            <div class="flex justify-between items-baseline mb-1">
                                <label class="text-sm font-bold">Colore vaso</label>
                                <span id="lbl_pot" class="text-sm font-bold">0</span>
                            </div>
                            <input type="range" id="range_pot" name="pot_color"
                                   min="0" max="2" value="0" step="1" class="range w-full range-sm"/>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ===== COLONNA DESTRA: Form dati ===== --}}
            <div class="flex flex-col gap-4 pb-24">

                {{-- Nome --}}
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-5 gap-3">
                        <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Nome pianta</p>
                        <input id="plant_name" name="plant_name" type="text"
                               class="input w-full @error('plant_name') input-error @enderror"
                               placeholder="es. Monstera Deliciosa"
                               value="{{ old('plant_name') }}"/>
                        @error('plant_name')
                        <p class="text-error text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Template condizioni --}}
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-5 gap-4">
                        <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Condizioni
                            ottimali</p>

                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" data-template="tropicale"
                                    class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                                <img src="{{ asset('assets/NaHida_Icon_Palm.png') }}" class="w-7 h-7 object-contain" alt="" onerror="this.style.display='none'">
                                <div>
                                    <p class="text-sm font-bold leading-tight">Tropicale</p>
                                    <p class="text-xs text-base-content/50">Caldo e umido</p>
                                </div>
                            </button>

                            <button type="button" data-template="mediterraneo"
                                    class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                                <img src="{{ asset('assets/NaHida_Icon_Umbrella.png') }}" class="w-7 h-7 object-contain" alt="" onerror="this.style.display='none'">
                                <div>
                                    <p class="text-sm font-bold leading-tight">Mediterraneo</p>
                                    <p class="text-xs text-base-content/50">Temperato e secco</p>
                                </div>
                            </button>

                            <button type="button" data-template="succulente"
                                    class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                                <img src="{{ asset('assets/NaHida_Icon_Light.png') }}" class="w-7 h-7 object-contain" alt="" onerror="this.style.display='none'">
                                <div>
                                    <p class="text-sm font-bold leading-tight">Succulente</p>
                                    <p class="text-xs text-base-content/50">Arido e soleggiato</p>
                                </div>
                            </button>

                            <!-- Preset: Basilico -->
                            <button type="button" data-template="basilico"
                                    class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                                <img src="{{ asset('assets/NaHida_Icon_Plant.png') }}" class="w-7 h-7 object-contain"
                                     alt="" onerror="this.style.display='none'">
                                <div>
                                    <p class="text-sm font-bold leading-tight">Basilico</p>
                                    <p class="text-xs text-base-content/50">Aromatico, soleggiato</p>
                                </div>
                            </button>

                            <!-- Preset: Monstera -->
                            <button type="button" data-template="monstera"
                                    class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                                <img src="{{ asset('assets/NaHida_Icon_Plant.png') }}" class="w-7 h-7 object-contain"
                                     alt="" onerror="this.style.display='none'">
                                <div>
                                    <p class="text-sm font-bold leading-tight">Monstera</p>
                                    <p class="text-xs text-base-content/50">Tropicale, umido</p>
                                </div>
                            </button>

                            <!-- Preset: Aloe Vera -->
                            <button type="button" data-template="aloe"
                                    class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                                <img src="{{ asset('assets/NaHida_Icon_Plant.png') }}" class="w-7 h-7 object-contain"
                                     alt="" onerror="this.style.display='none'">
                                <div>
                                    <p class="text-sm font-bold leading-tight">Aloe Vera</p>
                                    <p class="text-xs text-base-content/50">Arido, luminoso</p>
                                </div>
                            </button>

                            <!-- Preset: Lavanda -->
                            <button type="button" data-template="lavanda"
                                    class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                                <img src="{{ asset('assets/NaHida_Icon_Plant.png') }}" class="w-7 h-7 object-contain" alt="" onerror="this.style.display='none'">
                                <div>
                                    <p class="text-sm font-bold leading-tight">Lavanda</p>
                                    <p class="text-xs text-base-content/50">Rustica, clima secco</p>
                                </div>
                            </button>

                            <button type="button" data-template="custom"
                                    class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                                <img src="{{ asset('assets/NaHida_Icon_Edit.png') }}" class="w-7 h-7 object-contain" alt="" onerror="this.style.display='none'">
                                <div>
                                    <p class="text-sm font-bold leading-tight">Custom</p>
                                    <p class="text-xs text-base-content/50">Imposta tu i valori</p>
                                </div>
                            </button>
                        </div>

                        <div class="divider my-0 text-xs text-base-content/40">Parametri</div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Umidità
                                    aria min (%)</label>
                                <input id="hum_min" name="hum_min" type="number"
                                       class="input input-sm w-full @error('hum_min') input-error @enderror"
                                       placeholder="es. 50" value="{{ old('hum_min') }}"/>
                                @error('hum_min') <p class="text-error text-xs mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Umidità
                                    aria max (%)</label>
                                <input id="hum_max" name="hum_max" type="number"
                                       class="input input-sm w-full @error('hum_max') input-error @enderror"
                                       placeholder="es. 80" value="{{ old('hum_max') }}"/>
                                @error('hum_max') <p class="text-error text-xs mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Temperatura
                                    min (°C)</label>
                                <input id="temp_min" name="temp_min" type="number"
                                       class="input input-sm w-full @error('temp_min') input-error @enderror"
                                       placeholder="es. 18" value="{{ old('temp_min') }}"/>
                                @error('temp_min') <p class="text-error text-xs mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Temperatura
                                    max (°C)</label>
                                <input id="temp_max" name="temp_max" type="number"
                                       class="input input-sm w-full @error('temp_max') input-error @enderror"
                                       placeholder="es. 28" value="{{ old('temp_max') }}"/>
                                @error('temp_max') <p class="text-error text-xs mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Suolo
                                    min (%)</label>
                                <input id="soil_hum_min" name="soil_hum_min" type="number"
                                       class="input input-sm w-full @error('soil_hum_min') input-error @enderror"
                                       placeholder="es. 40" value="{{ old('soil_hum_min') }}"/>
                                @error('soil_hum_min') <p class="text-error text-xs mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Suolo
                                    max (%)</label>
                                <input id="soil_hum_max" name="soil_hum_max" type="number"
                                       class="input input-sm w-full @error('soil_hum_max') input-error @enderror"
                                       placeholder="es. 70" value="{{ old('soil_hum_max') }}"/>
                                @error('soil_hum_max') <p class="text-error text-xs mt-0.5">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Frequenza annaffiatura --}}
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-5 gap-3">
                        <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Frequenza
                            annaffiatura</p>
                        <div class="flex items-center gap-3">
                            <input id="water_interval" type="number" class="input flex-1" placeholder="es. 3" min="1"
                                   oninput="this.value = this.value !== '' ? Math.max(1, Math.abs(this.value)) : ''"/>
                            <div class="flex rounded-box overflow-hidden border-2 border-base-200">
                                <button type="button" id="btn_ore" data-unit="ore"
                                        class="unit-btn px-4 py-2 text-sm font-bold bg-primary text-primary-content transition-all">
                                    Ore
                                </button>
                                <button type="button" id="btn_giorni" data-unit="giorni"
                                        class="unit-btn px-4 py-2 text-sm font-bold bg-base-200 text-base-content transition-all">
                                    Giorni
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-base-content/40" id="water_preview">Verrà annaffiata ogni — ore</p>
                        @error('watering_cycle')
                        <p class="text-error text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Note (opzionale) --}}
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-5 gap-3">
                        <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Note <span
                                class="normal-case font-normal text-base-content/40">(opzionale)</span></p>
                        <textarea id="notes" name="notes" class="textarea w-full h-24"
                                  placeholder="es. Pianta comprata il 3 marzo. Va bene in penombra...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Errori globali --}}
                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="button" id="btn_confirm" class="btn btn-primary w-full btn-lg gap-2 shadow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                    Aggiungi pianta
                </button>

            </div>
        </div>

    </form>

    @vite(['resources/js/pages/plants_create.js'])

@endsection
