import { PlantViewer } from "../live2d/live2d-viewer.js";

// -----------------------------------------------------------
//  Toast
// -----------------------------------------------------------
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const alertClass = {
        success: 'alert-success',
        error:   'alert-error',
        warning: 'alert-warning',
        info:    'alert-info',
    }[type] ?? 'alert-info';

    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} shadow-lg pointer-events-auto max-w-sm text-sm`;
    toast.innerHTML = `<span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.4s';
        toast.style.opacity    = '0';
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}
window.showToast = showToast;

// -----------------------------------------------------------
//  Dati dal blade
// -----------------------------------------------------------
const PLANT_ID   = window.PLANT_ID;
const PLANT_DATA = window.PLANT_DATA ?? {};

// -----------------------------------------------------------
//  CSRF helper
// -----------------------------------------------------------
function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

// -----------------------------------------------------------
//  Helper: richiesta JSON autenticata
// -----------------------------------------------------------
async function apiRequest(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: {
            'Content-Type':      'application/json',
            'X-CSRF-TOKEN':      csrf(),
            'X-Requested-With':  'XMLHttpRequest',
        },
    };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    return res.json();
}

// -----------------------------------------------------------
//  1. ANNAFFIATURA MANUALE
// -----------------------------------------------------------
function initWatering() {
    const btnConfirm = document.getElementById('btn_confirm_water');
    if (!btnConfirm) return;

    btnConfirm.addEventListener('click', async () => {
        btnConfirm.disabled = true;
        btnConfirm.textContent = 'Salvataggio...';

        try {
            const data = await apiRequest(`/plants/${PLANT_ID}/water`, 'POST');

            if (data.status === 'ok') {
                document.getElementById('modal_watered')?.close();
                showToast('Annaffiatura registrata! 💧', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast('Errore nel salvataggio.', 'error');
            }
        } catch {
            showToast('Errore di rete.', 'error');
        } finally {
            btnConfirm.disabled = false;
            btnConfirm.textContent = 'Sì, confermo';
        }
    });
}

// -----------------------------------------------------------
//  2. STORICO — icone e campi corretti dall'API
// -----------------------------------------------------------
function initHistory() {
    // Carica lo storico ogni volta che si apre il modal
    document.querySelectorAll('[onclick*="modal_history"]').forEach(btn => {
        btn.addEventListener('click', loadHistory);
    });
}

async function loadHistory() {
    const list = document.getElementById('history_list');
    if (!list) return;

    list.innerHTML = '<li class="py-6 text-center text-sm text-base-content/50">Caricamento...</li>';

    try {
        const data = await apiRequest(`/plants/${PLANT_ID}/history`);

        if (data?.events?.length > 0) {
            list.innerHTML = data.events.map(ev => {
                const icon = ev.type === 'watering' ? './assets/NaHida_Icon_Water.png' : './assets/NaHida_Icon_Warning.png';
                const detail = ev.detail
                    ? `<p class="text-xs text-base-content/50 mt-0.5">${ev.detail}</p>`
                    : '';
                return `
                    <li class="flex items-center gap-3 px-1 py-3 border-b border-base-200 last:border-0">
                        <img src="${icon}" class="w-5 h-5 object-contain flex-shrink-0" alt="" onerror="this.style.display='none'">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold leading-snug">${ev.label}</p>
                            ${detail}
                        </div>
                        <span class="text-xs text-base-content/40 flex-shrink-0">${ev.date_str}</span>
                    </li>
                `;
            }).join('');
        } else {
            list.innerHTML = '<li class="py-6 text-center text-sm text-base-content/50">Nessun evento registrato.</li>';
        }
    } catch {
        list.innerHTML = '<li class="py-6 text-center text-sm text-error">Impossibile caricare lo storico.</li>';
    }
}

// -----------------------------------------------------------
//  3. DISPOSITIVO — status live (anche alla pagina)
// -----------------------------------------------------------
function initDevice() {
    const modal       = document.getElementById('modal_device');
    const input       = document.getElementById('device_token_input');
    const btnSave     = document.getElementById('btn_save_device');
    const btnUnlink   = document.getElementById('btn_unlink_device');
    const statusRow   = document.getElementById('device_status_row');
    const statusBadge = document.getElementById('device_status_badge');
    const statusToken = document.getElementById('device_status_token');

    if (!modal || !input) return;

    const currentToken = PLANT_DATA.device_token;
    if (currentToken) {
        input.value = currentToken;
        btnUnlink?.classList.remove('hidden');
        // Carica stato subito (non solo all'apertura del modal)
        fetchDeviceStatus(currentToken, statusRow, statusBadge, statusToken);
    }

    // Apri modal → aggiorna status
    document.querySelectorAll('[onclick*="modal_device"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const tok = PLANT_DATA.device_token;
            if (tok) fetchDeviceStatus(tok, statusRow, statusBadge, statusToken);
        });
    });

    // Salva token
    btnSave?.addEventListener('click', async () => {
        const token = input.value.trim();
        if (!token) { showToast('Inserisci un token valido.', 'warning'); return; }

        btnSave.disabled = true;
        btnSave.textContent = 'Salvataggio...';

        try {
            const data = await apiRequest(`/plants/${PLANT_ID}/device`, 'POST', { device_token: token });

            if (data.status === 'ok') {
                modal.close();
                showToast('Dispositivo collegato! 📡', 'success');
                PLANT_DATA.device_token = token;
                PLANT_DATA.has_device   = true;
                btnUnlink?.classList.remove('hidden');
                fetchDeviceStatus(token, statusRow, statusBadge, statusToken);
            } else {
                showToast(data.message ?? 'Errore nel collegamento.', 'error');
            }
        } catch {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });

    // Scollega
    btnUnlink?.addEventListener('click', async () => {
        if (!confirm('Vuoi scollegare il dispositivo?')) return;

        try {
            const data = await apiRequest(`/plants/${PLANT_ID}/device`, 'DELETE');

            if (data.status === 'ok') {
                modal.close();
                showToast('Dispositivo scollegato.', 'info');
                input.value             = '';
                PLANT_DATA.device_token = null;
                PLANT_DATA.has_device   = false;
                btnUnlink?.classList.add('hidden');
                statusRow?.classList.add('hidden');
                setPageDeviceStatus(false, null);
            }
        } catch {
            showToast('Errore di rete.', 'error');
        }
    });
}

