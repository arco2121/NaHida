import { PlantViewer } from "../live2d/live2d-viewer.js";
import { Chart, LineController, LineElement, PointElement, LinearScale, CategoryScale, Filler, Tooltip } from 'https://cdn.jsdelivr.net/npm/chart.js@4.5.1/+esm';

Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Filler, Tooltip);

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
//  Helper: invia configurazione ottimale all'ESP via MQTT
// -----------------------------------------------------------
async function sendDeviceConfig() {
    const token = PLANT_DATA.device_token;
    if (!token) return;
    try {
        await apiRequest('/device/send-config', 'POST', { device_token: token });
    } catch {}
}

async function sendMusic(source) {
    const token = PLANT_DATA.device_token;
    if (!token) return;
    try {
        await apiRequest('/device/send-music', 'POST', { device_token: token, source });
    } catch {}
}

// -----------------------------------------------------------
//  Stato salute: calcolo centralizzato
//  Ritorna { state, errorCount, healthColor, healthLabel, healthEmoji }
// -----------------------------------------------------------
function calcHealth(reading) {
    if (!reading) return { state: 'normal', errorCount: 0, healthColor: 'success', healthLabel: 'Condizioni ottimali', healthEmoji: 'NaHida_Emoji_Happy.png' };

    const pd = PLANT_DATA;
    let errorCount = 0;

    if (reading.temperature !== null && reading.temperature !== undefined) {
        if (reading.temperature < pd.temp_min || reading.temperature > pd.temp_max) errorCount++;
    }
    if (reading.humidity !== null && reading.humidity !== undefined) {
        if (reading.humidity < pd.hum_min || reading.humidity > pd.hum_max) errorCount++;
    }
    if (reading.soil_humidity !== null && reading.soil_humidity !== undefined) {
        if (reading.soil_humidity < pd.soil_hum_min || reading.soil_humidity > pd.soil_hum_max) errorCount++;
    }

    let state = 'normal';
    if (reading.luminosity !== null && reading.luminosity !== undefined && reading.luminosity < 50) {
        state = 'sleep';
    } else if (errorCount >= 2) {
        state = 'sad';
    } else if (errorCount === 1) {
        state = 'mid';
    }

    const healthColor = errorCount === 0 ? 'success' : errorCount === 1 ? 'warning' : 'error';
    const healthLabel = errorCount === 0 ? 'Condizioni ottimali' : errorCount === 1 ? 'Attenzione richiesta' : 'Condizioni pessime';
    const healthEmoji = errorCount === 0 ? 'NaHida_Emoji_Happy.png' : errorCount === 1 ? 'NaHida_Emoji_Mid.png' : 'NaHida_Emoji_Sad.png';

    return { state, errorCount, healthColor, healthLabel, healthEmoji };
}

