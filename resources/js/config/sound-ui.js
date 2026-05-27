// ============================================================
//  NaHida — Sound UI Integration
//
//  Mappatura suoni:
//  • Dock click              → tap (alterna tap0/tap1)
//  • Qualsiasi .btn / <a>    → tap (delegation, cattura tutti inclusi futuri)
//  • Apertura modal          → tap
//  • Tap canvas Live2D       → suono emotivo (happy/mid/sad) in base allo stato
//  • Toast success           → positive
//  • Toast error/warning     → negative
//  • Toast info              → negative
//  • Bottone mute            → toggle mute
// ============================================================

import { Sound } from './sound.js';

export function initSoundUI() {

    // ── 1. Tap globale via delegation ────────────────────────────────
    // Cattura click su .btn, <a>, e qualsiasi elemento cliccabile
    // anche se aggiunto dinamicamente al DOM dopo il DOMContentLoaded.
    // Usa la capture phase per garantire che il suono parta prima.
    document.addEventListener('click', (e) => {
        // Escludi il bottone mute (gestito separatamente al punto 6)
        if (e.target.closest('#btn_toggle_sound')) return;

        const target = e.target;

        // Dock (suono tap)
        if (target.closest('.docke')) {
            Sound.tap();
            return;
        }

        // Bottoni e link generici
        if (target.closest('.btn, a, button')) {
            Sound.tap();
            return;
        }

        // Elementi con onclick che aprono modali
        if (target.closest('[onclick*="showModal"]')) {
            Sound.tap();
        }
    }, true);

    // ── 2. Toast observer ────────────────────────────────────────────
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
                    else if (cl.contains('alert-info'))     { Sound.play('negative'); return; }
                }
            }
        }).observe(toastContainer, { childList: true });
    }

    // ── 3. Tap canvas Live2D → suono emotivo della pianta ────────────
    // Il suono dipende dallo stato corrente della pianta al momento
    // del tap, non dal cambio del badge.
    const canvas = document.getElementById('live2d-canvas');
    if (canvas) {
        canvas.addEventListener('pointerdown', () => {
            // Legge lo stato salute corrente dal badge (già aggiornato da JS)
            const label = document.querySelector('[data-health-label]')?.textContent ?? '';

            if      (label.includes('ottimali'))  Sound.play('plantHappy');
            else if (label.includes('ttenzione')) Sound.play('plantMid');
            else if (label.includes('pessime'))   Sound.play('plantSad');
            else                                   Sound.play('plantHappy'); // fallback
        });
    }

    // ── 4. Bottone mute ──────────────────────────────────────────────
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#btn_toggle_sound')) return;
        const isMuted = Sound.toggle();
        _updateMuteBtn(isMuted);
    });

    // Stato iniziale del bottone mute
    _updateMuteBtn(Sound.muted);
}

function _updateMuteBtn(muted) {
    const btn = document.getElementById('btn_toggle_sound');
    if (!btn) return;

    btn.title = muted ? 'Riattiva suoni' : 'Disattiva suoni';

    const icon = btn.querySelector('[data-sound-icon]');
    if (icon) icon.style.opacity = muted ? '0.35' : '1';

    btn.setAttribute('aria-label', muted ? 'Suoni disattivati' : 'Suoni attivi');
}
