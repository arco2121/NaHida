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
        }

        $healthEmoji = match(true) {
            $errorCount === 0 => 'NaHida_Emoji_Happy.png',
            $errorCount === 1 => 'NaHida_Emoji_Mid.png',
            default => 'NaHida_Emoji_Sad.png',
        };
        $healthLabel = match(true) {
            $errorCount === 0 => 'Condizioni ottimali',
            $errorCount === 1 => 'Attenzione richiesta',
            default => 'Condizioni pessime',
        };
        $healthColor = match(true) {
            $errorCount === 0 => 'success',
            $errorCount === 1 => 'warning',
            default => 'error',
        };

        $lastWatering = $plant->wateringEvents()->latest('watered_at')->first();
        $baseDate = $lastWatering ? $lastWatering->watered_at : $plant->created_at;
        $nextWatering = Carbon::parse($baseDate)->addHours($plant->watering_cycle);
        $isOverdue = $nextWatering->isPast();

        $isOnline = $device
            && $device->last_seen_at
            && $device->last_seen_at->diffInSeconds(now()) < 60;
    @endphp

    {{-- ✅ Passa al JS l'aspetto salvato e i dati della pianta --}}
    <script>
        window.PLANT_ID = {{ $plant->plant_id }};

        window.PLANT_APPEARANCE = {
            pot_color:     {{ (int)($plant->pot_color     ?? 0) }},
            plant_variant: {{ (int)($plant->plant_variant ?? 0) }},
            plant_color:   {{ (int)($plant->plant_color   ?? 0) }},
            flower_color:  {{ (int)($plant->flower_color  ?? 0) }},
        };

        window.PLANT_DATA = {
            notes:          {!! json_encode($plant->notes) !!},
            temp_min:       {{ $plant->temp_min }},
            temp_max:       {{ $plant->temp_max }},
            hum_min:        {{ $plant->hum_min }},
            hum_max:        {{ $plant->hum_max }},
            soil_hum_min:   {{ $plant->soil_hum_min }},
            soil_hum_max:   {{ $plant->soil_hum_max }},
            watering_cycle: {{ $plant->watering_cycle }},
            device_token:   {!! json_encode($device?->device_token) !!},
            has_device:     {{ $device ? 'true' : 'false' }},
        };
    </script>

    {{-- Flash messages --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.showToast && showToast({!! json_encode(session('success')) !!}, 'success');
            });
        </script>
    @endif

    <div class="lg:px-6 lg:pt-6 lg:grid lg:grid-cols-[1fr_1.1fr] lg:gap-6 lg:items-start pb-24">

        <div class="lg:sticky lg:top-20 flex flex-col gap-4">

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

                <div class="relative flex justify-center pb-5">
                    <div class="flex items-center gap-2 bg-{{ $healthColor }}/12 border border-{{ $healthColor }}/25 rounded-full px-4 py-1.5">
                        <img src="{{ asset('assets/' . $healthEmoji) }}" class="w-5 h-5 object-contain" alt="" onerror="this.style.display='none'">
                        <span class="text-sm font-bold text-{{ $healthColor }}">{{ $healthLabel }}</span>
                    </div>
                </div>

            </div>

            <div class="hidden lg:block">
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2 px-1">Prossima annaffiata</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="{{ asset('assets/NaHida_Icon_Clock.png') }}" class="w-12 h-12 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                        <div class="flex-1">
                            @if($isOverdue)
                                <p class="font-bold text-error">In ritardo!</p>
                                <p class="text-sm text-base-content/60">Avrebbe dovuto essere annaffiata {{ $nextWatering->locale('it')->diffForHumans() }}</p>
                            @else
                                <p class="font-bold text-base-content">{{ ucfirst($nextWatering->locale('it')->isoFormat('dddd D MMMM')) }}</p>
                                <p class="text-sm text-base-content/60">{{ $nextWatering->locale('it')->diffForHumans() }}</p>
                            @endif
                        </div>
                        <button class="btn btn-primary btn-sm flex-shrink-0"
                                onclick="document.getElementById('modal_watered').showModal()">
                            Annaffiato!
                        </button>
                    </div>
                </div>
            </div>

            @if($device)
                <div class="hidden lg:block">
                    <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2 px-1">Dispositivo collegato</p>
                    <div class="card bg-base-100 shadow">
                        <div class="card-body p-4 flex-row items-center gap-3">
                            <span class="w-3 h-3 rounded-full flex-shrink-0 {{ $isOnline ? 'bg-success' : 'bg-base-300' }}"></span>
                            <div class="flex-1">
                                <p class="font-bold text-sm text-base-content">{{ $device->device_token }}</p>
                                <p class="text-xs text-base-content/50">
                                    {{ $isOnline ? 'Online' : 'Offline' }}
                                    @if($device->last_seen_at)
                                        · visto {{ $device->last_seen_at->locale('it')->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="flex flex-col gap-5 px-4 pt-5 lg:px-0 lg:pt-0">

            <div>
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Stato sensori</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 gap-3">
                        @if($latest)
                            <p class="text-xs text-base-content/40 -mb-1">
                                Aggiornato {{ Carbon::parse($latest->recorded_at)->locale('it')->diffForHumans() }}
                            </p>
                            <div class="grid grid-cols-2 gap-3">

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Temp.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Temperatura</span>
                                    </div>
                                    <p class="text-2xl font-bold text-{{ $tempColor }}">{{ round($latest->temperature, 1) }}°C</p>
                                    <p class="text-xs text-base-content/40">Ottimale: {{ $plant->temp_min }} — {{ $plant->temp_max }}°C</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Humidity.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Umidità aria</span>
                                    </div>
                                    <p class="text-2xl font-bold text-{{ $humColor }}">{{ round($latest->humidity) }}%</p>
                                    <p class="text-xs text-base-content/40">Ottimale: {{ $plant->hum_min }} — {{ $plant->hum_max }}%</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Soil.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Umidità suolo</span>
                                    </div>
                                    <p class="text-2xl font-bold text-{{ $soilColor }}">{{ round($latest->soil_humidity) }}%</p>
                                    <p class="text-xs text-base-content/40">Ottimale: {{ $plant->soil_hum_min }} — {{ $plant->soil_hum_max }}%</p>
                                </div>

                                <div class="bg-base-200 rounded-box p-3 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ asset('assets/NaHida_Icon_Light.png') }}" class="w-4 h-4 object-contain" alt="" onerror="this.style.display='none'">
                                        <span class="text-xs text-base-content/50 font-bold uppercase tracking-wide">Luminosità</span>
                                    </div>
                                    @if($latest->luminosity !== null)
                                        <p class="text-2xl font-bold text-{{ $lumColor }}">{{ round($latest->luminosity) }} lx</p>
                                        @if($plant->lum_preference)
                                            <p class="text-xs text-base-content/40">Preferenza: {{ $plant->lum_preference }}</p>
                                        @endif
                                    @else
                                        <p class="text-2xl font-bold text-base-content/30">—</p>
                                        <p class="text-xs text-base-content/40">Nessun dato</p>
                                    @endif
                                </div>

                            </div>
                        @else
                            <div class="py-6 text-center text-sm text-base-content/50">
                                Nessuna lettura sensori disponibile.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:hidden">
                <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Prossima annaffiata</p>
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="{{ asset('assets/NaHida_Icon_Clock.png') }}" class="w-12 h-12 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                        <div class="flex-1">
                            @if($isOverdue)
                                <p class="font-bold text-error">In ritardo!</p>
                                <p class="text-sm text-base-content/60">{{ $nextWatering->locale('it')->diffForHumans() }}</p>
                            @else
                                <p class="font-bold text-base-content">{{ ucfirst($nextWatering->locale('it')->isoFormat('dddd D MMMM')) }}</p>
                                <p class="text-sm text-base-content/60">{{ $nextWatering->locale('it')->diffForHumans() }}</p>
                            @endif
                        </div>
                        <button class="btn btn-primary btn-sm flex-shrink-0"
                                onclick="document.getElementById('modal_watered').showModal()">
                            Annaffiato!
                        </button>
                    </div>
                </div>
            </div>

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
                                    <span class="text-xs {{ $isOnline ? 'text-success' : 'text-base-content/40' }}">
                                        {{ $isOnline ? 'Online' : 'Offline' }}
                                    </span>
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
                            <img src="{{ asset('assets/NaHida_Icon_Settings.png') }}" class="w-9 h-9 object-contain" alt="" onerror="this.style.display='none'">
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

            @if($plant->sensorReadings->count() > 1)
                <div>
                    <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Ultime letture</p>
                    <div class="card bg-base-100 shadow">
                        <ul class="divide-y divide-base-200">
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

        </div>
    </div>

    @vite(['resources/js/pages/plants_show.js'])
@endsection