// -----------------------------------------------------------
//  Aggiorna badge salute in cima al modello Live2D
// -----------------------------------------------------------
function updateHealthBadge({ healthColor, healthLabel, healthEmoji }) {
    const badge = document.getElementById('health_badge');
    if (!badge) return;

    badge.className = `flex items-center gap-2 bg-${healthColor}/12 border border-${healthColor}/25 rounded-full px-4 py-1.5`;

    const img = badge.querySelector('[data-health-emoji]');
    if (img) img.src = `/assets/${healthEmoji}`;

    const label = badge.querySelector('[data-health-label]');
    if (label) {
        label.textContent = healthLabel;
        label.className   = `text-sm font-bold text-${healthColor}`;
    }
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
                PlantViewer?.playWatering();
                updateNextWateringDisplay(data.watered_at);
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
//  Aggiorna la card "Prossima annaffiata" dinamicamente
// -----------------------------------------------------------
function updateNextWateringDisplay(wateredAt = null) {
    const base     = wateredAt ? new Date(wateredAt) : new Date();
    const cycleMs  = (PLANT_DATA.watering_cycle ?? 24) * 3600 * 1000;
    const nextDate = new Date(base.getTime() + cycleMs);
    const now      = new Date();
    const diffMs   = nextDate - now;

    let dateLabel, subLabel;
    if (diffMs < 0) {
        dateLabel = 'In ritardo!';
        subLabel  = `Avrebbe dovuto essere annaffiata ${formatRelative(nextDate)}`;
    } else {
        dateLabel = formatDay(nextDate);
        subLabel  = formatRelative(nextDate);
    }

    const isOverdue = diffMs < 0;
    const color     = isOverdue ? 'error' : 'base-content';

    document.querySelectorAll('[data-watering-title]').forEach(el => {
        el.textContent  = dateLabel;
        el.className    = `font-bold text-${color}`;
    });
    document.querySelectorAll('[data-watering-sub]').forEach(el => {
        el.textContent = subLabel;
    });
}

function formatDay(date) {
    return date.toLocaleDateString('it-IT', { weekday: 'long', day: 'numeric', month: 'long' });
}

function formatRelative(date) {
    const diffMs = date - new Date();
    const abs    = Math.abs(diffMs);
    const h      = Math.floor(abs / 3600000);
    const d      = Math.floor(h / 24);
    if (d > 0) return diffMs < 0 ? `${d} giorn${d===1?'o':'i'} fa` : `tra ${d} giorn${d===1?'o':'i'}`;
    if (h > 0) return diffMs < 0 ? `${h} or${h===1?'a':'e'} fa` : `tra ${h} or${h===1?'a':'e'}`;
    return diffMs < 0 ? 'poco fa' : 'tra poco';
}

// -----------------------------------------------------------
//  2. STORICO
// -----------------------------------------------------------
function initHistory() {
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
                const icon = ev.type === 'watering'
                    ? '../assets/NaHida_Icon_Water.png'
                    : '../assets/NaHida_Icon_Warning.png';
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
//  3. DISPOSITIVO — status live
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
        fetchDeviceStatus(currentToken, statusRow, statusBadge, statusToken);
    }

    document.querySelectorAll('[onclick*="modal_device"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const tok = PLANT_DATA.device_token;
            if (tok) fetchDeviceStatus(tok, statusRow, statusBadge, statusToken);
        });
    });

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
                await sendDeviceConfig();
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

async function fetchDeviceStatus(token, statusRow, statusBadge, statusToken) {
    if (!token) return;

    try {
        const data     = await apiRequest(`/device/status?device_token=${token}`);
        const isOnline = data.online;

        if (statusRow) {
            statusRow.classList.remove('hidden');
            if (statusBadge) {
                statusBadge.className   = `badge ${isOnline ? 'badge-success' : 'badge-ghost'} gap-1`;
                statusBadge.textContent = isOnline ? 'Online' : 'Offline';
            }
            if (statusToken) {
                statusToken.textContent = data.last_seen_at
                    ? `Ultimo ping: ${new Date(data.last_seen_at).toLocaleString('it-IT')}`
                    : 'Mai visto';
            }
        }

        setPageDeviceStatus(isOnline, data.last_seen_at);
    } catch {}
}

function setPageDeviceStatus(isOnline, lastSeenAt) {
    document.querySelectorAll('[data-device-dot]').forEach(el => {
        el.className = `w-2 h-2 rounded-full flex-shrink-0 ${isOnline ? 'bg-success' : 'bg-base-300'}`;
    });
    document.querySelectorAll('[data-device-text]').forEach(el => {
        el.textContent = isOnline ? 'Online' : 'Offline';
        el.className   = `text-xs ${isOnline ? 'text-success' : 'text-base-content/40'}`;
    });
    const sidebarText = document.getElementById('device_sidebar_text');
    if (sidebarText) {
        let txt = isOnline ? 'Online' : 'Offline';
        if (!isOnline && lastSeenAt) {
            txt += ` · visto ${new Date(lastSeenAt).toLocaleString('it-IT')}`;
        }
        sidebarText.textContent = txt;
    }
    const actionText = document.getElementById('device_action_text');
    if (actionText) {
        actionText.textContent = isOnline ? 'Online' : 'Offline';
        actionText.className   = `text-xs ${isOnline ? 'text-success' : 'text-base-content/40'}`;
    }
}

