import { PlantViewer} from "../live2d/live2d-viewer.js";

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
//  Dati passati dal blade tramite window.*
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
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  csrf(),
            'X-Requested-With': 'XMLHttpRequest',
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

                // Aggiorna la UI del timer prossima annaffiata
                updateNextWateringDisplay(data.watered_at);
            } else {
                showToast('Errore nel salvataggio.', 'error');
            }
        } catch (e) {
            showToast('Errore di rete.', 'error');
        } finally {
            btnConfirm.disabled = false;
            btnConfirm.textContent = 'Sì, confermo';
        }
    });
}

function updateNextWateringDisplay(wateredAt) {
    // Ricalcola la prossima annaffiatura e aggiorna tutti i testi pertinenti
    // (semplice refresh della pagina per evitare ricalcoli Carbon lato client)
    setTimeout(() => window.location.reload(), 800);
}

// -----------------------------------------------------------
//  2. STORICO ANNAFFIATURE
// -----------------------------------------------------------
function initHistory() {
    const modal = document.getElementById('modal_history');
    if (!modal) return;

    modal.addEventListener('click', (e) => {
        // Carica quando il modal viene aperto tramite showModal
    });

    // Intercetta l'apertura del modal dallo show button
    document.querySelectorAll('[onclick*="modal_history"]').forEach(btn => {
        btn.addEventListener('click', loadHistory);
    });
}

