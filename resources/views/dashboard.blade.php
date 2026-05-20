@php
    use Carbon\Carbon;

    $now = Carbon::now();

    if($now->hour >= 20) $message = ["Buonasera", 'Buonanotte', "Notte"][random_int(0, 2)];
    else $message = ["Buongiorno", "Giorno", "Buonagiornata", "Ciao"][random_int(0, 3)];

@endphp

@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="mx-auto pb-20 w-11/12 px-4 pt-6 flex flex-col gap-6">

        <div class="card bg-primary text-primary-content shadow">
            <div class="card-body py-5 px-5">
                <h2 class="text-2xl font-bold">{{ $message  }}, {{ $params['user']->name }}!</h2>
                <p class="opacity-80 text-sm">{{ucfirst($now->dayName)}}, {{$now->day}}/{{$now->month}}/{{$now->year}} - hai 3 piante da monitorare</p>
            </div>
        </div>

        <div class="flex flex-row flex-wrap gap-10">
            <div class="flex-1">
                <h3 class="text-base font-bold text-base-content mb-2 flex items-center gap-2">
                    <img src="{{ asset('assets/NaHida_Icon_Warning.png') }}" class="w-5 h-5 object-contain" alt=""> Attenzione richiesta
                </h3>
                <div class="flex flex-col gap-2">
                    @forelse($params['attentionPlants'] as $plant)
                        @php
                            $latestReading = $plant->sensorReadings->first();
                            $issues = [];

                            if ($latestReading) {
                                // Controllo Temperatura
                                if ($latestReading->temperature > $plant->temp_max) {
                                    $issues[] = ['type' => 'error', 'text' => 'Temp. alta: ' . $latestReading->temperature . '°C'];
                                } elseif ($latestReading->temperature < $plant->temp_min) {
                                    $issues[] = ['type' => 'error', 'text' => 'Temp. bassa: ' . $latestReading->temperature . '°C'];
                                }

                                // Controllo Umidità
                                if ($latestReading->humidity > $plant->hum_max) {
                                    $issues[] = ['type' => 'warning', 'text' => 'Umidità alta: ' . $latestReading->humidity . '%'];
                                } elseif ($latestReading->humidity < $plant->hum_min) {
                                    $issues[] = ['type' => 'warning', 'text' => 'Umidità bassa: ' . $latestReading->humidity . '%'];
                                }

                                // Controllo Suolo
                                if ($latestReading->soil_humidity > $plant->soil_hum_max) {
                                    $issues[] = ['type' => 'warning', 'text' => 'Suolo umido: ' . $latestReading->soil_humidity . '%'];
                                } elseif ($latestReading->soil_humidity < $plant->soil_hum_min) {
                                    $issues[] = ['type' => 'warning', 'text' => 'Suolo secco: ' . $latestReading->soil_humidity . '%'];
                                }
                            }

                            // Determina la gravità (se c'è più di un problema, o c'è un errore termico, diventa rosso/error)
                            $severity = count($issues) > 1 || collect($issues)->contains('type', 'error') ? 'error' : 'warning';
                            $emoji = $severity === 'error' ? 'NaHida_Emoji_Sad.png' : 'NaHida_Emoji_Mid.png';
                        @endphp

                        <div class="alert bg-{{ $severity }}/15 border border-{{ $severity }}/30 py-3 px-4 justify-between flex">
                            <img src="{{ asset('assets/' . $emoji) }}" class="w-8 h-8 rounded-full object-cover" alt="{{ $severity }}" onerror="this.style.display='none'">

                            <div class="flex-1 mx-3">
                                <span class="font-bold text-{{ $severity }}">{{ $plant->plant_name }}</span>
                                <div class="flex gap-1 flex-wrap mt-1">
                                    @foreach($issues as $issue)
                                        <span class="badge badge-{{ $issue['type'] }} badge-sm">{{ $issue['text'] }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <a href="{{ url('/plants/' . $plant->plant_id) }}" class="btn btn-sm btn-ghost text-{{ $severity }} flex items-center gap-1">Vedi <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/></svg></a>
                        </div>
                    @empty
                        <div class="alert bg-success/15 border border-success/30 py-4 px-4 flex justify-center text-center">
                            <span class="text-success font-bold text-sm">Nessuna pianta richiede la tua attenzione al momento!</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex-1">
                <h3 class="text-base font-bold text-base-content mb-2 flex items-center gap-2">
                    <img src="{{ asset('assets/NaHida_Icon_Water.png') }}" class="w-5 h-5 object-contain" alt=""> Prossime annaffiate
                </h3>
                <div class="flex flex-row flex-wrap gap-3 overflow-x-auto pb-1 snap-x">
                    @forelse($params['nextWaterings'] as $plant)
                        @php
                            $baseDate = $plant->watered_at ?? $plant->created_at;
                            $nextWateringDate = Carbon::parse($baseDate)->addHours($plant->watering_cycle);
                            $now = now();

                            if ($nextWateringDate->isPast()) {
                                $badgeClass = 'badge-error';
                                $badgeText = 'Ritardo';
                            } elseif ($nextWateringDate->diffInHours($now) <= 3) {
                                $badgeClass = 'badge-error';
                                $badgeText = $nextWateringDate->locale('it')->diffForHumans(null, true);
                            } elseif ($nextWateringDate->diffInHours($now) <= 12) {
                                $badgeClass = 'badge-warning';
                                $badgeText = $nextWateringDate->locale('it')->diffForHumans(null, true);
                            } else {
                                $badgeClass = 'badge-success';
                                $badgeText = $nextWateringDate->locale('it')->diffForHumans(null, true);
                            }
                        @endphp

                        <div class="card bg-base-100 shadow min-w-[160px] snap-start flex-shrink-0">
                            <div class="card-body p-4 items-center text-center gap-2">
                                <img src="{{ asset('assets/NaHida_Plant.png') }}" class="w-13 h-13 object-contain" alt="" onerror="this.style.display='none'">
                                <p class="font-bold text-sm leading-tight">
                                    {!! nl2br(e($plant->plant_name)) !!}
                                </p>
                                <span class="badge {{ $badgeClass }} badge-sm">{{ $badgeText }}</span>
                                <a href="{{ url('/plants/' . $plant->plant_id) }}" class="btn btn-xs btn-outline btn-primary w-full">Dettagli</a>
                            </div>
                        </div>
                    @empty
                        <div class="card bg-base-100 shadow w-full">
                            <div class="card-body p-6 text-center text-sm text-base-content/60">
                                Nessuna pianta registrata nel sistema.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- LE TUE PIANTE -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-bold text-base-content flex items-center gap-2"><img src="assets/NaHida_Icon_Plant.png" class="w-5 h-5 object-contain" alt=""> Le tue piante</h3>
                <a href="plants.html" class="text-sm text-primary font-bold flex items-center gap-1">Vedi tutte <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/></svg></a>
            </div>
            @if($params["yourPlants"]->isNotEmpty())

                <div class="flex flex-col gap-3">
                    @foreach($params['yourPlants'] as $plant)
                        @php
                            $latest = $plant->sensorReadings()->latest('recorded_at')->first();
                            $tempClass = 'badge-success';
                            $humClass = 'badge-success';
                            $soilClass = 'badge-success';
                            $errorCount = 0;

                            if ($latest) {
                                // Controllo Temperatura
                                if ($latest->temperature < $plant->temp_min || $latest->temperature > $plant->temp_max) {
                                    $tempClass = 'badge-error';
                                    $errorCount++;
                                }
                                // Controllo Umidità Aria
                                if ($latest->humidity < $plant->hum_min || $latest->humidity > $plant->hum_max) {
                                    $humClass = 'badge-error';
                                    $errorCount++;
                                }
                                // Controllo Umidità Terreno
                                if ($latest->soil_humidity < $plant->soil_hum_min || $latest->soil_humidity > $plant->soil_hum_max) {
                                    $soilClass = 'badge-error';
                                    $errorCount++;
                                }
                            }

                            if ($errorCount === 0) {
                                $emoji = 'NaHida_Emoji_Happy.png';
                            } elseif ($errorCount === 1) {
                                $emoji = 'NaHida_Emoji_Mid.png';
                            } else {
                                $emoji = 'NaHida_Emoji_Sad.png';
                            }
                        @endphp

                        <div class="card bg-base-100 shadow">
                            <div class="card-body p-4 flex-row items-center gap-4">
                                <img src="{{ asset('assets/NaHida_Plant.png') }}" class="w-14 h-14 object-contain flex-shrink-0" alt="{{ $plant->plant_name }}" onerror="this.style.display='none'">

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <img src="{{ asset('assets/' . $emoji) }}" class="w-5 h-5 object-contain" alt="Stato salute" onerror="this.style.display='none'">
                                        <span class="font-bold text-sm truncate">{{ $plant->plant_name }}</span>
                                    </div>

                                    <div class="flex gap-1 flex-wrap">
                                        @if($latest)
                                            <span class="badge {{ $tempClass }} badge-sm">{{ round($latest->temperature) }}°C</span>
                                            <span class="badge {{ $humClass }} badge-sm">{{ round($latest->humidity) }}%</span>
                                            <span class="badge {{ $soilClass }} badge-sm">{{ round($latest->soil_humidity) }}%</span>
                                        @else
                                            <span class="badge badge-ghost badge-sm">Nessun dato</span>
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('plants.show', $plant->plant_id) }}" class="btn btn-sm btn-ghost flex-shrink-0">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <h1>Nessuna pianta da mostrare</h1>
            @endif
        </div>

        <!-- ATTIVITÀ RECENTE -->
        <div>
            <h3 class="text-base font-bold text-base-content mb-2 flex items-center gap-2">
                <img src="{{ asset('assets/NaHida_Icon_Clock.png') }}" class="w-5 h-5 object-contain" alt=""> Attività recente
            </h3>

            <div class="card bg-base-100 shadow">
                <ul class="divide-y divide-base-200">
                    @forelse($params['recentActivity'] as $activity)
                        @php
                            $icon = match($activity['type']) {
                                'warning' => 'NaHida_Icon_Warning.png',
                                'watering' => 'NaHida_Icon_Water.png',
                                'creation' => 'NaHida_Icon_Plant.png',
                                default => 'NaHida_Icon_Clock.png'
                            };
                        @endphp

                        <li class="flex items-center gap-3 px-4 py-3">
                            <img src="{{ asset('assets/' . $icon) }}" class="w-6 h-6 object-contain flex-shrink-0" alt="{{ $activity['type'] }}" onerror="this.style.display='none'">

                            <div class="flex-1 text-sm">
                                {{ $activity['message'] }}
                            </div>

                            <span class="text-xs text-base-content/50 flex-shrink-0">
                        {{ Carbon::parse($activity['date'])->locale('it')->diffForHumans(null, false, true) }}
                    </span>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-base-content/60">
                            Nessuna attività recente.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
@endsection
