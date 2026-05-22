@extends('layouts.app')
@section('title', $title)
@section('content')

    <div class="px-4 pt-6 flex flex-col gap-4 lg:grid lg:grid-cols-[1fr_1.2fr] lg:items-start lg:gap-6">

        <!-- ===== COLONNA SINISTRA: preview modello + personalizzazione ===== -->
        <div class="flex flex-col gap-4 lg:sticky lg:top-20">

            <!-- Preview modello -->
            <div class="relative overflow-hidden bg-base-100 rounded-2xl shadow">
                <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse at 50% 90%, color-mix(in srgb, var(--color-primary) 20%, transparent), transparent 65%), radial-gradient(ellipse at 85% 15%, color-mix(in srgb, var(--color-accent) 14%, transparent), transparent 55%);"></div>
                <p class="relative text-xs font-bold text-base-content/50 uppercase tracking-wider px-5 pt-5 pb-1">Anteprima</p>
                <div class="relative flex justify-center px-6 pt-2 pb-4">
                    <div class="relative w-full max-w-xs" style="aspect-ratio:1;">
                        <div id="model-skeleton" class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>
                        <canvas id="live2d-canvas" class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>
                    </div>
                </div>
            </div>

            <!-- Personalizzazione modello -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-5 gap-5">
                    <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider -mb-2">Personalizzazione</p>

                    <!-- Variante pianta: 0-6 -->
                    <div>
                        <div class="flex justify-between items-baseline mb-1">
                            <label class="text-sm font-bold">Variante pianta</label>
                            <span id="lbl_variant" class="text-sm font-bold text-primary">0</span>
                        </div>
                        <input type="range" id="range_variant" min="0" max="6" value="0" step="1"
                               class="range range-primary range-sm"
                               oninput="document.getElementById('lbl_variant').textContent=this.value; updateModel()"/>
                    </div>

                    <!-- Colore pianta: 0-5 -->
                    <div>
                        <div class="flex justify-between items-baseline mb-1">
                            <label class="text-sm font-bold">Colore pianta</label>
                            <span id="lbl_plant_color" class="text-sm font-bold text-accent">0</span>
                        </div>
                        <input type="range" id="range_plant_color" min="0" max="5" value="0" step="1"
                               class="range range-accent range-sm"
                               oninput="document.getElementById('lbl_plant_color').textContent=this.value; updateModel()"/>
                    </div>

                    <!-- Colore fiore: 0-6 -->
                    <div>
                        <div class="flex justify-between items-baseline mb-1">
                            <label class="text-sm font-bold">Colore fiore</label>
                            <span id="lbl_flower" class="text-sm font-bold text-secondary">0</span>
                        </div>
                        <input type="range" id="range_flower" min="0" max="6" value="0" step="1"
                               class="range range-secondary range-sm"
                               oninput="document.getElementById('lbl_flower').textContent=this.value; updateModel()"/>
                    </div>

                    <!-- Colore vaso: 0-2 -->
                    <div>
                        <div class="flex justify-between items-baseline mb-1">
                            <label class="text-sm font-bold">Colore vaso</label>
                            <span id="lbl_pot" class="text-sm font-bold">0</span>
                        </div>
                        <input type="range" id="range_pot" min="0" max="2" value="0" step="1"
                               class="range range-sm"
                               oninput="document.getElementById('lbl_pot').textContent=this.value; updateModel()"/>
                    </div>

                </div>
            </div>

        </div>

        <!-- ===== COLONNA DESTRA: form ===== -->
        <div class="flex flex-col gap-4">

            <!-- Nome pianta -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-5 gap-3">
                    <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Nome pianta</p>
                    <input id="plant_name" type="text" class="input w-full" placeholder="es. Monstera Deliciosa" />
                </div>
            </div>

            <!-- Condizioni ottimali -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-5 gap-4">
                    <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Condizioni ottimali</p>

                    <!-- Template selezionati -->
                    <div class="grid grid-cols-2 gap-2" id="template_grid">

                        <button onclick="selectTemplate(this, 'tropicale')"
                                class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                            <span class="text-2xl">🌴</span>
                            <div>
                                <p class="text-sm font-bold leading-tight">Tropicale</p>
                                <p class="text-xs text-base-content/50">Caldo e umido</p>
                            </div>
                        </button>

                        <button onclick="selectTemplate(this, 'mediterraneo')"
                                class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                            <span class="text-2xl">🌿</span>
                            <div>
                                <p class="text-sm font-bold leading-tight">Mediterraneo</p>
                                <p class="text-xs text-base-content/50">Temperato e secco</p>
                            </div>
                        </button>

                        <button onclick="selectTemplate(this, 'succulente')"
                                class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                            <span class="text-2xl">🌵</span>
                            <div>
                                <p class="text-sm font-bold leading-tight">Succulente</p>
                                <p class="text-xs text-base-content/50">Arido e soleggiato</p>
                            </div>
                        </button>

                        <button onclick="selectTemplate(this, 'custom')"
                                class="template-btn flex items-center gap-3 p-3 rounded-box border-2 border-base-200 bg-base-200 hover:border-primary/40 transition-all text-left">
                            <span class="text-2xl">✏️</span>
                            <div>
                                <p class="text-sm font-bold leading-tight">Custom</p>
                                <p class="text-xs text-base-content/50">Imposta tu i valori</p>
                            </div>
                        </button>

                    </div>

                    <div class="divider my-0 text-xs text-base-content/40">Parametri</div>

                    <!-- Input condizioni -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Umidità aria min (%)</label>
                            <input id="hum_min" type="number" class="input input-sm w-full" placeholder="es. 50" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Umidità aria max (%)</label>
                            <input id="hum_max" type="number" class="input input-sm w-full" placeholder="es. 80" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Temperatura min (°C)</label>
                            <input id="temp_min" type="number" class="input input-sm w-full" placeholder="es. 18" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Temperatura max (°C)</label>
                            <input id="temp_max" type="number" class="input input-sm w-full" placeholder="es. 28" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Suolo min (%)</label>
                            <input id="soil_min" type="number" class="input input-sm w-full" placeholder="es. 40" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide block mb-1">Suolo max (%)</label>
                            <input id="soil_max" type="number" class="input input-sm w-full" placeholder="es. 70" />
                        </div>
                    </div>

                </div>
            </div>

            <!-- Frequenza annaffiatura -->
            <div class="card bg-base-100 shadow">
                <div class="card-body p-5 gap-3">
                    <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Frequenza annaffiatura</p>
                    <div class="flex items-center gap-3">
                        <input id="water_interval" type="number" class="input flex-1" placeholder="es. 3" min="1" />
                        <div class="flex rounded-box overflow-hidden border-2 border-base-200">
                            <button id="btn_ore" onclick="setUnit('ore')"
                                    class="px-4 py-2 text-sm font-bold bg-primary text-primary-content transition-all">
                                Ore
                            </button>
                            <button id="btn_giorni" onclick="setUnit('giorni')"
                                    class="px-4 py-2 text-sm font-bold bg-base-200 text-base-content transition-all">
                                Giorni
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-base-content/40" id="water_preview">Verrà annaffiata ogni — ore</p>
                </div>
            </div>

            <!-- Tasto conferma -->
            <button class="btn btn-primary w-full btn-lg gap-2 shadow"
                    onclick="document.getElementById('modal_confirm').showModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Aggiungi pianta
            </button>

        </div>
    </div>

    @vite(['resources/js/pages/plants_create.js'])

@endsection