// -----------------------------------------------------------
//  4. CONDIZIONI OTTIMALI
// -----------------------------------------------------------
const LUX_PRESETS = {
    low:    [0,    500],
    medium: [500,  2000],
    high:   [2000, 100000],
};

function updateLuxHidden(preset) {
    const [min, max] = LUX_PRESETS[preset] ?? [0, 100000];
    const minEl = document.getElementById('cond_lux_min');
    const maxEl = document.getElementById('cond_lux_max');
    if (minEl) minEl.value = min;
    if (maxEl) maxEl.value = max;
}

function initConditions() {
    const modal   = document.getElementById('modal_conditions');
    const btnSave = document.getElementById('btn_save_conditions');
    if (!modal || !btnSave) return;

    let _condUnit = 'ore';

    function setCondUnit(unit) {
        _condUnit = unit;
        const isOre = unit === 'ore';
        document.getElementById('cond_btn_ore').className =
            `cond-unit-btn px-3 py-1.5 text-xs font-bold transition-all ${isOre ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content'}`;
        document.getElementById('cond_btn_giorni').className =
            `cond-unit-btn px-3 py-1.5 text-xs font-bold transition-all ${!isOre ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content'}`;
        syncCondWateringPreview();
    }

    function syncCondWateringPreview() {
        const val = document.getElementById('cond_watering_display')?.value;
        const prev = document.getElementById('cond_watering_preview');
        if (prev) prev.textContent = val ? `Verrà annaffiata ogni ${val} ${_condUnit}` : '';
        // Aggiorna il hidden in ore
        const hrs = val ? (parseInt(val) * (_condUnit === 'giorni' ? 24 : 1)) : '';
        const hidden = document.getElementById('cond_watering');
        if (hidden) hidden.value = hrs;
    }

    document.querySelectorAll('[onclick*="modal_conditions"]').forEach(btn => {
        btn.addEventListener('click', () => {
            setValue('cond_temp_min',  PLANT_DATA.temp_min);
            setValue('cond_temp_max',  PLANT_DATA.temp_max);
            setValue('cond_hum_min',   PLANT_DATA.hum_min);
            setValue('cond_hum_max',   PLANT_DATA.hum_max);
            setValue('cond_soil_min',  PLANT_DATA.soil_hum_min);
            setValue('cond_soil_max',  PLANT_DATA.soil_hum_max);
            _condUnit = 'ore';
            setCondUnit('ore');
            const dispEl = document.getElementById('cond_watering_display');
            if (dispEl) dispEl.value = PLANT_DATA.watering_cycle ?? '';
            syncCondWateringPreview();

            const luxMax = PLANT_DATA.lux_max ?? 100000;
            const sel = document.getElementById('cond_lux_preset');
            if (sel) {
                if (luxMax <= 500)       sel.value = 'low';
                else if (luxMax <= 2000) sel.value = 'medium';
                else                     sel.value = 'high';
                updateLuxHidden(sel.value);
            }
        });
    });

    document.getElementById('cond_lux_preset')?.addEventListener('change', function () {
        updateLuxHidden(this.value);
    });
    document.getElementById('cond_btn_ore')?.addEventListener('click', () => setCondUnit('ore'));
    document.getElementById('cond_btn_giorni')?.addEventListener('click', () => setCondUnit('giorni'));
    document.getElementById('cond_watering_display')?.addEventListener('input', syncCondWateringPreview);

    btnSave.addEventListener('click', async () => {
        const payload = {
            temp_min:       parseFloat(document.getElementById('cond_temp_min')?.value),
            temp_max:       parseFloat(document.getElementById('cond_temp_max')?.value),
            hum_min:        parseFloat(document.getElementById('cond_hum_min')?.value),
            hum_max:        parseFloat(document.getElementById('cond_hum_max')?.value),
            soil_hum_min:   parseFloat(document.getElementById('cond_soil_min')?.value),
            soil_hum_max:   parseFloat(document.getElementById('cond_soil_max')?.value),
            watering_cycle: parseInt(document.getElementById('cond_watering')?.value),
            lux_min:        parseFloat(document.getElementById('cond_lux_min')?.value ?? 0),
            lux_max:        parseFloat(document.getElementById('cond_lux_max')?.value ?? 100000),
        };

        if (payload.temp_max     < payload.temp_min)     { showToast('Temp. max deve essere >= min.', 'warning');  return; }
        if (payload.hum_max      < payload.hum_min)      { showToast('Umidità max deve essere >= min.', 'warning'); return; }
        if (payload.soil_hum_max < payload.soil_hum_min) { showToast('Suolo max deve essere >= min.', 'warning');   return; }
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

                const lastReading = window.PLANT_HEALTH;
                if (lastReading) {
                    const health = calcHealth(lastReading);
                    updateHealthBadge(health);
                    PlantViewer?.setState(health.state);
                }

                await sendDeviceConfig();
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
//  6. ASPETTO + NOME
//  - Snapshot dell'aspetto salvato al momento dell'apertura.
//  - I cambiamenti agli slider aggiornano il modello in tempo reale.
//  - Se il modale viene chiuso senza salvare (Annulla, X, backdrop),
//    il modello torna all'aspetto originale (snapshot).
//  - Solo un salvataggio riuscito aggiorna lo snapshot.
// -----------------------------------------------------------
function initAppearance() {
    const modal   = document.getElementById('modal_edit_plant');
    const btnSave = document.getElementById('btn_save_appearance');
    if (!modal || !btnSave) return;

    // Snapshot dell'aspetto al momento dell'apertura del modale.
    // Viene aggiornato solo dopo un salvataggio riuscito.
    let _savedAppearance = null;

    // Flag: diventa true solo quando btnSave porta a termine con successo.
    let _didSave = false;

    // --- Apertura modale: popola slider e scatta snapshot ---
    document.querySelectorAll('[onclick*="modal_edit_plant"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const nameInput = document.getElementById('edit_plant_name');
            if (nameInput) nameInput.value = PLANT_DATA.plant_name ?? '';
            hideNameError();

            const appearance = window.PLANT_APPEARANCE ?? {};
            setSlider('range_variant',     'lbl_variant',     appearance.plant_variant ?? 0);
            setSlider('range_pot',         'lbl_pot',         appearance.pot_color     ?? 0);
            setSlider('range_plant_color', 'lbl_plant_color', appearance.plant_color   ?? 0);
            setSlider('range_flower',      'lbl_flower',      appearance.flower_color  ?? 0);

            // Scatta lo snapshot dell'aspetto corrente (quello salvato sul server)
            _savedAppearance = { ...appearance };
            _didSave = false;
        });
    });

    // --- Slider: aggiorna il modello in anteprima live ---
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

    document.getElementById('edit_plant_name')?.addEventListener('input', hideNameError);

    // --- Chiusura modale: ripristina se non è stato salvato ---
    modal.addEventListener('close', () => {
        if (!_didSave && _savedAppearance) {
            PlantViewer?.setAppearance(_savedAppearance);
        }
        _didSave = false;
    });

    // --- Salva ---
    btnSave.addEventListener('click', async () => {
        const nameInput = document.getElementById('edit_plant_name');
        const plantName = nameInput?.value.trim() ?? '';

        if (!plantName) {
            showNameError('Il nome non può essere vuoto.');
            return;
        }
        if (plantName.length > 100) {
            showNameError('Il nome è troppo lungo (max 100 caratteri).');
            return;
        }

        const appearance = {
            plant_name:    plantName,
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
                // Segnala che la chiusura è conseguente a un salvataggio riuscito
                _didSave = true;

                Object.assign(PLANT_DATA, appearance);
                window.PLANT_APPEARANCE = { ...window.PLANT_APPEARANCE, ...appearance };
                PlantViewer?.setAppearance(appearance);

                modal.close();
                showToast('Aspetto aggiornato! 🌸', 'success');

                const nameDisplay = document.getElementById('plant_name_display');
                if (nameDisplay) nameDisplay.textContent = plantName;
                document.title = plantName;

                await sendDeviceConfig();
                await PlantViewer?.capturePreview(PLANT_ID);
            } else {
                const serverError = data.errors?.plant_name?.[0];
                if (serverError) {
                    showNameError(serverError);
                } else {
                    showToast('Errore nel salvataggio.', 'error');
                }
            }
        } catch {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });
}