/**
 * Recupera lo stato del dispositivo e aggiorna TUTTI gli indicatori
 * (sia nel modal che nella pagina principale).
 */
async function fetchDeviceStatus(token, statusRow, statusBadge, statusToken) {
    if (!token) return;

    try {
        const data = await apiRequest(`/device/status?device_token=${token}`);
        const isOnline = data.online;

        // Aggiorna modal
        if (statusRow) {
            statusRow.classList.remove('hidden');
            if (statusBadge) {
                statusBadge.className  = `badge ${isOnline ? 'badge-success' : 'badge-ghost'} gap-1`;
                statusBadge.textContent = isOnline ? 'Online' : 'Offline';
            }
            if (statusToken) {
                statusToken.textContent = data.last_seen_at
                    ? `Ultimo ping: ${new Date(data.last_seen_at).toLocaleString('it-IT')}`
                    : 'Mai visto';
            }
        }

        // Aggiorna indicatori in pagina
        setPageDeviceStatus(isOnline, data.last_seen_at);
    } catch {
        // silenzioso
    }
}

/**
 * Aggiorna tutti i dot/badge/testi "online/offline" presenti nel DOM
 * (action card, sidebar desktop, ecc.).
 */
function setPageDeviceStatus(isOnline, lastSeenAt) {
    // Tutti i dot di stato dispositivo
    document.querySelectorAll('[data-device-dot]').forEach(el => {
        el.className = `w-2 h-2 rounded-full flex-shrink-0 ${isOnline ? 'bg-success' : 'bg-base-300'}`;
    });
    // Tutti i testi "Online / Offline"
    document.querySelectorAll('[data-device-text]').forEach(el => {
        el.textContent  = isOnline ? 'Online' : 'Offline';
        el.className    = `text-xs ${isOnline ? 'text-success' : 'text-base-content/40'}`;
    });
    // Sidebar desktop: testo esteso
    const sidebarText = document.getElementById('device_sidebar_text');
    if (sidebarText) {
        let txt = isOnline ? 'Online' : 'Offline';
        if (!isOnline && lastSeenAt) {
            txt += ` · visto ${new Date(lastSeenAt).toLocaleString('it-IT')}`;
        }
        sidebarText.textContent = txt;
    }
    // Action card: classe del testo
    const actionText = document.getElementById('device_action_text');
    if (actionText) {
        actionText.textContent = isOnline ? 'Online' : 'Offline';
        actionText.className   = `text-xs ${isOnline ? 'text-success' : 'text-base-content/40'}`;
    }
}

