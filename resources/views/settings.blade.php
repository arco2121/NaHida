@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="flex w-full justify-center items-center">
        <div class="w-full lg:w-11/12 px-4 pt-6 flex flex-col gap-1 pb-24">

            {{-- ===== SEZIONE PROFILO ===== --}}
            <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider px-1 mb-2">Profilo</p>
            <div class="card bg-base-100 shadow mb-6">
                <div class="card-body p-0 divide-y divide-base-200">

                    {{-- Nome --}}
                    <div class="flex flex-col gap-1.5 px-4 pt-4 pb-3">
                        <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide" for="setting_first_name">Nome</label>
                        <div class="flex items-center gap-2">
                            <input
                                type="text"
                                id="setting_first_name"
                                class="input input-sm flex-1"
                                value="{{ $params['user']->first_name }}"
                                autocomplete="given-name"
                            />
                            <button
                                id="btn_save_first_name"
                                class="btn btn-sm btn-success btn-square opacity-0 pointer-events-none transition-opacity duration-150"
                                title="Salva nome"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Cognome --}}
                    <div class="flex flex-col gap-1.5 px-4 pt-3 pb-3">
                        <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide" for="setting_last_name">Cognome</label>
                        <div class="flex items-center gap-2">
                            <input
                                type="text"
                                id="setting_last_name"
                                class="input input-sm flex-1"
                                value="{{ $params['user']->last_name }}"
                                autocomplete="family-name"
                            />
                            <button
                                id="btn_save_last_name"
                                class="btn btn-sm btn-success btn-square opacity-0 pointer-events-none transition-opacity duration-150"
                                title="Salva cognome"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="flex flex-col gap-1.5 px-4 pt-3 pb-3">
                        <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide" for="setting_email">Email</label>
                        <div class="flex items-center gap-2">
                            <input
                                type="email"
                                id="setting_email"
                                class="input input-sm flex-1"
                                value="{{ $params['user']->email }}"
                                autocomplete="email"
                            />
                            <button
                                id="btn_save_email"
                                class="btn btn-sm btn-success btn-square opacity-0 pointer-events-none transition-opacity duration-150"
                                title="Salva email"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <p id="setting_email_hint" class="text-xs text-warning hidden">
                            ⚠️ Cambiando email dovrai riverificare il tuo indirizzo.
                        </p>
                    </div>

                    {{-- Password — apre modale --}}
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex flex-col gap-0.5">
                            <label class="text-xs font-bold text-base-content/50 uppercase tracking-wide">Password</label>
                            <span class="text-base font-bold tracking-widest text-base-content/40">••••••••</span>
                        </div>
                        <button
                            class="btn btn-sm btn-ghost gap-1 text-primary"
                            onclick="document.getElementById('modal_password').showModal()"
                        >
                            Cambia
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M9.71069 18.2929C10.1012 18.6834 10.7344 18.6834 11.1249 18.2929L16.0123 13.4006C16.7927 12.6195 16.7924 11.3537 16.0117 10.5729L11.1213 5.68254C10.7308 5.29202 10.0976 5.29202 9.70708 5.68254C9.31655 6.07307 9.31655 6.70623 9.70708 7.09676L13.8927 11.2824C14.2833 11.6729 14.2833 12.3061 13.8927 12.6966L9.71069 16.8787C9.32016 17.2692 9.32016 17.9023 9.71069 18.2929Z" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>

                </div>
            </div>

            {{-- ===== SEZIONE TEMA ===== --}}
            <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider px-1 mb-2">Tema</p>
            <div class="card bg-base-100 shadow mb-6">
                <div class="card-body p-4">
                    <div class="grid grid-cols-2 gap-3">

                        <button id="btn_theme_light" onclick="setTheme('light')"
                                class="flex flex-col items-center gap-3 p-4 rounded-box border-2 border-primary bg-primary/10 transition-all">
                            <div class="w-14 h-10 rounded-lg overflow-hidden border-2 border-base-300 flex shadow-sm">
                                <div class="flex-1 bg-[#EAE2D5]"></div>
                                <div class="w-4 bg-[#5A7A3A]"></div>
                            </div>
                            <span class="text-sm font-bold">Chiaro</span>
                            <div class="w-5 h-5 rounded-full bg-primary flex items-center justify-center">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 13l4 4L19 7" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </button>

                        <button id="btn_theme_dark" onclick="setTheme('dark')"
                                class="flex flex-col items-center gap-3 p-4 rounded-box border-2 border-base-200 bg-base-100 transition-all">
                            <div class="w-14 h-10 rounded-lg overflow-hidden border-2 border-base-300 flex shadow-sm">
                                <div class="flex-1 bg-[#261F14]"></div>
                                <div class="w-4 bg-[#7AAA4A]"></div>
                            </div>
                            <span class="text-sm font-bold">Scuro</span>
                            <div class="w-5 h-5 rounded-full border-2 border-base-300 flex items-center justify-center"></div>
                        </button>

                    </div>
                </div>
            </div>

            {{-- ===== SEZIONE ACCOUNT ===== --}}
            <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider px-1 mb-2">Account</p>
            <div class="card bg-base-100 shadow mb-6">
                <div class="card-body p-4 flex flex-row gap-3">

                    <button class="btn btn-outline btn-error flex-1 gap-2"
                            onclick="document.getElementById('modal_delete').showModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Elimina account
                    </button>

                    <button class="btn btn-neutral flex-1 gap-2"
                            onclick="document.getElementById('modal_logout').showModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Logout
                    </button>

                </div>
            </div>

        </div>
    </div>

    @vite(["resources/js/pages/settings.js"])
@endsection