function showNameError(msg) {
    const el    = document.getElementById('edit_plant_name_error');
    const input = document.getElementById('edit_plant_name');
    if (el)    { el.textContent = msg; el.classList.remove('hidden'); }
    if (input) input.classList.add('input-error');
}

function hideNameError() {
    const el    = document.getElementById('edit_plant_name_error');
    const input = document.getElementById('edit_plant_name');
    if (el)    el.classList.add('hidden');
    if (input) input.classList.remove('input-error');
}

// -----------------------------------------------------------
//  7. REAL-TIME via Echo (ButtonPressed + SensorUpdated)
// -----------------------------------------------------------
function initEcho() {
    if (!window.Echo || !PLANT_ID) return;

    window.Echo.channel(`plant.${PLANT_ID}`)
        .listen('.ButtonPressed', (e) => {
            showToast(e.message ?? '💧 Annaffiatura rilevata!', 'success');
            PlantViewer?.playWatering();
            updateNextWateringDisplay(new Date().toISOString());
            setTimeout(() => window.location.reload(), 1500);
        })
        .listen('.SensorUpdated', (e) => {
            window.PLANT_HEALTH = {
                temperature:   e.temperature,
                humidity:      e.humidity,
                soil_humidity: e.soil_humidity,
                luminosity:    e.luminosity,
            };

            updateSensorDisplay(e);

            const health = calcHealth(e);
            updateHealthBadge(health);
            PlantViewer?.setState(health.state);

            window._lastEchoUpdate = Date.now();
        });

    console.log(`[Echo] In ascolto su plant.${PLANT_ID}`);
}

