// ============================================================
//  NaHida — Sound UI Integration
//
//  Si aggancia all'app esistente tramite event delegation e
//  MutationObserver, senza dover riscrivere i file di pagina.
//
//  Mappatura suoni:
//  • Dock click              → tap (alterna tap0/tap1)
//  • Toast success           → positive
//  • Toast error/warning     → negative
//  • Toast info              → chime1
//  • Apertura modal          → chime1
//  • Tap canvas Live2D       → chime2
//  • Salute pianta: ottimale → plantHappy
//  • Salute pianta: warning  → plantMid
//  • Salute pianta: pessima  → plantSad
//  • Pulsante mute (#btn_toggle_sound) → toggle mute
// ============================================================

import { Sound } from './sound.js';

export function initSoundUI() {

    // ── 1. Dock navigation ───────────────────────────────────────────
    // Capture phase: garantisce che il suono parta prima della nav.
    document.addEventListener('click', (e) => {
        if (e.target.closest('.docke')) Sound.tap();
    }, true);

    // ── 2. Toast observer ────────────────────────────────────────────
    // Ascolta nuovi toast aggiunti in #toast-container e mappa il
    // tipo alert al suono corretto.
    const toastContainer = document.getElementById('toast-container');
    if (toastContainer) {
        new MutationObserver((mutations) => {
            for (const m of mutations) {
                for (const node of m.addedNodes) {
                    if (!(node instanceof HTMLElement)) continue;
                    const cl = node.classList;
                    if      (cl.contains('alert-success'))  { Sound.play('positive'); return; }
                    else if (cl.contains('alert-error'))    { Sound.play('negative'); return; }
                    else if (cl.contains('alert-warning'))  { Sound.play('negative'); return; }
                    else if (cl.contains('alert-info'))     { Sound.play('chime1');   return; }
                }
            }
        }).observe(toastContainer, { childList: true });
    }

    // ── 3. Apertura modal → chime ────────────────────────────────────
    // Intercetta qualsiasi elemento con onclick="... .showModal()"
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[onclick*="showModal"]');
        if (trigger) Sound.play('chime1');
    });

    // ── 4. Tap canvas Live2D → chime2 ────────────────────────────────
    const canvas = document.getElementById('live2d-canvas');
    if (canvas) {
        canvas.addEventListener('pointerdown', () => Sound.play('chime2'));
    }

    // ── 5. Salute pianta → suoni emotivi ─────────────────────────────
    // Osserva il badge #health_badge: quando il testo del label cambia
    // riproduce il suono corrispondente allo stato.
    // _initialized evita che il suono parta al primo render della pagina.
    const healthBadge = document.getElementById('health_badge');
    if (healthBadge) {
        let _prevLabel    = null;
        let _initialized  = false;

        setTimeout(() => {
            // Legge lo stato iniziale prima di abilitare l'observer
            _prevLabel   = healthBadge.querySelector('[data-health-label]')?.textContent ?? '';
            _initialized = true;
        }, 1500);

        new MutationObserver(() => {
            if (!_initialized) return;
            const label = healthBadge.querySelector('[data-health-label]')?.textContent ?? '';
            if (label === _prevLabel) return; // nessun cambiamento reale
            _prevLabel = label;

            if      (label.includes('ottimali'))   Sound.play('plantHappy');
            else if (label.includes('ttenzione'))  Sound.play('plantMid');
            else if (label.includes('pessime'))    Sound.play('plantSad');
        }).observe(healthBadge, { subtree: true, childList: true, characterData: true });
    }

    // ── 6. Bottone mute (opzionale) ──────────────────────────────────
    // Se nel DOM esiste un elemento con id="btn_toggle_sound", viene
    // usato come toggle mute. Vedi navbar modificata per l'esempio.
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#btn_toggle_sound')) return;
        const isMuted = Sound.toggle();
        _updateMuteBtn(isMuted);
    });

    // Stato iniziale del bottone mute (se presente)
    _updateMuteBtn(Sound.muted);
}

function _updateMuteBtn(muted) {
    const btn = document.getElementById('btn_toggle_sound');
    if (!btn) return;

    btn.title = muted ? 'Riattiva suoni' : 'Disattiva suoni';

    const icon = btn.querySelector('[data-sound-icon]');
    if (icon) icon.style.opacity = muted ? '0.35' : '1';

    // Aggiorna l'aria-label per accessibilità
    btn.setAttribute('aria-label', muted ? 'Suoni disattivati' : 'Suoni attivi');
}
