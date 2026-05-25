@extends('layouts.app')
@section('title', $title)
@section('content')

    <div class="mx-auto pb-24 w-11/12 px-4 pt-6 flex flex-col gap-6">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-base-content">{{$title}}</h2>
                <p class="text-sm text-base-content/50 mt-0.5">
                    {{ $params['plants']->count() }} {{ $params['plants']->count() === 1 ? 'pianta registrata' : 'piante registrate' }}
                </p>
            </div>
            <a href="{{ route('plants.create') }}" class="btn btn-primary btn-sm gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Aggiungi
            </a>
        </div>

        @if($params['plants']->isEmpty())
            <div class="flex flex-col items-center justify-center gap-5 py-20 text-center">
                <img src="{{ asset('assets/NaHida_Plant.png') }}"
                     class="w-28 h-28 object-contain opacity-40"
                     alt="Nessuna pianta"
                     onerror="this.style.display='none'">
                <div>
                    <p class="text-lg font-bold text-base-content/60">Nessuna pianta ancora!</p>
                    <p class="text-sm text-base-content/40 mt-1">Aggiungi la tua prima pianta per iniziare a monitorarla.</p>
                </div>
                <a href="{{ route('plants.create') }}" class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Aggiungi pianta
                </a>
            </div>

        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach($params['plants'] as $plant)
                    @php
                        $latest     = $plant->sensorReadings->first();
                        $errorCount = 0;
                        $tempClass  = 'badge-success';
                        $humClass   = 'badge-success';
                        $soilClass  = 'badge-success';

                        if ($latest) {
                            if ($latest->temperature < $plant->temp_min || $latest->temperature > $plant->temp_max) {
                                $tempClass = 'badge-error'; $errorCount++;
                            }
                            if ($latest->humidity < $plant->hum_min || $latest->humidity > $plant->hum_max) {
                                $humClass = 'badge-error'; $errorCount++;
                            }
                            if ($latest->soil_humidity < $plant->soil_hum_min || $latest->soil_humidity > $plant->soil_hum_max) {
                                $soilClass = 'badge-error'; $errorCount++;
                            }
                        }

                        $emoji = match(true) {
                            $errorCount === 0 => 'NaHida_Emoji_Happy.png',
                            $errorCount === 1 => 'NaHida_Emoji_Mid.png',
                            default           => 'NaHida_Emoji_Sad.png',
                        };

                        $borderClass = match(true) {
                            $errorCount >= 2 => 'border-error/40',
                            $errorCount === 1 => 'border-warning/40',
                            default          => 'border-base-200',
                        };
                    @endphp

                    <a href="{{ route('plants.show', $plant->plant_id) }}"
                       class="card bg-base-100 shadow border {{ $borderClass }} hover:shadow-md active:scale-[0.98] transition-all duration-150">
                        <div class="card-body p-4 flex-row items-center gap-4">

                            <div class="relative flex-shrink-0">
                                @php
                                    $previewPath = 'storage/plants/' . $plant->plant_id . '/preview.png';
                                    $previewUrl  = file_exists(public_path($previewPath))
                                        ? asset($previewPath) . '?v=' . filemtime(public_path($previewPath))
                                        : asset('assets/NaHida_Plant.png');
                                @endphp
                                <img src="{{ $previewUrl }}"
                                     class="w-16 h-16 object-contain"
                                     alt="{{ $plant->plant_name }}"
                                     onerror="this.src='{{ asset('assets/NaHida_Plant.png') }}'">
                                <img src="{{ asset('assets/' . $emoji) }}"
                                     class="absolute -bottom-1 -right-1 w-6 h-6 object-contain"
                                     alt="stato"
                                     onerror="this.style.display='none'">
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-base truncate text-base-content">
                                    {{ $plant->plant_name }}
                                </p>

                                @if($plant->notes)
                                    <p class="text-xs text-base-content/50 truncate mt-0.5">{{ $plant->notes }}</p>
                                @endif

                                <div class="flex gap-1 flex-wrap mt-2">
                                    @if($latest)
                                        <span class="badge {{ $tempClass }} badge-sm">{{ round($latest->temperature, 1) }}°C</span>
                                        <span class="badge {{ $humClass }} badge-sm">{{ round($latest->humidity) }}% umidità</span>
                                        <span class="badge {{ $soilClass }} badge-sm">{{ round($latest->soil_humidity) }}% suolo</span>
                                    @else
                                        <span class="badge badge-ghost badge-sm">Nessun dato sensore</span>
                                    @endif
                                </div>

                                @if($plant->device)
                                    @php
                                        $isOnline = $plant->device->last_seen_at
                                            && $plant->device->last_seen_at->diffInSeconds(now()) < 60;
                                    @endphp
                                    <div class="flex items-center gap-1 mt-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-success' : 'bg-base-300' }} flex-shrink-0"></span>
                                        <span class="text-xs text-base-content/40">
                                        {{ $isOnline ? 'Dispositivo online' : 'Dispositivo offline' }}
                                    </span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1 mt-1.5">
                                        <span class="w-2 h-2 rounded-full bg-base-300 flex-shrink-0"></span>
                                        <span class="text-xs text-base-content/40">Nessun dispositivo</span>
                                    </div>
                                @endif
                            </div>

                            <svg class="w-4 h-4 text-base-content/30 flex-shrink-0"
                                 viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z"
                                      fill="currentColor"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </div>

@endsection