// -----------------------------------------------------------
//  8. POLLING AJAX come fallback/integrazione a Echo
// -----------------------------------------------------------
function initSensorPolling() {
    if (!PLANT_DATA.has_device || !PLANT_DATA.device_token) return;

    const statusRow   = document.getElementById('device_status_row');
    const statusBadge = document.getElementById('device_status_badge');
    const statusToken = document.getElementById('device_status_token');

    async function pollReading() {
        if (window._lastEchoUpdate && (Date.now() - window._lastEchoUpdate) < 20_000) {
            return;
        }

        try {
            const data = await apiRequest(`/plants/${PLANT_ID}/latest-reading`);
            if (data?.reading) {
                const r = data.reading;

                const newTime = new Date(r.recorded_at).getTime();
                if (!window._lastReadingTime || newTime > window._lastReadingTime) {
                    window._lastReadingTime = newTime;

                    window.PLANT_HEALTH = {
                        temperature:   r.temperature,
                        humidity:      r.humidity,
                        soil_humidity: r.soil_humidity,
                        luminosity:    r.luminosity,
                    };

                    updateSensorDisplay(r);

                    const health = calcHealth(r);
                    updateHealthBadge(health);
                    PlantViewer?.setState(health.state);
                }
            }
        } catch { /* silenzioso */ }
    }

    async function pollStatus() {
        await fetchDeviceStatus(PLANT_DATA.device_token, statusRow, statusBadge, statusToken);
    }

    pollReading();
    pollStatus();

    setInterval(pollReading, 15_000);
    setInterval(pollStatus, 15_000);
}

