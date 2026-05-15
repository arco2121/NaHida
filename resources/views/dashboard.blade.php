@php
    use Carbon\Carbon;

    $now = Carbon::now();

    if($now->hour >= 20) $message = "Buonasera";
    else $message = "Buongiorno";
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
                <h3 class="text-base font-bold text-base-content mb-2 flex items-center gap-2"><img src="assets/NaHida_Icon_Warning.png" class="w-5 h-5 object-contain" alt=""> Attenzione richiesta</h3>
                <div class="flex flex-col gap-2">

                    <div class="alert bg-error/15 border border-error/30 py-3 px-4 justify-between flex">
                        <img src="assets/NaHida_Emoji_Sad.png" class="w-8 h-8 rounded-full object-cover" alt="triste" onerror="this.style.display='none'">
                        <div class="flex-1">
                            <span class="font-bold text-error">Monstera Deliciosa</span>
                            <div class="flex gap-1 flex-wrap mt-1">
                                <span class="badge badge-error badge-sm">Temp. alta: 32°C</span>
                                <span class="badge badge-warning badge-sm">Umidità bassa: 28%</span>
                            </div>
                        </div>
                        <a href="plant_detail.html" class="btn btn-sm btn-ghost text-error flex items-center gap-1">Vedi <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/></svg></a>
                    </div>

                    <div class="alert bg-warning/15 border border-warning/30 py-3 px-4 justify-between flex">
                        <img src="assets/NaHida_Emoji_Mid.png" class="w-8 h-8 rounded-full object-cover" alt="così così" onerror="this.style.display='none'">
                        <div class="flex-1">
                            <span class="font-bold text-warning">Ficus Benjamina</span>
                            <div class="flex gap-1 flex-wrap mt-1">
                                <span class="badge badge-warning badge-sm">Suolo secco: 18%</span>
                            </div>
                        </div>
                        <a href="plant_detail.html" class="btn btn-sm btn-ghost text-warning flex items-center gap-1">Vedi <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/></svg></a>
                    </div>

                </div>
            </div>

            <div class="flex-1">
                <h3 class="text-base font-bold text-base-content mb-2 flex items-center gap-2"><img src="assets/NaHida_Icon_Water.png" class="w-5 h-5 object-contain" alt=""> Prossime annaffiate</h3>
                <div class="flex flex-row flex-wrap gap-3 overflow-x-auto pb-1 snap-x">

                    <div class="card bg-base-100 shadow min-w-[160px] snap-start flex-shrink-0">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="assets/NaHida_Plant.png" class="w-13 h-13 object-contain" alt="" onerror="this.style.display='none'">
                            <p class="font-bold text-sm leading-tight">Monstera<br>Deliciosa</p>
                            <span class="badge badge-error badge-sm">tra 1 ora</span>
                            <a href="plant_detail.html" class="btn btn-xs btn-outline btn-primary w-full">Dettagli</a>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow min-w-[160px] snap-start flex-shrink-0">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="assets/NaHida_Plant.png" class="w-13 h-13 object-contain" alt="" onerror="this.style.display='none'">
                            <p class="font-bold text-sm leading-tight">Ficus<br>Benjamina</p>
                            <span class="badge badge-warning badge-sm">tra 3 ore</span>
                            <a href="plant_detail.html" class="btn btn-xs btn-outline btn-primary w-full">Dettagli</a>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow min-w-[160px] snap-start flex-shrink-0">
                        <div class="card-body p-4 items-center text-center gap-2">
                            <img src="assets/NaHida_Plant.png" class="w-13 h-13 object-contain" alt="" onerror="this.style.display='none'">
                            <p class="font-bold text-sm leading-tight">Pothos<br>Golden</p>
                            <span class="badge badge-success badge-sm">domani</span>
                            <a href="plant_detail.html" class="btn btn-xs btn-outline btn-primary w-full">Dettagli</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- LE TUE PIANTE -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-bold text-base-content flex items-center gap-2"><img src="assets/NaHida_Icon_Plant.png" class="w-5 h-5 object-contain" alt=""> Le tue piante</h3>
                <a href="plants.html" class="text-sm text-primary font-bold flex items-center gap-1">Vedi tutte <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/></svg></a>
            </div>
            <div class="flex flex-col gap-3">

                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="assets/NaHida_Plant.png" class="w-14 h-14 object-contain flex-shrink-0" alt="Monstera" onerror="this.style.display='none'">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <img src="assets/NaHida_Emoji_Sad.png" class="w-5 h-5 object-contain" alt="" onerror="this.style.display='none'">
                                <span class="font-bold text-sm truncate">Monstera Deliciosa</span>
                            </div>
                            <div class="flex gap-1 flex-wrap">
                                <span class="badge badge-error badge-sm">32°C</span>
                                <span class="badge badge-warning badge-sm">28%</span>
                                <span class="badge badge-neutral badge-sm">55%</span>
                            </div>
                        </div>
                        <a href="plant_detail.html" class="btn btn-sm btn-ghost flex-shrink-0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/></svg></a>
                    </div>
                </div>

                <!-- Pianta 2 — problema lieve -->
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="assets/NaHida_Plant.png" class="w-14 h-14 object-contain flex-shrink-0" alt="Ficus" onerror="this.style.display='none'">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <img src="assets/NaHida_Emoji_Mid.png" class="w-5 h-5 object-contain" alt="" onerror="this.style.display='none'">
                                <span class="font-bold text-sm truncate">Ficus Benjamina</span>
                            </div>
                            <div class="flex gap-1 flex-wrap">
                                <span class="badge badge-success badge-sm">23°C</span>
                                <span class="badge badge-success badge-sm">60%</span>
                                <span class="badge badge-warning badge-sm">18%</span>
                            </div>
                        </div>
                        <a href="plant_detail.html" class="btn btn-sm btn-ghost flex-shrink-0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/></svg></a>
                    </div>
                </div>

                <!-- Pianta 3 — tutto ok -->
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4 flex-row items-center gap-4">
                        <img src="assets/NaHida_Plant.png" class="w-14 h-14 object-contain flex-shrink-0" alt="Pothos" onerror="this.style.display='none'">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <img src="assets/NaHida_Emoji_Happy.png" class="w-5 h-5 object-contain" alt="" onerror="this.style.display='none'">
                                <span class="font-bold text-sm truncate">Pothos Golden</span>
                            </div>
                            <div class="flex gap-1 flex-wrap">
                                <span class="badge badge-success badge-sm">22°C</span>
                                <span class="badge badge-success badge-sm">65%</span>
                                <span class="badge badge-success badge-sm">48%</span>
                            </div>
                        </div>
                        <a href="plant_detail.html" class="btn btn-sm btn-ghost flex-shrink-0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/></svg></a>
                    </div>
                </div>

            </div>
        </div>

        <!-- ATTIVITÀ RECENTE -->
        <div>
            <h3 class="text-base font-bold text-base-content mb-2 flex items-center gap-2"><img src="assets/NaHida_Icon_Clock.png" class="w-5 h-5 object-contain" alt=""> Attività recente</h3>
            <div class="card bg-base-100 shadow">
                <ul class="divide-y divide-base-200">

                    <li class="flex items-center gap-3 px-4 py-3">
                        <img src="assets/NaHida_Icon_Warning.png" class="w-6 h-6 object-contain flex-shrink-0" alt="">
                        <div class="flex-1 text-sm">
                            <span class="font-bold">Monstera Deliciosa</span> ha superato la temperatura massima
                        </div>
                        <span class="text-xs text-base-content/50 flex-shrink-0">2h fa</span>
                    </li>

                    <li class="flex items-center gap-3 px-4 py-3">
                        <img src="assets/NaHida_Icon_Water.png" class="w-6 h-6 object-contain flex-shrink-0" alt="">
                        <div class="flex-1 text-sm">
                            Hai annaffiato <span class="font-bold">Ficus Benjamina</span>
                        </div>
                        <span class="text-xs text-base-content/50 flex-shrink-0">ieri</span>
                    </li>

                    <li class="flex items-center gap-3 px-4 py-3">
                        <img src="assets/NaHida_Icon_Water.png" class="w-6 h-6 object-contain flex-shrink-0" alt="">
                        <div class="flex-1 text-sm">
                            Hai annaffiato <span class="font-bold">Pothos Golden</span>
                        </div>
                        <span class="text-xs text-base-content/50 flex-shrink-0">2 giorni fa</span>
                    </li>

                    <li class="flex items-center gap-3 px-4 py-3">
                        <img src="assets/NaHida_Icon_Plant.png" class="w-6 h-6 object-contain flex-shrink-0" alt="">
                        <div class="flex-1 text-sm">
                            Hai aggiunto <span class="font-bold">Monstera Deliciosa</span>
                        </div>
                        <span class="text-xs text-base-content/50 flex-shrink-0">5 giorni fa</span>
                    </li>

                </ul>
            </div>
        </div>

    </div>
@endsection
