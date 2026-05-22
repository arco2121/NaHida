<!-- ===== MODALI ===== -->

<!-- Modale: Conferma annaffiatura -->
<dialog id="modal_watered" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="text-lg font-bold mb-1">Hai annaffiato la pianta?</h3>
        <p class="text-sm text-base-content/60 mb-4">Verrà registrata una nuova annaffiatura e il timer verrà resettato.</p>
        <div class="modal-action gap-2">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button class="btn btn-primary">Sì, confermo</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Storico -->
<dialog id="modal_history" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-4">Storico</h3>
        <ul class="divide-y divide-base-200">
            <li class="flex items-center gap-3 py-3">
                <img src="assets/NaHida_Icon_Water.png" class="w-5 h-5 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                <div class="flex-1 text-sm">Annaffiatura</div>
                <span class="text-xs text-base-content/50">11 mag 09:14</span>
            </li>
            <li class="flex items-center gap-3 py-3">
                <img src="assets/NaHida_Icon_Warning.png" class="w-5 h-5 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                <div class="flex-1 text-sm">Temperatura alta (32°C)</div>
                <span class="text-xs text-base-content/50">11 mag 07:02</span>
            </li>
            <li class="flex items-center gap-3 py-3">
                <img src="assets/NaHida_Icon_Water.png" class="w-5 h-5 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                <div class="flex-1 text-sm">Annaffiatura</div>
                <span class="text-xs text-base-content/50">8 mag 10:30</span>
            </li>
            <li class="flex items-center gap-3 py-3">
                <img src="assets/NaHida_Icon_Water.png" class="w-5 h-5 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                <div class="flex-1 text-sm">Annaffiatura</div>
                <span class="text-xs text-base-content/50">5 mag 08:45</span>
            </li>
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
        <div class="flex items-center gap-2 mb-4">
                <span class="badge badge-success gap-1">
                    <img src="assets/NaHida_Icon_Device.png" class="w-3 h-3 object-contain" alt="" onerror="this.style.display='none'">
                    Online
                </span>
            <span class="text-sm text-base-content/60">esp32-nahida-a3f2</span>
        </div>
        <label class="label text-sm font-bold mb-1">Token dispositivo</label>
        <input type="text" class="input w-full mb-4" placeholder="es. a3f2b1c9d..." value="a3f2b1c9d84e" />
        <div class="modal-action gap-2">
            <button class="btn btn-ghost btn-sm text-error">Scollega</button>
            <button class="btn btn-primary">Salva</button>
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
                <input type="radio" name="music" class="radio radio-primary" checked />
                <span class="text-sm font-bold">Nessuna</span>
            </label>
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" />
                <span class="text-sm font-bold">Pioggia leggera</span>
            </label>
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" />
                <span class="text-sm font-bold">Foresta</span>
            </label>
            <label class="flex items-center gap-3 p-3 bg-base-200 rounded-box cursor-pointer">
                <input type="radio" name="music" class="radio radio-primary" />
                <span class="text-sm font-bold">Lo-fi chill</span>
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
                    <input type="number" class="input input-sm w-full" value="18" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Temp. max (°C)</label>
                    <input type="number" class="input input-sm w-full" value="26" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Umidità aria min (%)</label>
                    <input type="number" class="input input-sm w-full" value="50" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Umidità aria max (%)</label>
                    <input type="number" class="input input-sm w-full" value="80" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Suolo min (%)</label>
                    <input type="number" class="input input-sm w-full" value="40" />
                </div>
                <div>
                    <label class="label text-xs font-bold">Suolo max (%)</label>
                    <input type="number" class="input input-sm w-full" value="70" />
                </div>
            </div>
            <div>
                <label class="label text-xs font-bold">Ogni quanto va annaffiata (ore)</label>
                <input type="number" class="input input-sm w-full" value="72" />
            </div>
        </div>
        <div class="modal-action">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button class="btn btn-primary">Salva</button>
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
        <textarea class="textarea w-full h-36" placeholder="Scrivi qualcosa sulla tua pianta...">Pianta comprata il 3 marzo. Va bene in penombra, attenzione al sole diretto in estate.</textarea>
        <div class="modal-action">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button class="btn btn-primary">Salva</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

<!-- Modale: Modifica pianta -->
<!-- Parametri da PlantViewer JS:
     plant_variant: 0-6  (6 varianti)
     pot_color:     0-2  (3 colori vaso)
     plant_color:   0-5  (6 colori pianta)
     flower_color:  0-6  (7 colori fiore)
-->
<dialog id="modal_edit_plant" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold mb-4">Modifica pianta</h3>
        <div class="flex flex-col gap-5">

            <!-- Nome -->
            <div>
                <label class="label text-sm font-bold">Nome</label>
                <input type="text" class="input w-full" value="Monstera Deliciosa" />
            </div>

            <div class="divider my-0 text-xs text-base-content/40">Aspetto del modello</div>

            <!-- Variante pianta: 0-6 -->
            <div>
                <div class="flex justify-between items-baseline mb-1">
                    <label class="label text-sm font-bold p-0">Variante pianta</label>
                    <span id="lbl_variant" class="text-sm font-bold text-primary">2</span>
                </div>
                <input type="range" id="range_variant" min="0" max="6" value="2" step="1"
                       class="range range-primary range-sm"
                       oninput="document.getElementById('lbl_variant').textContent = this.value" />
                <div class="flex justify-between text-xs text-base-content/40 px-0.5 mt-0.5">
                    <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span>
                </div>
            </div>

            <!-- Colore vaso: 0-2 -->
            <div>
                <div class="flex justify-between items-baseline mb-1">
                    <label class="label text-sm font-bold p-0">Colore vaso</label>
                    <span id="lbl_pot" class="text-sm font-bold text-secondary">0</span>
                </div>
                <input type="range" id="range_pot" min="0" max="2" value="0" step="1"
                       class="range range-secondary range-sm"
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
                       class="range range-accent range-sm"
                       oninput="document.getElementById('lbl_plant_color').textContent = this.value" />
                <div class="flex justify-between text-xs text-base-content/40 px-0.5 mt-0.5">
                    <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                </div>
            </div>

            <!-- Colore fiore: 0-6 -->
            <div>
                <div class="flex justify-between items-baseline mb-1">
                    <label class="label text-sm font-bold p-0">Colore fiore</label>
                    <span id="lbl_flower" class="text-sm font-bold">3</span>
                </div>
                <input type="range" id="range_flower" min="0" max="6" value="3" step="1"
                       class="range range-sm"
                       oninput="document.getElementById('lbl_flower').textContent = this.value" />
                <div class="flex justify-between text-xs text-base-content/40 px-0.5 mt-0.5">
                    <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span>
                </div>
            </div>

        </div>
        <div class="modal-action">
            <form method="dialog"><button class="btn btn-ghost">Annulla</button></form>
            <button class="btn btn-primary" onclick="applyAppearance()">Salva</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>

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
            <a href="../../../../../../../Downloads/plant_detail.html" class="btn btn-primary">Conferma</a>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>chiudi</button></form>
</dialog>