// -----------------------------------------------------------
//  4. CONDIZIONI OTTIMALI
// -----------------------------------------------------------
function initConditions() {
    const modal   = document.getElementById('modal_conditions');
    const btnSave = document.getElementById('btn_save_conditions');
    if (!modal || !btnSave) return;

    document.querySelectorAll('[onclick*="modal_conditions"]').forEach(btn => {
        btn.addEventListener('click', () => {
            setValue('cond_temp_min',  PLANT_DATA.temp_min);
            setValue('cond_temp_max',  PLANT_DATA.temp_max);
            setValue('cond_hum_min',   PLANT_DATA.hum_min);
            setValue('cond_hum_max',   PLANT_DATA.hum_max);
            setValue('cond_soil_min',  PLANT_DATA.soil_hum_min);
            setValue('cond_soil_max',  PLANT_DATA.soil_hum_max);
            setValue('cond_watering',  PLANT_DATA.watering_cycle);
        });
    });

    btnSave.addEventListener('click', async () => {
        const payload = {
            temp_min:       parseFloat(document.getElementById('cond_temp_min')?.value),
            temp_max:       parseFloat(document.getElementById('cond_temp_max')?.value),
            hum_min:        parseFloat(document.getElementById('cond_hum_min')?.value),
            hum_max:        parseFloat(document.getElementById('cond_hum_max')?.value),
            soil_hum_min:   parseFloat(document.getElementById('cond_soil_min')?.value),
            soil_hum_max:   parseFloat(document.getElementById('cond_soil_max')?.value),
            watering_cycle: parseInt(document.getElementById('cond_watering')?.value),
        };

        if (payload.temp_max   < payload.temp_min)     { showToast('Temp. max deve essere ≥ min.', 'warning');  return; }
        if (payload.hum_max    < payload.hum_min)      { showToast('Umidità max deve essere ≥ min.', 'warning'); return; }
        if (payload.soil_hum_max < payload.soil_hum_min) { showToast('Suolo max deve essere ≥ min.', 'warning'); return; }
        if (!payload.watering_cycle || payload.watering_cycle < 1) { showToast('Ciclo non valido.', 'warning'); return; }

        btnSave.disabled = true;
        btnSave.textContent = 'Salvataggio...';

        try {
            const data = await apiRequest(`/plants/${PLANT_ID}`, 'PATCH', payload);

            if (data.status === 'ok') {
                Object.assign(PLANT_DATA, payload);
                modal.close();
                showToast('Condizioni aggiornate! ✅', 'success');
                updateConditionLabels(data.plant);
            } else {
                showToast('Errore nel salvataggio.', 'error');
            }
        } catch {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });
}

function updateConditionLabels(plant) {
    if (!plant) return;
    const el = (id) => document.getElementById(id);
    if (el('lbl_range_temp'))  el('lbl_range_temp').textContent  = `Ottimale: ${plant.temp_min} — ${plant.temp_max}°C`;
    if (el('lbl_range_hum'))   el('lbl_range_hum').textContent   = `Ottimale: ${plant.hum_min} — ${plant.hum_max}%`;
    if (el('lbl_range_soil'))  el('lbl_range_soil').textContent  = `Ottimale: ${plant.soil_hum_min} — ${plant.soil_hum_max}%`;
}

// -----------------------------------------------------------
//  5. NOTE
// -----------------------------------------------------------
function initNotes() {
    const modal    = document.getElementById('modal_notes');
    const textarea = document.getElementById('notes_textarea');
    const btnSave  = document.getElementById('btn_save_notes');
    if (!modal || !textarea || !btnSave) return;

    document.querySelectorAll('[onclick*="modal_notes"]').forEach(btn => {
        btn.addEventListener('click', () => { textarea.value = PLANT_DATA.notes ?? ''; });
    });

    btnSave.addEventListener('click', async () => {
        const notes = textarea.value.trim();
        btnSave.disabled = true;
        btnSave.textContent = 'Salvataggio...';

        try {
            const data = await apiRequest(`/plants/${PLANT_ID}`, 'PATCH', { notes });

            if (data.status === 'ok') {
                PLANT_DATA.notes = notes;
                modal.close();
                showToast('Note salvate! 📝', 'success');
                const preview = document.getElementById('notes_preview');
                if (preview) {
                    preview.textContent = notes
                        ? (notes.length > 50 ? notes.slice(0, 50) + '…' : notes)
                        : 'Nessuna nota';
                }
            } else {
                showToast('Errore nel salvataggio.', 'error');
            }
        } catch {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });
}

// -----------------------------------------------------------
//  6. ASPETTO
// -----------------------------------------------------------
function initAppearance() {
    const btnSave = document.getElementById('btn_save_appearance');
    if (!btnSave) return;

    document.querySelectorAll('[onclick*="modal_edit_plant"]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!window.PLANT_APPEARANCE) return;
            setSlider('range_variant',     'lbl_variant',     PLANT_APPEARANCE.plant_variant ?? 0);
            setSlider('range_pot',         'lbl_pot',         PLANT_APPEARANCE.pot_color     ?? 0);
            setSlider('range_plant_color', 'lbl_plant_color', PLANT_APPEARANCE.plant_color   ?? 0);
            setSlider('range_flower',      'lbl_flower',      PLANT_APPEARANCE.flower_color  ?? 0);
        });
    });

    ['range_variant', 'range_pot', 'range_plant_color', 'range_flower'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', () => {
            PlantViewer?.setAppearance({
                plant_variant: parseInt(document.getElementById('range_variant')?.value     ?? 0),
                pot_color:     parseInt(document.getElementById('range_pot')?.value         ?? 0),
                plant_color:   parseInt(document.getElementById('range_plant_color')?.value ?? 0),
                flower_color:  parseInt(document.getElementById('range_flower')?.value      ?? 0),
            });
        });
    });

    btnSave.addEventListener('click', async () => {
        const appearance = {
            plant_variant: parseInt(document.getElementById('range_variant')?.value     ?? 0),
            pot_color:     parseInt(document.getElementById('range_pot')?.value         ?? 0),
            plant_color:   parseInt(document.getElementById('range_plant_color')?.value ?? 0),
            flower_color:  parseInt(document.getElementById('range_flower')?.value      ?? 0),
        };

        btnSave.disabled = true;
        btnSave.textContent = 'Salvataggio...';

        try {
            const data = await apiRequest(`/plants/${PLANT_ID}`, 'PATCH', appearance);

            if (data.status === 'ok') {
                window.PLANT_APPEARANCE = { ...window.PLANT_APPEARANCE, ...appearance };
                PlantViewer?.setAppearance(appearance);
                document.getElementById('modal_edit_plant')?.close();
                showToast('Aspetto aggiornato! 🌸', 'success');
            } else {
                showToast('Errore nel salvataggio.', 'error');
            }
        } catch {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });
}