// -----------------------------------------------------------
//  Aggiorna i valori dei sensori nel DOM senza ricaricare.
// -----------------------------------------------------------
function updateSensorDisplay(reading) {
    const noData = document.getElementById('sensor_no_data');
    const grid   = document.getElementById('sensor_grid');
    const updEl  = document.getElementById('sensor_updated_at');

    if (noData) noData.classList.add('hidden');
    if (grid)   grid.classList.remove('hidden');
    if (updEl)  updEl.classList.remove('hidden');

    const pd = PLANT_DATA;

    function colorClass(val, min, max) {
        return (val === null || val === undefined || (val >= min && val <= max))
            ? 'text-success'
            : 'text-error';
    }

    const tempEl = document.getElementById('val_temp');
    if (tempEl && reading.temperature !== null && reading.temperature !== undefined) {
        tempEl.textContent = `${parseFloat(reading.temperature).toFixed(1)}°C`;
        tempEl.className   = `text-2xl font-bold ${colorClass(reading.temperature, pd.temp_min, pd.temp_max)}`;
    }

    const humEl = document.getElementById('val_hum');
    if (humEl && reading.humidity !== null && reading.humidity !== undefined) {
        humEl.textContent = `${Math.round(reading.humidity)}%`;
        humEl.className   = `text-2xl font-bold ${colorClass(reading.humidity, pd.hum_min, pd.hum_max)}`;
    }

    const soilEl = document.getElementById('val_soil');
    if (soilEl && reading.soil_humidity !== null && reading.soil_humidity !== undefined) {
        soilEl.textContent = `${Math.round(reading.soil_humidity)}%`;
        soilEl.className   = `text-2xl font-bold ${colorClass(reading.soil_humidity, pd.soil_hum_min, pd.soil_hum_max)}`;
    }

    const lumEl = document.getElementById('val_lum');
    if (lumEl && reading.luminosity !== null && reading.luminosity !== undefined) {
        lumEl.textContent = `${Math.round(reading.luminosity)} lx`;
        const luxColor = (pd.lux_min > 0)
            ? colorClass(reading.luminosity, pd.lux_min, pd.lux_max)
            : 'text-base-content';
        lumEl.className = `text-2xl font-bold ${luxColor}`;
    }

    if (updEl) {
        const ts = reading.recorded_at
            ? new Date(reading.recorded_at).toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
            : null;
        updEl.textContent = ts ? `Aggiornato alle ${ts}` : 'Aggiornato adesso';
        updEl.classList.remove('hidden');
    }

    if (window._appendChartReading) {
        window._appendChartReading({
            ...reading,
            recorded_at: reading.recorded_at ?? new Date().toISOString(),
        });
    }

    setPageDeviceStatus(true, new Date().toISOString());
    prependReadingRow(reading);
    showToast('📊 Sensori aggiornati', 'info');
}

