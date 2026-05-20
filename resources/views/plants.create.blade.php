@extends('layouts.app')
@section('title', $title)
@section('content')

    @php
        $templates = [
            ['id' => 'succulenta', 'emoji' => '🌵', 'name' => 'Succulenta',   'hum_min' => 20, 'hum_max' => 50, 'temp_min' => 15, 'temp_max' => 35, 'soil_hum_min' => 5,  'soil_hum_max' => 25, 'watering_days' => 14],
            ['id' => 'tropicale',  'emoji' => '🌴', 'name' => 'Tropicale',    'hum_min' => 60, 'hum_max' => 90, 'temp_min' => 18, 'temp_max' => 32, 'soil_hum_min' => 40, 'soil_hum_max' => 80, 'watering_days' => 2],
            ['id' => 'erbe',       'emoji' => '🌿', 'name' => 'Erbe arom.',   'hum_min' => 40, 'hum_max' => 70, 'temp_min' => 12, 'temp_max' => 28, 'soil_hum_min' => 30, 'soil_hum_max' => 60, 'watering_days' => 3],
            ['id' => 'orchidea',   'emoji' => '🌸', 'name' => 'Orchidea',     'hum_min' => 50, 'hum_max' => 80, 'temp_min' => 16, 'temp_max' => 30, 'soil_hum_min' => 30, 'soil_hum_max' => 55, 'watering_days' => 7],
            ['id' => 'orto',       'emoji' => '🍅', 'name' => 'Orto',         'hum_min' => 50, 'hum_max' => 75, 'temp_min' => 16, 'temp_max' => 32, 'soil_hum_min' => 50, 'soil_hum_max' => 80, 'watering_days' => 1],
        ];
        $variants = [
            ['id' => '1', 'emoji' => '🌱', 'label' => 'Tipo 1'],
            ['id' => '2', 'emoji' => '🪴', 'label' => 'Tipo 2'],
            ['id' => '3', 'emoji' => '🌿', 'label' => 'Tipo 3'],
            ['id' => '4', 'emoji' => '🌳', 'label' => 'Tipo 4'],
        ];
        $plantSwatches  = ['#4ade80','#22c55e','#86efac','#15803d','#bbf7d0','#166534'];
        $flowerSwatches = ['#f9a8d4','#f472b6','#fbbf24','#fb923c','#a78bfa','#ffffff'];
        $potSwatches    = ['#a78bfa','#c084fc','#f97316','#78716c','#e2e8f0','#1e293b'];
    @endphp

    <div
        class="mx-auto pb-24 w-11/12 px-4 pt-6 flex flex-col gap-5 max-w-2xl"
        x-data="{
        activeTemplate: null,
        variant: '{{ old('plant_variant', '1') }}',
        wateringValue: {{ old('watering_value', 3) }},
        wateringUnit: '{{ old('watering_unit', 'days') }}',
        plantColor:  '{{ old('plant_color',  '#4ade80') }}',
        flowerColor: '{{ old('flower_color', '#f9a8d4') }}',
        potColor:    '{{ old('pot_color',    '#a78bfa') }}',

        get wateringHours() {
            return this.wateringUnit === 'days'
                ? this.wateringValue * 24
                : this.wateringValue;
        },
        get wateringPreview() {
            const h = this.wateringHours;
            const d = Math.floor(h / 24), r = h % 24;
            let s = '= ';
            if (d) s += d + (d === 1 ? ' giorno' : ' giorni');
            if (r) s += (d ? ' e ' : '') + r + (r === 1 ? ' ora' : ' ore');
            return s;
        },

        applyTemplate(t) {
            this.activeTemplate  = t.id;
            this.$refs.hum_min.value      = t.hum_min;
            this.$refs.hum_max.value      = t.hum_max;
            this.$refs.temp_min.value     = t.temp_min;
            this.$refs.temp_max.value     = t.temp_max;
            this.$refs.soil_hum_min.value = t.soil_hum_min;
            this.$refs.soil_hum_max.value = t.soil_hum_max;
            this.wateringValue = t.watering_days;
            this.wateringUnit  = 'days';
        },

        syncColor(field, val) {
            if (/^#[0-9a-fA-F]{6}$/.test(val)) this[field] = val;
        }
    }"
    >

        {{-- HEADER --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('plants.index') }}" class="btn btn-ghost btn-sm btn-circle">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-base-content">Aggiungi pianta</h2>
                <p class="text-sm text-base-content/50">Configura le condizioni e personalizza il modello</p>
            </div>
        </div>

        {{-- ERRORI --}}
        @if($errors->any())
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                </svg>
                <ul class="list-none m-0 p-0 text-sm space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('plants.store') }}" class="flex flex-col gap-5">
            @csrf

            {{-- ① NOME --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body gap-4 p-5">
                    <h3 class="font-bold text-base flex items-center gap-2">
                        <span class="badge badge-primary badge-sm">1</span> Nome della pianta
                    </h3>

                    <div class="flex flex-col gap-1">
                        <label class="label text-sm font-medium" for="plant_name">Nome</label>
                        <input id="plant_name" type="text" name="plant_name"
                               class="input w-full @error('plant_name') input-error @enderror"
                               placeholder="Es. Monstera, Basilico, Cactus…"
                               value="{{ old('plant_name') }}" required>
                        @error('plant_name')
                        <span class="text-error text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="label text-sm font-medium" for="notes">
                            Note <span class="font-normal text-base-content/40">(opzionale)</span>
                        </label>
                        <textarea id="notes" name="notes" class="textarea w-full resize-none" rows="2"
                                  placeholder="Dove si trova, tipo di terreno usato…">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ② CONDIZIONI OTTIMALI --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body gap-4 p-5">
                    <h3 class="font-bold text-base flex items-center gap-2">
                        <span class="badge badge-primary badge-sm">2</span> Condizioni ottimali
                    </h3>

                    {{-- TEMPLATE PRESETS --}}
                    <div>
                        <p class="text-xs text-base-content/50 mb-2 uppercase tracking-wide font-semibold">Scegli un template</p>
                        <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1">
                            @foreach($templates as $t)
                                <button type="button"
                                        class="flex-shrink-0 flex flex-col items-center gap-1 px-4 py-3 rounded-xl border-2 transition-all text-center min-w-[84px]"
                                        :class="activeTemplate === '{{ $t['id'] }}'
                                    ? 'border-primary bg-primary/10 text-primary'
                                    : 'border-base-200 bg-base-200/50 text-base-content/70 hover:border-base-300'"
                                        @click="applyTemplate({{ json_encode($t) }})"
                                >
                                    <span class="text-xl">{{ $t['emoji'] }}</span>
                                    <span class="text-xs font-semibold">{{ $t['name'] }}</span>
                                </button>
                            @endforeach
                            <button type="button"
                                    class="flex-shrink-0 flex flex-col items-center gap-1 px-4 py-3 rounded-xl border-2 transition-all text-center min-w-[84px]"
                                    :class="activeTemplate === 'custom'
                                ? 'border-primary bg-primary/10 text-primary'
                                : 'border-base-200 bg-base-200/50 text-base-content/70 hover:border-base-300'"
                                    @click="activeTemplate = 'custom'"
                            >
                                <span class="text-xl">✏️</span>
                                <span class="text-xs font-semibold">Custom</span>
                            </button>
                        </div>
                    </div>

                    <div class="divider my-0"></div>

                    {{-- UMIDITÀ ARIA --}}
                    <div>
                        <p class="text-sm font-semibold mb-2">💧 Umidità aria <span class="font-normal text-base-content/40">(RH%)</span></p>
                        <div class="flex gap-3">
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="label text-xs text-base-content/50" for="hum_min">Min</label>
                                <div class="join">
                                    <input x-ref="hum_min" id="hum_min" type="number" name="hum_min" min="0" max="100"
                                           class="input join-item w-full @error('hum_min') input-error @enderror"
                                           placeholder="30" value="{{ old('hum_min') }}" required>
                                    <span class="join-item btn btn-disabled no-animation text-xs">%</span>
                                </div>
                            </div>
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="label text-xs text-base-content/50" for="hum_max">Max</label>
                                <div class="join">
                                    <input x-ref="hum_max" id="hum_max" type="number" name="hum_max" min="0" max="100"
                                           class="input join-item w-full @error('hum_max') input-error @enderror"
                                           placeholder="70" value="{{ old('hum_max') }}" required>
                                    <span class="join-item btn btn-disabled no-animation text-xs">%</span>
                                </div>
                            </div>
                        </div>
                        @error('hum_max')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>

                    {{-- TEMPERATURA --}}
                    <div>
                        <p class="text-sm font-semibold mb-2">🌡️ Temperatura</p>
                        <div class="flex gap-3">
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="label text-xs text-base-content/50" for="temp_min">Min</label>
                                <div class="join">
                                    <input x-ref="temp_min" id="temp_min" type="number" name="temp_min" min="-10" max="60"
                                           class="input join-item w-full @error('temp_min') input-error @enderror"
                                           placeholder="15" value="{{ old('temp_min') }}" required>
                                    <span class="join-item btn btn-disabled no-animation text-xs">°C</span>
                                </div>
                            </div>
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="label text-xs text-base-content/50" for="temp_max">Max</label>
                                <div class="join">
                                    <input x-ref="temp_max" id="temp_max" type="number" name="temp_max" min="-10" max="60"
                                           class="input join-item w-full @error('temp_max') input-error @enderror"
                                           placeholder="30" value="{{ old('temp_max') }}" required>
                                    <span class="join-item btn btn-disabled no-animation text-xs">°C</span>
                                </div>
                            </div>
                        </div>
                        @error('temp_max')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>

                    {{-- UMIDITÀ TERRENO --}}
                    <div>
                        <p class="text-sm font-semibold mb-2">🪴 Umidità terreno <span class="font-normal text-base-content/40">(RH%)</span></p>
                        <div class="flex gap-3">
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="label text-xs text-base-content/50" for="soil_hum_min">Min</label>
                                <div class="join">
                                    <input x-ref="soil_hum_min" id="soil_hum_min" type="number" name="soil_hum_min" min="0" max="100"
                                           class="input join-item w-full @error('soil_hum_min') input-error @enderror"
                                           placeholder="20" value="{{ old('soil_hum_min') }}" required>
                                    <span class="join-item btn btn-disabled no-animation text-xs">%</span>
                                </div>
                            </div>
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="label text-xs text-base-content/50" for="soil_hum_max">Max</label>
                                <div class="join">
                                    <input x-ref="soil_hum_max" id="soil_hum_max" type="number" name="soil_hum_max" min="0" max="100"
                                           class="input join-item w-full @error('soil_hum_max') input-error @enderror"
                                           placeholder="60" value="{{ old('soil_hum_max') }}" required>
                                    <span class="join-item btn btn-disabled no-animation text-xs">%</span>
                                </div>
                            </div>
                        </div>
                        @error('soil_hum_max')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>

                    {{-- CICLO ANNAFFIATURA --}}
                    <div>
                        <p class="text-sm font-semibold mb-2">🚿 Ciclo di annaffiatura</p>
                        <div class="flex gap-3 items-end">
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="label text-xs text-base-content/50">Ogni quanto</label>
                                <input type="number" min="1"
                                       class="input w-full @error('watering_cycle') input-error @enderror"
                                       x-model.number="wateringValue" placeholder="3">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="label text-xs text-base-content/50">Unità</label>
                                <select class="select" x-model="wateringUnit">
                                    <option value="hours">Ore</option>
                                    <option value="days">Giorni</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="watering_cycle" :value="wateringHours">
                        <p class="text-xs text-base-content/40 mt-1" x-text="wateringPreview"></p>
                        @error('watering_cycle')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>

                </div>
            </div>

            {{-- ③ PERSONALIZZAZIONE MODELLO --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body gap-4 p-5">
                    <h3 class="font-bold text-base flex items-center gap-2">
                        <span class="badge badge-primary badge-sm">3</span> Personalizzazione modello
                    </h3>

                    {{-- VARIANTE --}}
                    <div>
                        <p class="text-sm font-semibold mb-2">Variante della pianta</p>
                        <div class="flex gap-2 flex-wrap">
                            @foreach($variants as $v)
                                <button type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 text-sm font-semibold transition-all"
                                        :class="variant === '{{ $v['id'] }}'
                                    ? 'border-primary bg-primary/10 text-primary'
                                    : 'border-base-200 bg-base-200/50 text-base-content/70 hover:border-base-300'"
                                        @click="variant = '{{ $v['id'] }}'"
                                >
                                    <span>{{ $v['emoji'] }}</span>
                                    <span>{{ $v['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="plant_variant" :value="variant">
                    </div>

                    <div class="divider my-0"></div>

                    {{-- COLORI --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

                        {{-- Colore pianta --}}
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold">🌿 Colore pianta</label>
                            <div class="flex items-center gap-2">
                                <input type="color"
                                       class="w-10 h-10 rounded-lg cursor-pointer border border-base-300 p-0.5 bg-base-100"
                                       :value="plantColor"
                                       @input="plantColor = $event.target.value">
                                <input type="text"
                                       class="input input-sm flex-1 font-mono"
                                       :value="plantColor"
                                       @input="syncColor('plantColor', $event.target.value)"
                                       maxlength="7" placeholder="#4ade80">
                            </div>
                            <input type="hidden" name="plant_color" :value="plantColor">
                            <div class="flex gap-1 flex-wrap">
                                @foreach($plantSwatches as $c)
                                    <button type="button"
                                            class="w-6 h-6 rounded-md border-2 border-black/10 transition-all hover:scale-110"
                                            style="background:{{ $c }}"
                                            :style="plantColor === '{{ $c }}' ? 'outline: 2px solid oklch(var(--p)); outline-offset: 2px' : ''"
                                            @click="plantColor = '{{ $c }}'">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Colore fiore --}}
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold">🌸 Colore fiore</label>
                            <div class="flex items-center gap-2">
                                <input type="color"
                                       class="w-10 h-10 rounded-lg cursor-pointer border border-base-300 p-0.5 bg-base-100"
                                       :value="flowerColor"
                                       @input="flowerColor = $event.target.value">
                                <input type="text"
                                       class="input input-sm flex-1 font-mono"
                                       :value="flowerColor"
                                       @input="syncColor('flowerColor', $event.target.value)"
                                       maxlength="7" placeholder="#f9a8d4">
                            </div>
                            <input type="hidden" name="flower_color" :value="flowerColor">
                            <div class="flex gap-1 flex-wrap">
                                @foreach($flowerSwatches as $c)
                                    <button type="button"
                                            class="w-6 h-6 rounded-md border-2 border-black/10 transition-all hover:scale-110"
                                            style="background:{{ $c }}"
                                            :style="flowerColor === '{{ $c }}' ? 'outline: 2px solid oklch(var(--p)); outline-offset: 2px' : ''"
                                            @click="flowerColor = '{{ $c }}'">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Colore vaso --}}
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold">🪣 Colore vaso</label>
                            <div class="flex items-center gap-2">
                                <input type="color"
                                       class="w-10 h-10 rounded-lg cursor-pointer border border-base-300 p-0.5 bg-base-100"
                                       :value="potColor"
                                       @input="potColor = $event.target.value">
                                <input type="text"
                                       class="input input-sm flex-1 font-mono"
                                       :value="potColor"
                                       @input="syncColor('potColor', $event.target.value)"
                                       maxlength="7" placeholder="#a78bfa">
                            </div>
                            <input type="hidden" name="pot_color" :value="potColor">
                            <div class="flex gap-1 flex-wrap">
                                @foreach($potSwatches as $c)
                                    <button type="button"
                                            class="w-6 h-6 rounded-md border-2 border-black/10 transition-all hover:scale-110"
                                            style="background:{{ $c }}"
                                            :style="potColor === '{{ $c }}' ? 'outline: 2px solid oklch(var(--p)); outline-offset: 2px' : ''"
                                            @click="potColor = '{{ $c }}'">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- SUBMIT --}}
            <button type="submit" class="btn btn-primary w-full gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Aggiungi pianta
            </button>

        </form>
    </div>

@endsection