// -----------------------------------------------------------
//  7. REAL-TIME via Echo (ButtonPressed + SensorUpdated)
// -----------------------------------------------------------
function initEcho() {
    if (!window.Echo || !PLANT_ID) return;

    window.Echo.channel(`plant.${PLANT_ID}`)
        .listen('.ButtonPressed', (e) => {
            showToast(e.message ?? '💧 Annaffiatura rilevata!', 'success');
            setTimeout(() => window.location.reload(), 1500);
        })
        .listen('.SensorUpdated', (e) => {
            updateSensorDisplay(e);
        });
}

/**
 * Aggiorna i valori dei sensori nel DOM senza ricaricare la pagina.
 * Usa i range in PLANT_DATA per determinare il colore (success/error).
 */
function updateSensorDisplay(reading) {
    const pd = PLANT_DATA;

    // Helper: restituisce la classe colore in base a range min/max
    function colorClass(val, min, max) {
        return (val === null || val === undefined || (val >= min && val <= max))
            ? 'text-success'
            : 'text-error';
    }

    // Temperatura
    const tempEl = document.getElementById('val_temp');
    if (tempEl && reading.temperature !== null) {
        tempEl.textContent = `${parseFloat(reading.temperature).toFixed(1)}°C`;
        tempEl.className   = `text-2xl font-bold ${colorClass(reading.temperature, pd.temp_min, pd.temp_max)}`;
    }

    // Umidità aria
    const humEl = document.getElementById('val_hum');
    if (humEl && reading.humidity !== null) {
        humEl.textContent = `${Math.round(reading.humidity)}%`;
        humEl.className   = `text-2xl font-bold ${colorClass(reading.humidity, pd.hum_min, pd.hum_max)}`;
    }

    // Umidità suolo
    const soilEl = document.getElementById('val_soil');
    if (soilEl && reading.soil_humidity !== null) {
        soilEl.textContent = `${Math.round(reading.soil_humidity)}%`;
        soilEl.className   = `text-2xl font-bold ${colorClass(reading.soil_humidity, pd.soil_hum_min, pd.soil_hum_max)}`;
    }

    // Luminosità
    const lumEl = document.getElementById('val_lum');
    if (lumEl && reading.luminosity !== null) {
        lumEl.textContent = `${Math.round(reading.luminosity)} lx`;
        lumEl.className   = 'text-2xl font-bold text-base-content';
    }

    // Timestamp aggiornamento
    const updEl = document.getElementById('sensor_updated_at');
    if (updEl) {
        updEl.textContent = 'Aggiornato adesso';
    }

    // Toast discreto
    showToast('📊 Sensori aggiornati', 'info');
}