// -----------------------------------------------------------
//  Inserisce una nuova lettura in cima a #latest_readings_list
// -----------------------------------------------------------
function prependReadingRow(reading) {
    let list = document.getElementById('latest_readings_list');

    if (!list) {
        const rightCol = document.querySelector('.lg\\:px-0.lg\\:pt-0');
        if (!rightCol) return;

        const section = document.createElement('div');
        section.innerHTML = `
            <p class="text-xs font-bold text-base-content/50 uppercase tracking-wider mb-2">Ultime letture</p>
            <div class="card bg-base-100 shadow">
                <ul id="latest_readings_list" class="divide-y divide-base-200"></ul>
            </div>
        `;
        rightCol.appendChild(section);
        list = document.getElementById('latest_readings_list');
    }

    const badges = [
        reading.temperature   !== null && reading.temperature   !== undefined ? `<span class="badge badge-ghost badge-sm">${parseFloat(reading.temperature).toFixed(1)}°C</span>`  : '',
        reading.humidity      !== null && reading.humidity      !== undefined ? `<span class="badge badge-ghost badge-sm">${Math.round(reading.humidity)}% aria</span>`             : '',
        reading.soil_humidity !== null && reading.soil_humidity !== undefined ? `<span class="badge badge-ghost badge-sm">${Math.round(reading.soil_humidity)}% suolo</span>`       : '',
        reading.luminosity    !== null && reading.luminosity    !== undefined ? `<span class="badge badge-ghost badge-sm">${Math.round(reading.luminosity)} lx</span>`              : '',
    ].join('');

    const li = document.createElement('li');
    li.className = 'flex items-center gap-3 px-4 py-3';
    li.innerHTML = `
        <span class="text-xs text-base-content/40 flex-shrink-0 w-20">adesso</span>
        <div class="flex gap-1 flex-wrap flex-1">${badges}</div>
    `;

    list.insertBefore(li, list.firstChild);

    while (list.children.length > 6) {
        list.removeChild(list.lastChild);
    }
}

// -----------------------------------------------------------
//  9. MUSICA
// -----------------------------------------------------------
function initMusic() {
    const modal = document.getElementById('modal_music');
    if (!modal) return;

    modal.addEventListener('close', async () => {
        const selected = modal.querySelector('input[name="music"]:checked');
        if (!selected) return;

        const source = parseInt(selected.value);
        await sendMusic(source);

        if (source === -1) {
            showToast('Musica disattivata 🔇', 'info');
        } else {
            const label = selected.closest('label')?.querySelector('span')?.textContent ?? `Traccia ${source}`;
            showToast(`Musica: ${label} 🎵`, 'success');
        }
    });
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
    PlantViewer?.capturePreview(PLANT_ID);
};

