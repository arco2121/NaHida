<!-- ===== MODALI ===== -->

<!-- Modale: Conferma annaffiatura -->
<dialog id="modal_watered" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="text-lg font-bold mb-1">Hai annaffiato la pianta?</h3>
        <p class="text-sm text-base-content/60 mb-4">Verrà registrata una nuova annaffiatura e il timer verrà resettato.</p>
        <div class="modal-action gap-2">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button id="btn_confirm_water" class="btn btn-primary">Sì, confermo</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Storico (annaffiature + anomalie) -->
<dialog id="modal_history" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-4">Storico</h3>
        <ul id="history_list" class="divide-y divide-base-200">
            <li class="py-6 text-center text-sm text-base-content/50">Caricamento...</li>
        </ul>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Collega dispositivo -->
<dialog id="modal_device" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-1">Collega dispositivo</h3>
        <p class="text-sm text-base-content/60 mb-4">Inserisci il token del tuo dispositivo ESP32.</p>

        <div id="device_status_row" class="flex items-center gap-2 mb-4 hidden">
            <span id="device_status_badge" class="badge badge-success gap-1">Online</span>
            <span id="device_status_token" class="text-sm text-base-content/60"></span>
        </div>

        <label class="label text-sm font-bold mb-1">Token dispositivo</label>
        <input id="device_token_input" type="text" class="input w-full mb-4"
               placeholder="es. a3f2b1c9d..." />

        <div class="modal-action gap-2">
            <button id="btn_unlink_device" class="btn btn-ghost btn-sm text-error hidden">Scollega</button>
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button id="btn_save_device" class="btn btn-primary">Salva</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Musica -->
<dialog id="modal_music" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-4">Musica di sottofondo</h3>
        <div class="flex flex-col gap-2">
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" value="-1" checked />
                <span class="text-sm font-bold">Nessuna</span>
            </label>
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" value="6"/>
                <span class="text-sm font-bold">Lily & Daisy</span>
            </label>
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" value="7"/>
                <span class="text-sm font-bold">City Lights</span>
            </label>
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" value="8"/>
                <span class="text-sm font-bold">Dreamy Days</span>
            </label>
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" value="9"/>
                <span class="text-sm font-bold">Sunny Symphony</span>
            </label>
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" value="10"/>
                <span class="text-sm font-bold">Jazzberry Jam</span>
            </label>
        </div>
        <div class="modal-action">
            <form method="dialog"><button class="btn btn-primary">Conferma</button></form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Condizioni ottimali -->
<dialog id="modal_conditions" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-4">Condizioni ottimali</h3>
        <div class="flex flex-col gap-3">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="label text-xs font-bold">Temp. min (°C)</label>
                    <input id="cond_temp_min" type="number" class="input input-sm w-full" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Temp. max (°C)</label>
                    <input id="cond_temp_max" type="number" class="input input-sm w-full" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Umidità aria min (%)</label>
                    <input id="cond_hum_min" type="number" class="input input-sm w-full" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Umidità aria max (%)</label>
                    <input id="cond_hum_max" type="number" class="input input-sm w-full" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Suolo min (%)</label>
                    <input id="cond_soil_min" type="number" class="input input-sm w-full" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Suolo max (%)</label>
                    <input id="cond_soil_max" type="number" class="input input-sm w-full" />
                </div>
            </div>
            <div>
                <label class="label text-xs font-bold">Ogni quanto va annaffiata (ore)</label>
                <input id="cond_watering" type="number" class="input input-sm w-full" min="1" />
            </div>
            <div>
                <label class="label text-xs font-bold">Esigenza di luce</label>
                <select id="cond_lux_preset" class="select select-sm w-full">
                    <option value="low">Poca luce (0 – 500 lx)</option>
                    <option value="medium">Media luce (500 – 2000 lx)</option>
                    <option value="high">Tanta luce (2000 – 100 000 lx)</option>
                </select>
                <input type="hidden" id="cond_lux_min" value="0" />
                <input type="hidden" id="cond_lux_max" value="500" />
            </div>
        </div>
        <div class="modal-action">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button id="btn_save_conditions" class="btn btn-primary">Salva</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Note -->
<dialog id="modal_notes" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-4">Note</h3>
        <textarea id="notes_textarea" class="textarea w-full h-36"
                  placeholder="Scrivi qualcosa sulla tua pianta..."></textarea>
        <div class="modal-action">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button id="btn_save_notes" class="btn btn-primary">Salva</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Modifica aspetto pianta -->