// -----------------------------------------------------------
//  8. POLLING stato dispositivo ogni 15s
// -----------------------------------------------------------
function initSensorPolling() {
    if (!PLANT_DATA.has_device || !PLANT_DATA.device_token) return;

    const statusRow   = document.getElementById('device_status_row');
    const statusBadge = document.getElementById('device_status_badge');
    const statusToken = document.getElementById('device_status_token');

    async function poll() {
        await fetchDeviceStatus(PLANT_DATA.device_token, statusRow, statusBadge, statusToken);
    }

    // Prima chiamata immediata per correggere stato stale server-side
    poll();
    setInterval(poll, 15_000);
}

// -----------------------------------------------------------
//  Utility helpers
// -----------------------------------------------------------
function setValue(id, value) {
    const el = document.getElementById(id);
    if (el && value != null) el.value = value;
}

function setSlider(rangeId, labelId, value) {
    const range = document.getElementById(rangeId);
    const label = document.getElementById(labelId);
    if (range) range.value = value;
    if (label) label.textContent = value;
}

window.applyAppearance = function () {
    const appearance = {
        plant_variant: parseInt(document.getElementById('range_variant')?.value     ?? 0),
        pot_color:     parseInt(document.getElementById('range_pot')?.value         ?? 0),
        plant_color:   parseInt(document.getElementById('range_plant_color')?.value ?? 0),
        flower_color:  parseInt(document.getElementById('range_flower')?.value      ?? 0),
    };
    PlantViewer?.setAppearance(appearance);
    document.getElementById('modal_edit_plant')?.close();
};

// -----------------------------------------------------------
//  INIT
// -----------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    initWatering();
    initHistory();
    initDevice();
    initConditions();
    initNotes();
    initAppearance();
    initEcho();
    initSensorPolling();

    if (PlantViewer && window.PLANT_HEALTH) {
        PlantViewer.setHealth(window.PLANT_HEALTH);
    }
});
