@extends("layouts.app")
@section('title', $title)
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Str;

        $plant  = $params['plant'];
        $latest = $plant->sensorReadings->first();
        $device = $plant->device;

        $errorCount = 0;
        $tempColor  = 'success';
        $humColor   = 'success';
        $soilColor  = 'success';
        $lumColor   = 'success';

        if ($latest) {
            if ($latest->temperature < $plant->temp_min || $latest->temperature > $plant->temp_max) {
                $tempColor = 'error'; $errorCount++;
            }
            if ($latest->humidity < $plant->hum_min || $latest->humidity > $plant->hum_max) {
                $humColor = 'error'; $errorCount++;
            }
            if ($latest->soil_humidity < $plant->soil_hum_min || $latest->soil_humidity > $plant->soil_hum_max) {
                $soilColor = 'error'; $errorCount++;
            }
            if ($latest->luminosity !== null && ($plant->lux_min ?? 0) > 0) {
                if ($latest->luminosity < $plant->lux_min || $latest->luminosity > ($plant->lux_max ?? 100000)) {
                    $lumColor = 'error'; $errorCount++;
                }
            }
        }

        $healthEmoji = match(true) {
            $errorCount === 0 => 'NaHida_Emoji_Happy.png',
            $errorCount === 1 => 'NaHida_Emoji_Mid.png',
            default           => 'NaHida_Emoji_Sad.png',
        };
        $healthLabel = match(true) {
            $errorCount === 0 => 'Condizioni ottimali',
            $errorCount === 1 => 'Attenzione richiesta',
            default           => 'Condizioni pessime',
        };
        $healthColor = match(true) {
            $errorCount === 0 => 'success',
            $errorCount === 1 => 'warning',
            default           => 'error',
        };

        $lastWatering = $plant->wateringEvents()->latest('watered_at')->first();
        $baseDate     = $lastWatering ? $lastWatering->watered_at : $plant->created_at;
        $nextWatering = Carbon::parse($baseDate)->addHours($plant->watering_cycle);
        $isOverdue    = $nextWatering->isPast();

        $isOnline = $device
            && $device->last_seen_at
            && $device->last_seen_at->diffInSeconds(now()) < 90;

        $luxMin = $plant->lux_min ?? 0;
        $luxMax = $plant->lux_max ?? 100000;
        $luxLabel = match(true) {
            $luxMax <= 500   => 'Poca luce',
            $luxMax <= 2000  => 'Media luce',
            default          => 'Tanta luce',
        };
    @endphp

    {{-- Dati per JS --}}
    <script>
        window.PLANT_ID = {{ $plant->plant_id }};

        window.PLANT_APPEARANCE = {
            pot_color:     {{ (int)($plant->pot_color     ?? 0) }},
            plant_variant: {{ (int)($plant->plant_variant ?? 0) }},
            plant_color:   {{ (int)($plant->plant_color   ?? 0) }},
            flower_color:  {{ (int)($plant->flower_color  ?? 0) }},
        };

        window.PLANT_DATA = {
            plant_name:     {!! json_encode($plant->plant_name) !!},
            notes:          {!! json_encode($plant->notes) !!},
            id:             {{ $plant->plant_id }},
            temp_min:       {{ $plant->temp_min }},
            temp_max:       {{ $plant->temp_max }},
            hum_min:        {{ $plant->hum_min }},
            hum_max:        {{ $plant->hum_max }},
            soil_hum_min:   {{ $plant->soil_hum_min }},
            soil_hum_max:   {{ $plant->soil_hum_max }},
            lux_min:        {{ $plant->lux_min ?? 0 }},
            lux_max:        {{ $plant->lux_max ?? 100000 }},
            watering_cycle: {{ $plant->watering_cycle }},
            device_token:   {!! json_encode($device?->device_token) !!},
            has_device:     {{ $device ? 'true' : 'false' }},
        };

        window.PLANT_HEALTH = {
            temperature:   {{ $latest?->temperature   ?? 'null' }},
            humidity:      {{ $latest?->humidity       ?? 'null' }},
            soil_humidity: {{ $latest?->soil_humidity  ?? 'null' }},
            luminosity:    {{ $latest?->luminosity     ?? 'null' }},
        };

        window.PLANT_READINGS = {!! json_encode(
            $plant->sensorReadings
                ->reverse()
                ->values()
                ->map(fn($r) => [
                    'temperature'   => $r->temperature,
                    'humidity'      => $r->humidity,
                    'soil_humidity' => $r->soil_humidity,
                    'luminosity'    => $r->luminosity,
                    'recorded_at'   => $r->recorded_at->format('H:i'),
                ])
        ) !!};
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showToast && showToast({!! json_encode(session('success')) !!}, 'success');
            });
        </script>
    @endif

    <div class="lg:px-6 lg:pt-6 lg:grid lg:grid-cols-[1fr_1.1fr] lg:gap-6 lg:items-start pb-24">

        {{-- ===== COLONNA SINISTRA (Live2D + sidebar info) ===== --}}
        <div class="lg:sticky lg:top-20 flex flex-col gap-4">

            {{-- Card modello Live2D --}}
            <div class="relative overflow-hidden bg-base-100 lg:rounded-2xl lg:shadow-md">
                <div class="absolute inset-0 pointer-events-none"
                     style="background: radial-gradient(ellipse at 50% 90%, color-mix(in srgb, var(--color-primary) 20%, transparent), transparent 65%), radial-gradient(ellipse at 85% 15%, color-mix(in srgb, var(--color-accent) 14%, transparent), transparent 55%);"></div>

                <div class="relative flex items-center justify-between px-5 pt-5 pb-1">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('assets/NaHida_Icon_Heart.png') }}" class="w-5 h-5 object-contain" alt="" onerror="this.style.display='none'">
                        <h1 id="plant_name_display" class="text-lg font-bold text-base-content">{{ $plant->plant_name }}</h1>
                    </div>
                    <button class="btn btn-ghost btn-sm gap-1" onclick="document.getElementById('modal_edit_plant').showModal()">
                        <img src="{{ asset('assets/NaHida_Icon_Edit.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                        <span class="text-xs">Modifica</span>
                    </button>
                </div>

                <div class="relative flex justify-center px-6 pt-2 pb-4">
                    <div class="relative w-full max-w-xs" style="aspect-ratio: 1;">
                        <div id="model-skeleton" class="skeleton w-full h-full rounded-box absolute inset-0 z-20"></div>
                        <canvas id="live2d-canvas" class="w-full h-full pointer-events-auto opacity-0 transition-opacity duration-700 absolute inset-0 z-30"></canvas>
                    </div>
                </div>

                {{--
                    Badge salute — aggiornato live da JS via updateHealthBadge().
                    data-health-emoji e data-health-label sono i target degli aggiornamenti.
                --}}
                <div class="relative flex justify-center pb-5">
                    <div id="health_badge"
                         class="flex items-center gap-2 bg-{{ $healthColor }}/12 border border-{{ $healthColor }}/25 rounded-full px-4 py-1.5
                                transition-colors duration-500">
                        <img data-health-emoji
                             src="{{ asset('assets/' . $healthEmoji) }}"
                             class="w-5 h-5 object-contain"
                             alt=""
                             onerror="this.style.display='none'">
                        <span data-health-label
                              class="text-sm font-bold text-{{ $healthColor }}">{{ $healthLabel }}</span>
                    </div>
                </div>
            </div>

            {{-- Prossima annaffiata (solo desktop) --}}
            <div class="hidden lg:block">
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2 px-1">Prossima annaffiata</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="{{ asset('assets/NaHida_Icon_Clock.png') }}" class="w-12 h-12 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                        <div class="flex-1">
                            @if($isOverdue)
                                <p data-watering-title class="font-bold text-error">In ritardo!</p>
                                <p data-watering-sub class="text-sm text-base-content/60">Avrebbe dovuto essere annaffiata {{ $nextWatering->locale('it')->diffForHumans() }}</p>
                            @else
                                <p data-watering-title class="font-bold text-base-content">{{ ucfirst($nextWatering->locale('it')->isoFormat('dddd D MMMM')) }}</p>
                                <p data-watering-sub class="text-sm text-base-content/60">{{ $nextWatering->locale('it')->diffForHumans() }}</p>
                            @endif
                        </div>
                        <button class="btn btn-primary btn-sm flex-shrink-0"
                                onclick="document.getElementById('modal_watered').showModal()">
                            Annaffiato!
                        </button>
                    </div>
                </div>
            </div>

            {{-- Dispositivo collegato (solo desktop) --}}
            @if($device)
                <div class="hidden lg:block">
                    <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2 px-1">Dispositivo collegato</p>
                    <div class="card bg-base-100 shadow">
                        <div class="card-body p-4 flex-row items-center gap-3">
                            <span data-device-dot
                                  class="w-3 h-3 rounded-full flex-shrink-0 {{ $isOnline ? 'bg-success' : 'bg-base-300' }}"></span>
                            <div class="flex-1">
                                <p class="font-bold text-sm text-base-content">{{ $device->device_token }}</p>
                                <p id="device_sidebar_text" class="text-xs text-base-content/50">
                                    {{ $isOnline ? 'Online' : 'Offline' }}
                                    @if($device->last_seen_at && !$isOnline)
                                        · visto {{ $device->last_seen_at->locale('it')->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ===== COLONNA DESTRA ===== --}}
        <div class="flex flex-col gap-5 px-4 pt-5 lg:px-0 lg:pt-0">

            {{-- Stato sensori --}}
            <div>
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Stato sensori</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 gap-3">

                        @if($latest)
                            {{-- Lettura iniziale disponibile --}}
                            <p id="sensor_updated_at" class="text-xs text-base-content/40 -mb-1">
                                Aggiornato {{ Carbon::parse($latest->recorded_at)->locale('it')->diffForHumans() }}
                            </p>
                            <div id="sensor_grid" class="grid grid-cols-2 gap-3">

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Temp.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Temperatura</span>
                                    </div>
                                    <p id="val_temp" class="text-2xl font-bold text-{{ $tempColor }}">{{ round($latest->temperature, 1) }}°C</p>
                                    <p id="lbl_range_temp" class="text-xs text-base-content/40">Ottimale: {{ $plant->temp_min }} — {{ $plant->temp_max }}°C</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Water.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Umidità aria</span>
                                    </div>
                                    <p id="val_hum" class="text-2xl font-bold text-{{ $humColor }}">{{ round($latest->humidity) }}%</p>
                                    <p id="lbl_range_hum" class="text-xs text-base-content/40">Ottimale: {{ $plant->hum_min }} — {{ $plant->hum_max }}%</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Soil.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Umidità suolo</span>
                                    </div>
                                    <p id="val_soil" class="text-2xl font-bold text-{{ $soilColor }}">{{ round($latest->soil_humidity) }}%</p>
                                    <p id="lbl_range_soil" class="text-xs text-base-content/40">Ottimale: {{ $plant->soil_hum_min }} — {{ $plant->soil_hum_max }}%</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Light.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Luminosità</span>
                                    </div>
                                    @if($latest->luminosity !== null)
                                        <p id="val_lum" class="text-2xl font-bold text-{{ $lumColor }}">{{ round($latest->luminosity) }} lx</p>
                                    @else
                                        <p id="val_lum" class="text-2xl font-bold text-base-content/30">—</p>
                                    @endif
                                    <p class="text-xs text-base-content/40">{{ $luxLabel }}: {{ $luxMin }} — {{ number_format($luxMax, 0, ',', '.') }} lx</p>
                                </div>

                            </div>

                        @else
                            {{--
                                Nessuna lettura iniziale.
                                sensor_no_data: placeholder visibile al caricamento.
                                sensor_grid: griglia nascosta, svelata da JS al primo evento SensorUpdated.
                                sensor_updated_at: nascosto, svelato da JS insieme alla griglia.
                            --}}
                            <div id="sensor_no_data" class="py-6 text-center text-sm text-base-content/50">
                                Nessuna lettura sensori disponibile. In attesa di dati dal dispositivo…
                            </div>

                            <p id="sensor_updated_at" class="hidden text-xs text-base-content/40 -mb-1"></p>

                            <div id="sensor_grid" class="hidden grid grid-cols-2 gap-3">

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Temp.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Temperatura</span>
                                    </div>
                                    <p id="val_temp" class="text-2xl font-bold text-base-content">—</p>
                                    <p id="lbl_range_temp" class="text-xs text-base-content/40">Ottimale: {{ $plant->temp_min }} — {{ $plant->temp_max }}°C</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Water.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Umidità aria</span>
                                    </div>
                                    <p id="val_hum" class="text-2xl font-bold text-base-content">—</p>
                                    <p id="lbl_range_hum" class="text-xs text-base-content/40">Ottimale: {{ $plant->hum_min }} — {{ $plant->hum_max }}%</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Soil.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Umidità suolo</span>
                                    </div>
                                    <p id="val_soil" class="text-2xl font-bold text-base-content">—</p>
                                    <p id="lbl_range_soil" class="text-xs text-base-content/40">Ottimale: {{ $plant->soil_hum_min }} — {{ $plant->soil_hum_max }}%</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Light.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Luminosità</span>
                                    </div>
                                    <p id="val_lum" class="text-2xl font-bold text-base-content">—</p>
                                    <p class="text-xs text-base-content/40">{{ $luxLabel }}: {{ $luxMin }} — {{ number_format($luxMax, 0, ',', '.') }} lx</p>
                                </div>

                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- Prossima annaffiata (mobile) --}}
            <div class="lg:hidden">
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Prossima annaffiata</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="{{ asset('assets/NaHida_Icon_Clock.png') }}" class="w-12 h-12 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                        <div class="flex-1">
                            @if($isOverdue)
                                <p data-watering-title class="font-bold text-error">In ritardo!</p>
                                <p data-watering-sub class="text-sm text-base-content/60">{{ $nextWatering->locale('it')->diffForHumans() }}</p>
                            @else
                                <p data-watering-title class="font-bold text-base-content">{{ ucfirst($nextWatering->locale('it')->isoFormat('dddd D MMMM')) }}</p>
                                <p data-watering-sub class="text-sm text-base-content/60">{{ $nextWatering->locale('it')->diffForHumans() }}</p>
                            @endif
                        </div>
                        <button class="btn btn-primary btn-sm flex-shrink-0"
                                onclick="document.getElementById('modal_watered').showModal()">
                            Annaffiato!
                        </button>
                    </div>
                </div>
            </div>

            {{-- Azioni --}}
            <div>
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Azioni</p>
                <div class="grid grid-cols-2 gap-3">

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer"
                            onclick="document.getElementById('modal_history').showModal()">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="{{ asset('assets/NaHida_Icon_Clock.png') }}" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
                            <span class="text-sm font-bold text-base-content">Storico</span>
                        </div>
                    </button>

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer"
                            onclick="document.getElementById('modal_device').showModal()">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="{{ asset('assets/NaHida_Icon_Device.png') }}" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
                            <div>
                                <span class="text-sm font-bold text-base-content block">Dispositivo</span>
                                @if($device)
                                    <div class="flex items-center justify-center gap-1 mt-0.5">
                                        <span data-device-dot
                                              class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-success' : 'bg-base-300' }} flex-shrink-0"></span>
                                        <span id="device_action_text"
                                              class="text-xs {{ $isOnline ? 'text-success' : 'text-base-content/40' }}">
                                            {{ $isOnline ? 'Online' : 'Offline' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-base-content/40">Non collegato</span>
                                @endif
                            </div>
                        </div>
                    </button>

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer"
                            onclick="document.getElementById('modal_music').showModal()">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="{{ asset('assets/NaHida_Icon_Music.png') }}" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
                            <span class="text-sm font-bold text-base-content">Musica</span>
                        </div>
                    </button>

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer"
                            onclick="document.getElementById('modal_conditions').showModal()">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="{{ asset('assets/NaHida_Icon_Plant.png') }}" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
                            <span class="text-sm font-bold text-base-content">Condizioni</span>
                        </div>
                    </button>

                    <button class="card bg-base-100 shadow hover:shadow-md active:scale-95 transition-all cursor-pointer col-span-2"
                            onclick="document.getElementById('modal_notes').showModal()">
                        <div class="card-body p-4 flex-row items-center gap-3">
                            <img src="{{ asset('assets/NaHida_Icon_Notes.png') }}" class="w-9 h-9 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                            <div class="text-left flex-1">
                                <span class="text-sm font-bold text-base-content block">Note</span>
                                <span id="notes_preview" class="text-xs text-base-content/50">
                                    {{ $plant->notes ? Str::limit($plant->notes, 50) : 'Nessuna nota' }}
                                </span>
                            </div>
                            <svg class="w-4 h-4 text-base-content/30 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                <path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/>
                            </svg>
                        </div>
                    </button>

                </div>
            </div>

            {{-- Ultime letture (storico rapido) --}}
            @if($plant->sensorReadings->count() > 1)
                <div>
                    <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Ultime letture</p>
                    <div class="card bg-base-100 shadow">
                        <ul id="latest_readings_list" class="divide-y divide-base-200">
                            @foreach($plant->sensorReadings->skip(1)->take(6) as $reading)
                                <li class="flex items-center gap-3 px-4 py-3">
                                    <span class="text-xs text-base-content/40 flex-shrink-0 w-20">
                                        {{ Carbon::parse($reading->recorded_at)->locale('it')->diffForHumans(null, false, true) }}
                                    </span>
                                    <div class="flex gap-1 flex-wrap flex-1">
                                        <span class="badge badge-ghost badge-sm">{{ round($reading->temperature, 1) }}°C</span>
                                        <span class="badge badge-ghost badge-sm">{{ round($reading->humidity) }}% aria</span>
                                        <span class="badge badge-ghost badge-sm">{{ round($reading->soil_humidity) }}% suolo</span>
                                        @if($reading->luminosity !== null)
                                            <span class="badge badge-ghost badge-sm">{{ round($reading->luminosity) }} lx</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Grafici sensori --}}
            @if($plant->sensorReadings->count() > 1)
                <div>
                    <div class="card bg-base-100 shadow">
                        <div class="card-body p-4 gap-3">

                            <div role="tablist" class="tabs tabs-box tabs-sm">
                                <button role="tab" class="tab tab-active" data-chart="temp">🌡 Temp.</button>
                                <button role="tab" class="tab"            data-chart="hum">💧 Umidità</button>
                                <button role="tab" class="tab"            data-chart="soil">🌱 Suolo</button>
                                <button role="tab" class="tab"            data-chart="lum">☀️ Luce</button>
                            </div>

                            <div class="relative min-h-[200px]">
                                <canvas id="sensor-chart"></canvas>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @vite(['resources/js/pages/plants_show.js'])
@endsection