<dialog id="modal_edit_plant" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-4">Modifica aspetto</h3>
        <div class="flex flex-col gap-5">

            <div>
                <label class="label text-sm font-bold">Nome</label>
                <input id="edit_plant_name" type="text" class="input w-full"
                       maxlength="100"
                       value="{{ $params["plant"]->plant_name ?? '' }}" />
                <p id="edit_plant_name_error" class="text-error text-xs mt-1 hidden"></p>
            </div>

            <div class="divider my-0 text-xs text-base-content/40">Aspetto del modello</div>

            <!-- Variante pianta: 0-6 -->
            <div>
                <div class="flex justify-between items-baseline mb-1">
                    <label class="label text-sm font-bold p-0">Variante pianta</label>
                    <span id="lbl_variant" class="text-sm font-bold text-primary">0</span>
                </div>
                <input type="range" id="range_variant" min="0" max="7" value="0" step="1"
                       class="range w-full range-primary range-sm"
                       oninput="document.getElementById('lbl_variant').textContent = this.value" />
                <div class="flex justify-between text-xs text-base-content/40 px-0.5 mt-0.5">
                    <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span>
                </div>
            </div>

            <!-- Colore vaso: 0-2 -->
            <div>
                <div class="flex justify-between items-baseline mb-1">
                    <label class="label text-sm font-bold p-0">Colore vaso</label>
                    <span id="lbl_pot" class="text-sm font-bold text-secondary">0</span>
                </div>
                <input type="range" id="range_pot" min="0" max="2" value="0" step="1"
                       class="range w-full range-secondary range-sm"
                       oninput="document.getElementById('lbl_pot').textContent = this.value" />
                <div class="flex justify-between text-xs text-base-content/40 px-0.5 mt-0.5">
                    <span>0</span><span>1</span><span>2</span>
                </div>
            </div>

            <!-- Colore pianta: 0-5 -->
            <div>
                <div class="flex justify-between items-baseline mb-1">
                    <label class="label text-sm font-bold p-0">Colore pianta</label>
                    <span id="lbl_plant_color" class="text-sm font-bold text-accent">0</span>
                </div>
                <input type="range" id="range_plant_color" min="0" max="5" value="0" step="1"
                       class="range w-full range-accent range-sm"
                       oninput="document.getElementById('lbl_plant_color').textContent = this.value" />
                <div class="flex justify-between text-xs text-base-content/40 px-0.5 mt-0.5">
                    <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                </div>
            </div>

            <!-- Colore fiore: 0-6 -->
            <div>
                <div class="flex justify-between items-baseline mb-1">
                    <label class="label text-sm font-bold p-0">Colore fiore</label>
                    <span id="lbl_flower" class="text-sm font-bold">0</span>
                </div>
                <input type="range" id="range_flower" min="0" max="6" value="0" step="1"
                       class="range w-full range-sm"
                       oninput="document.getElementById('lbl_flower').textContent = this.value" />
                <div class="flex justify-between text-xs text-base-content/40 px-0.5 mt-0.5">
                    <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span>
                </div>
            </div>

        </div>
        <div class="modal-action">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button id="btn_save_appearance" class="btn btn-primary">Salva</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Conferma creazione pianta (usato solo in plants/create) -->
<dialog id="modal_confirm" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="text-lg font-bold mb-1">Conferma nuova pianta</h3>
        <p class="text-sm text-base-content/60 mb-4">Stai per aggiungere la pianta con le impostazioni scelte. Potrai modificarle in qualsiasi momento dai dettagli.</p>
        <div class="bg-base-200 rounded-box p-4 mb-4 flex flex-col gap-1 text-sm">
            <div class="flex justify-between">
                <span class="text-base-content/50">Nome</span>
                <span class="font-bold" id="summary_name">—</span>
            </div>
            <div class="flex justify-between">
                <span class="text-base-content/50">Condizioni</span>
                <span class="font-bold" id="summary_template">Custom</span>
            </div>
            <div class="flex justify-between">
                <span class="text-base-content/50">Annaffiatura</span>
                <span class="font-bold" id="summary_water">—</span>
            </div>
        </div>
        <div class="modal-action gap-2">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button type="button" id="btn_submit_plant" class="btn btn-primary">Conferma</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Toast container -->
<div id="toast-container" class="toast toast-bottom toast-center z-[999] pointer-events-none"></div>

<!-- Modale: Cambio password -->
<dialog id="modal_password" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-4">Cambia password</h3>
        <div class="flex flex-col gap-3">
            <div>
                <label class="label text-sm font-bold">Password attuale</label>
                <input type="password" class="input w-full" placeholder="••••••••" />
            </div>
            <div>
                <label class="label text-sm font-bold">Nuova password</label>
                <input type="password" class="input w-full" placeholder="••••••••" />
            </div>
            <div>
                <label class="label text-sm font-bold">Conferma nuova password</label>
                <input type="password" class="input w-full" placeholder="••••••••" />
            </div>
        </div>
        <div class="modal-action">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button class="btn btn-primary">Salva</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Logout -->
<dialog id="modal_logout" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="text-lg font-bold mb-1">Vuoi uscire?</h3>
        <p class="text-sm text-base-content/60 mb-4">Verrai reindirizzato alla pagina di login.</p>
        <div class="modal-action gap-2">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <form method="post" action="{{ route("logout") }}"><button type="submit" class="btn btn-neutral">Logout</button></form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Elimina account — step 1 -->
<dialog id="modal_delete" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold text-error mb-1">Elimina account</h3>
        <p class="text-sm text-base-content/60 mb-4">Questa azione è <strong>irreversibile</strong>. Tutti i tuoi dati, piante e storici verranno eliminati definitivamente.</p>
        <div class="modal-action gap-2">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button class="btn btn-error"
                    onclick="document.getElementById('modal_delete').close(); document.getElementById('modal_delete_confirm').showModal()">
                Continua
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Elimina account — step 2 doppia conferma -->
<dialog id="modal_delete_confirm" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="text-lg font-bold text-error mb-1">Sei sicuro?</h3>
        <p class="text-sm text-base-content/60 mb-3">Scrivi <strong class="text-base-content">ELIMINA</strong> per confermare.</p>
        <input type="text" id="delete_confirm_input" class="input w-full" placeholder="ELIMINA" />
        <div class="modal-action gap-2">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button class="btn btn-error" id="btn_delete_final" disabled
                    onclick="alert('Account eliminato (mockup)')">
                Elimina definitivamente
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>