async function loadHistory() {
    const list = document.getElementById('history_list');
    if (!list) return;

    list.innerHTML = '<li class="py-6 text-center text-sm text-base-content/50">Caricamento...</li>';

    try {
        const res  = await fetch(`/plants/${PLANT_ID}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await apiRequest(`/plants/${PLANT_ID}/history`);

        if (data && data.events && data.events.length > 0) {
            list.innerHTML = data.events.map(ev => `
                <li class="flex items-center gap-3 px-1 py-3">
                    <span class="text-lg">${ev.source === 'button' ? '🔘' : ev.source === 'scheduled' ? '⏰' : '🪣'}</span>
                    <div class="flex-1">
                        <p class="text-sm font-bold">${ev.source_label ?? ev.source}</p>
                        <p class="text-xs text-base-content/50">${ev.watered_at_human}</p>
                    </div>
                    <span class="text-xs text-base-content/40">${ev.watered_at_date}</span>
                </li>
            `).join('');
        } else {
            list.innerHTML = '<li class="py-6 text-center text-sm text-base-content/50">Nessuna annaffiatura registrata.</li>';
        }
    } catch (e) {
        // Fallback: mostra messaggio di errore
        list.innerHTML = '<li class="py-6 text-center text-sm text-error">Impossibile caricare lo storico.</li>';
    }
}

function initDevice() {
    const modal        = document.getElementById('modal_device');
    const input        = document.getElementById('device_token_input');
    const btnSave      = document.getElementById('btn_save_device');
    const btnUnlink    = document.getElementById('btn_unlink_device');
    const statusRow    = document.getElementById('device_status_row');
    const statusBadge  = document.getElementById('device_status_badge');
    const statusToken  = document.getElementById('device_status_token');

    if (!modal || !input) return;

    const currentToken = PLANT_DATA.device_token;
    if (currentToken) {
        input.value = currentToken;
        btnUnlink?.classList.remove('hidden');
        fetchDeviceStatus(currentToken, statusRow, statusBadge, statusToken);
    }

    // Salva token
    btnSave?.addEventListener('click', async () => {
        const token = input.value.trim();
        if (!token) {
            showToast('Inserisci un token valido.', 'warning');
            return;
        }

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
                updateDeviceUI(token, false);
            } else {
                showToast(data.message ?? 'Errore nel collegamento.', 'error');
            }
        } catch (e) {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });

    // Scollega dispositivo
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
                updateDeviceUI(null, false);
            }
        } catch (e) {
            showToast('Errore di rete.', 'error');
        }
    });

    // Aggiorna stato quando si apre il modal
    document.querySelectorAll('[onclick*="modal_device"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const tok = PLANT_DATA.device_token;
            if (tok) fetchDeviceStatus(tok, statusRow, statusBadge, statusToken);
        });
    });
}

async function fetchDeviceStatus(token, statusRow, statusBadge, statusToken) {
    if (!token || !statusRow) return;

    try {
        const data = await apiRequest(`/device/status?device_token=${token}`);
        statusRow.classList.remove('hidden');

        if (statusBadge) {
            statusBadge.className = `badge ${data.online ? 'badge-success' : 'badge-ghost'} gap-1`;
            statusBadge.textContent = data.online ? 'Online' : 'Offline';
        }
        if (statusToken) {
            statusToken.textContent = data.last_seen_at
                ? `Ultimo ping: ${new Date(data.last_seen_at).toLocaleString('it-IT')}`
                : 'Mai visto';
        }
    } catch (e) {
        // Silenzioso
    }
}

function updateDeviceUI(token, isOnline) {
    // Aggiorna il badge dispositivo nella griglia azioni
    const deviceCard = document.querySelector('[onclick*="modal_device"] .card-body div');
    if (!deviceCard) return;

    const statusSpan = deviceCard.querySelector('span:last-child');
    if (!statusSpan) return;

    if (token) {
        statusSpan.className = `text-xs ${isOnline ? 'text-success' : 'text-base-content/40'}`;
        statusSpan.textContent = isOnline ? 'Online' : 'Offline';
    } else {
        statusSpan.className = 'text-xs text-base-content/40';
        statusSpan.textContent = 'Non collegato';
    }
}

// -----------------------------------------------------------
//  4. CONDIZIONI OTTIMALI
// -----------------------------------------------------------
function initConditions() {
    const modal   = document.getElementById('modal_conditions');
    const btnSave = document.getElementById('btn_save_conditions');

    if (!modal || !btnSave) return;

    // Precompila i campi con i valori attuali quando si apre il modal
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

        // Validazione base
        if (payload.temp_max < payload.temp_min) {
            showToast('Temp. max deve essere ≥ min.', 'warning'); return;
        }
        if (payload.hum_max < payload.hum_min) {
            showToast('Umidità max deve essere ≥ min.', 'warning'); return;
        }
        if (payload.soil_hum_max < payload.soil_hum_min) {
            showToast('Suolo max deve essere ≥ min.', 'warning'); return;
        }
        if (!payload.watering_cycle || payload.watering_cycle < 1) {
            showToast('Ciclo annaffiatura non valido.', 'warning'); return;
        }

        btnSave.disabled = true;
        btnSave.textContent = 'Salvataggio...';

        try {
            const data = await apiRequest(`/plants/${PLANT_ID}`, 'PATCH', payload);

            if (data.status === 'ok') {
                // Aggiorna PLANT_DATA locale
                Object.assign(PLANT_DATA, payload);
                modal.close();
                showToast('Condizioni aggiornate! ✅', 'success');

                // Aggiorna i range labels in pagina (sezione stato sensori)
                updateConditionLabels(data.plant);
            } else {
                showToast('Errore nel salvataggio.', 'error');
            }
        } catch (e) {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });
}

function updateConditionLabels(plant) {
    if (!plant) return;

    // Aggiorna i range "Ottimale: X — Y" in pagina
    const labels = document.querySelectorAll('.text-xs.text-base-content\\/40');
    labels.forEach(el => {
        const text = el.textContent;
        if (text.includes('Ottimale:')) {
            if (text.includes('°C') && plant.temp_min != null) {
                el.textContent = `Ottimale: ${plant.temp_min} — ${plant.temp_max}°C`;
            } else if (text.includes('%') && !text.toLowerCase().includes('suol') && plant.hum_min != null) {
                el.textContent = `Ottimale: ${plant.hum_min} — ${plant.hum_max}%`;
            } else if (text.toLowerCase().includes('suol') && plant.soil_hum_min != null) {
                el.textContent = `Ottimale: ${plant.soil_hum_min} — ${plant.soil_hum_max}%`;
            }
        }
    });
}

// -----------------------------------------------------------
//  5. NOTE
// -----------------------------------------------------------
function initNotes() {
    const modal    = document.getElementById('modal_notes');
    const textarea = document.getElementById('notes_textarea');
    const btnSave  = document.getElementById('btn_save_notes');

    if (!modal || !textarea || !btnSave) return;

    // Precompila con le note attuali
    document.querySelectorAll('[onclick*="modal_notes"]').forEach(btn => {
        btn.addEventListener('click', () => {
            textarea.value = PLANT_DATA.notes ?? '';
        });
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

                // Aggiorna la preview note in pagina
                const preview = document.getElementById('notes_preview');
                if (preview) {
                    preview.textContent = notes
                        ? (notes.length > 50 ? notes.slice(0, 50) + '…' : notes)
                        : 'Nessuna nota';
                }
            } else {
                showToast('Errore nel salvataggio.', 'error');
            }
        } catch (e) {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });
}

// -----------------------------------------------------------
//  6. MODIFICA ASPETTO (modal_edit_plant)
// -----------------------------------------------------------
function initAppearance() {
    const btnSave = document.getElementById('btn_save_appearance');
    if (!btnSave) return;

    // Precompila i slider con i valori salvati
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
            if (PlantViewer) {
                PlantViewer.setAppearance({
                    plant_variant: parseInt(document.getElementById('range_variant')?.value     ?? 0),
                    pot_color:     parseInt(document.getElementById('range_pot')?.value         ?? 0),
                    plant_color:   parseInt(document.getElementById('range_plant_color')?.value ?? 0),
                    flower_color:  parseInt(document.getElementById('range_flower')?.value      ?? 0),
                });
            }
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
                // Aggiorna global state
                window.PLANT_APPEARANCE = { ...window.PLANT_APPEARANCE, ...appearance };

                // Applica al modello Live2D
                if (PlantViewer) {
                    PlantViewer.setAppearance(appearance);
                }

                document.getElementById('modal_edit_plant')?.close();
                showToast('Aspetto aggiornato! 🌸', 'success');
            } else {
                showToast('Errore nel salvataggio.', 'error');
            }
        } catch (e) {
            showToast('Errore di rete.', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Salva';
        }
    });
}

// -----------------------------------------------------------
//  7. REAL-TIME: ascolta ButtonPressed via Laravel Echo
// -----------------------------------------------------------
function initEcho() {
    if (!window.Echo || !PLANT_ID) return;

    window.Echo.channel(`plant.${PLANT_ID}`)
        .listen('.ButtonPressed', (e) => {
            showToast(e.message ?? '💧 Annaffiatura rilevata!', 'success');
            // Ricarica dopo un secondo per aggiornare il timer
            setTimeout(() => window.location.reload(), 1500);
        });
}

// -----------------------------------------------------------
//  8. POLLING STATO SENSORI (opzionale, ogni 30s)
// -----------------------------------------------------------
function initSensorPolling() {
    // Aggiorna lo stato del dispositivo ogni 30 secondi se è collegato
    if (!PLANT_DATA.has_device || !PLANT_DATA.device_token) return;

    setInterval(async () => {
        try {
            const data = await apiRequest(`/device/status?device_token=${PLANT_DATA.device_token}`);
            const isOnline = data.online;

            // Aggiorna tutti i badge online/offline in pagina
            document.querySelectorAll('[data-device-status]').forEach(el => {
                el.className = `w-2 h-2 rounded-full ${isOnline ? 'bg-success' : 'bg-base-300'} flex-shrink-0`;
            });
        } catch (e) { /* Silenzioso */ }
    }, 30_000);
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

// -----------------------------------------------------------
//  Espone applyAppearance per compatibilità con il blade
// -----------------------------------------------------------
window.applyAppearance = function () {
    const appearance = {
        plant_variant: parseInt(document.getElementById('range_variant')?.value     ?? 0),
        pot_color:     parseInt(document.getElementById('range_pot')?.value         ?? 0),
        plant_color:   parseInt(document.getElementById('range_plant_color')?.value ?? 0),
        flower_color:  parseInt(document.getElementById('range_flower')?.value      ?? 0),
    };
    if (PlantViewer) PlantViewer.setAppearance(appearance);
    document.getElementById('modal_edit_plant')?.close();
};

// -----------------------------------------------------------
//  INIT — avvia tutto al DOMContentLoaded
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

    // Imposta lo stato di salute Live2D se ci sono letture
    if (PlantViewer && window.PLANT_HEALTH) {
        PlantViewer.setHealth(window.PLANT_HEALTH);
    }
});
