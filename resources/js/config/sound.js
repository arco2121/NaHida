// ============================================================
//  NaHida — Sound Manager
//  Gestione centralizzata degli effetti sonori dell'app.
//  Rispetta la preferenza mute salvata in localStorage.
// ============================================================

const BASE = '/audios/';

const SOUNDS = {
    tap0:       'SFX_UI_Tap_0.mp3',
    tap1:       'SFX_UI_Tap_1.mp3',
    positive:   'SFX_UI_FEEDBACK_Positive.mp3',
    negative:   'SFX_UI_FEEDBACK_Negative.mp3',
    chime1:     'Chime%201.mp3',   // apertura modal / navigazione
    chime2:     'Chime%202.mp3',   // tap Live2D / eventi MQTT
    plantHappy: 'SFX_Plant_Happy.mp3',
    plantMid:   'SFX_Plant_Mid.mp3',
    plantSad:   'SFX_Plant_Sad.mp3',
};

const VOLUMES = {
    tap0:       0.35,
    tap1:       0.35,
    positive:   0.55,
    negative:   0.45,
    chime1:     0.45,
    chime2:     0.55,
    plantHappy: 0.65,
    plantMid:   0.50,
    plantSad:   0.50,
};

class SoundManager {
    constructor() {
        this._muted  = localStorage.getItem('Nahida_muted') === 'true';
        this._pool   = {};
        this._tapIdx = 0;
    }

    /** Lazy-init dell'oggetto Audio per evitare blocchi autoplay. */
    _get(name) {
        if (!this._pool[name]) {
            const a   = new Audio(BASE + SOUNDS[name]);
            a.volume  = VOLUMES[name] ?? 0.5;
            a.preload = 'auto';
            this._pool[name] = a;
        }
        return this._pool[name];
    }

    /** Riproduce un suono per nome-chiave. */
    play(name) {
        if (this._muted || !SOUNDS[name]) return;
        try {
            const a = this._get(name);
            a.currentTime = 0;
            a.play().catch(() => {}); // silenzio errori autoplay
        } catch {}
    }

    /** Alterna tap0 / tap1 ad ogni chiamata. */
    tap() {
        this.play(this._tapIdx++ % 2 === 0 ? 'tap0' : 'tap1');
    }

    get muted() { return this._muted; }

    setMuted(v) {
        this._muted = !!v;
        localStorage.setItem('Nahida_muted', this._muted);
    }

    /** Inverte lo stato mute e restituisce il nuovo valore. */
    toggle() {
        this.setMuted(!this._muted);
        return this._muted;
    }
}

export const Sound = new SoundManager();

// Esposto globalmente per uso inline nei blade se necessario
window.Sound = Sound;