function initCharts() {
    if (!window.PLANT_READINGS?.length || typeof Chart === 'undefined') return;

    const readings = window.PLANT_READINGS;
    const pd       = PLANT_DATA;
    const labels   = readings.map(r => r.recorded_at);

    // Recupera un colore CSS custom property come stringa hex/oklch
    function css(v) {
        return getComputedStyle(document.documentElement).getPropertyValue(v).trim();
    }

    // Configurazioni per ogni metrica
    const METRICS = {
        temp: {
            data:     readings.map(r => r.temperature),
            label:    'Temperatura',
            unit:     '°C',
            color:    css('--color-primary'),
            min:      pd.temp_min,
            max:      pd.temp_max,
        },
        hum: {
            data:     readings.map(r => r.humidity),
            label:    'Umidità aria',
            unit:     '%',
            color:    css('--color-info'),
            min:      pd.hum_min,
            max:      pd.hum_max,
        },
        soil: {
            data:     readings.map(r => r.soil_humidity),
            label:    'Umidità suolo',
            unit:     '%',
            color:    css('--color-success'),
            min:      pd.soil_hum_min,
            max:      pd.soil_hum_max,
        },
        lum: {
            data:     readings.map(r => r.luminosity ?? null),
            label:    'Luminosità',
            unit:     ' lx',
            color:    css('--color-warning'),
            min:      null,
            max:      null,
        },
    };

    let activeKey = 'temp';

    function buildDatasets(key) {
        const m = METRICS[key];
        const datasets = [];

        // Linee tratteggiate per il range ottimale
        if (m.min !== null && m.max !== null) {
            datasets.push({
                label: '_max',
                data:  labels.map(() => m.max),
                borderColor: `color-mix(in srgb, ${m.color} 45%, transparent)`,
                borderWidth: 1.5,
                borderDash: [5, 4],
                pointRadius: 0,
                fill: false,
            });
            datasets.push({
                label: '_min',
                data:  labels.map(() => m.min),
                borderColor: `color-mix(in srgb, ${m.color} 45%, transparent)`,
                borderWidth: 1.5,
                borderDash: [5, 4],
                pointRadius: 0,
                fill: false,
            });
        }

        // Linea principale
        datasets.push({
            label:                m.label,
            data:                 m.data,
            borderColor:          m.color,
            borderWidth:          2,
            backgroundColor:      'transparent',
            pointRadius:          2,
            pointHoverRadius:     5,
            pointBackgroundColor: m.color,
            tension:              0.35,
            fill:                 false,
            spanGaps:             true,
        });

        return datasets;
    }

    function buildScales(key) {
        const m = METRICS[key];

        // Calcola il range reale dei dati
        const valid   = m.data.filter(v => v !== null && v !== undefined);
        const dataMin = valid.length ? Math.min(...valid) : (m.min ?? 0);
        const dataMax = valid.length ? Math.max(...valid) : (m.max ?? 100);

        // Includi anche i limiti ottimali nel range visibile, con padding
        const visMin  = Math.min(dataMin, m.min ?? dataMin);
        const visMax  = Math.max(dataMax, m.max ?? dataMax);
        const pad     = Math.max((visMax - visMin) * 0.25, 2);

        return {
            x: {
                grid:  { color: 'rgba(128,128,128,0.1)' },
                ticks: { maxTicksLimit: 7, font: { size: 10 } },
            },
            y: {
                grid:  { color: 'rgba(128,128,128,0.1)' },
                ticks: { font: { size: 10 }, callback: v => v + m.unit },
                min:   Math.floor(visMin - pad),
                max:   Math.ceil(visMax  + pad),
            },
        };
    }

    // Crea il chart iniziale
    const ctx   = document.getElementById('sensor-chart');
    const chart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: buildDatasets(activeKey) },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 250 },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: item => {
                            if (item.dataset.label?.startsWith('_')) return null;
                            return ` ${item.parsed.y}${METRICS[activeKey].unit}`;
                        },
                    },
                    filter: item => !item.dataset.label?.startsWith('_'),
                },
            },
            scales: buildScales(activeKey),
        },
    });

    // Cambio tab: swappa dataset e scala
    document.querySelectorAll('[data-chart]').forEach(tab => {
        tab.addEventListener('click', () => {
            const key = tab.dataset.chart;
            if (key === activeKey) return;
            activeKey = key;

            // Aggiorna tab attiva
            document.querySelectorAll('[data-chart]').forEach(t =>
                t.classList.toggle('tab-active', t === tab)
            );

            // Aggiorna chart
            chart.data.datasets = buildDatasets(key);
            chart.options.scales = buildScales(key);
            chart.update();
        });
    });

    // Aggiornamento live
    window._appendChartReading = function (reading) {
        const time = new Date(reading.recorded_at)
            .toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });

        // Aggiorna i dati in memoria per ogni metrica
        METRICS.temp.data.push(reading.temperature);
        METRICS.hum.data.push(reading.humidity);
        METRICS.soil.data.push(reading.soil_humidity);
        METRICS.lum.data.push(reading.luminosity ?? null);
        labels.push(time);

        // Mantieni max 50 punti
        if (labels.length > 50) {
            labels.shift();
            Object.values(METRICS).forEach(m => m.data.shift());
        }

        // Ridisegna solo se la tab è quella attiva
        chart.data.labels   = labels;
        chart.data.datasets = buildDatasets(activeKey);
        chart.update('none');
    };
}

// -----------------------------------------------------------
//  INIT
// -----------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    initWatering();
    initHistory();
    initDevice();
    initConditions();
    initNotes();
    initMusic();
    initAppearance();
    initEcho();
    initSensorPolling();
    initCharts();

    if (window.PLANT_HEALTH) {
        const health = calcHealth(window.PLANT_HEALTH);
        updateHealthBadge(health);
        PlantViewer?.setState(health.state);
    }
});
