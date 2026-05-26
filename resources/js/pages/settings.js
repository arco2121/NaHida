// ─── Toast ───────────────────────────────────────────────────────────────────
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

// ─── CSRF ────────────────────────────────────────────────────────────────────
function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

// ─── API request (JSON) ──────────────────────────────────────────────────────
async function apiRequest(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: {
            'Content-Type':     'application/json',
            'Accept':           'application/json',
            'X-CSRF-TOKEN':     csrf(),
            'X-Requested-With': 'XMLHttpRequest',
        },
    };
    if (body) opts.body = JSON.stringify(body);
    const res  = await fetch(url, opts);
    const data = await res.json().catch(() => ({}));
    return { ok: res.ok, status: res.status, data };
}

// ─── Checkmark SVG (bottone conferma) ───────────────────────────────────────
const CHECK_SVG = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none">
    <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>`;

// ─── Mostra / nascondi il pulsante di conferma per un campo ─────────────────
function showConfirmBtn(btn, visible) {
    btn.style.opacity       = visible ? '1' : '0';
    btn.style.pointerEvents = visible ? 'auto' : 'none';
}

// ─── SEZIONE PROFILO ─────────────────────────────────────────────────────────
function initProfileFields() {
    const fields = [
        { inputId: 'setting_first_name', btnId: 'btn_save_first_name' },
        { inputId: 'setting_last_name',  btnId: 'btn_save_last_name'  },
        { inputId: 'setting_email',      btnId: 'btn_save_email'      },
    ];

    fields.forEach(({ inputId, btnId }) => {
        const input = document.getElementById(inputId);
        const btn   = document.getElementById(btnId);
        if (!input || !btn) return;

        // Snapshot del valore originale dal server
        const original = input.value;

        input.addEventListener('input', () => {
            const changed = input.value.trim() !== original.trim();
            showConfirmBtn(btn, changed);

            // Avviso visivo se l'email cambia
            if (inputId === 'setting_email') {
                const hint = document.getElementById('setting_email_hint');
                if (hint) hint.classList.toggle('hidden', !changed);
            }
        });

        btn.addEventListener('click', () => saveProfile(btn));
    });
}

async function saveProfile(triggerBtn) {
    const firstName = document.getElementById('setting_first_name')?.value.trim() ?? '';
    const lastName  = document.getElementById('setting_last_name')?.value.trim()  ?? '';
    const email     = document.getElementById('setting_email')?.value.trim()       ?? '';

    // Validazione base lato client
    if (!firstName) { showToast('Il nome non può essere vuoto.', 'warning');    return; }
    if (!lastName)  { showToast('Il cognome non può essere vuoto.', 'warning'); return; }
    if (!email)     { showToast("L'email non può essere vuota.", 'warning');     return; }

    const originalHtml    = triggerBtn.innerHTML;
    triggerBtn.disabled   = true;
    triggerBtn.innerHTML  = '<span class="loading loading-spinner loading-xs"></span>';

    try {
        const { ok, data } = await apiRequest('/profile', 'PATCH', {
            first_name: firstName,
            last_name:  lastName,
            email:      email,
        });

        if (ok && data.status === 'ok') {
            showToast('Profilo aggiornato! ✅', 'success');

            // Nascondi tutti i bottoni di conferma e aggiorna i valori "originali"
            ['setting_first_name', 'setting_last_name', 'setting_email'].forEach(id => {
                const el  = document.getElementById(id);
                const btn = document.getElementById('btn_save_' + id.replace('setting_', ''));
                if (el)  el.defaultValue = el.value;  // aggiorna il riferimento "originale"
                if (btn) showConfirmBtn(btn, false);
            });

            // Nascondi l'avviso email
            document.getElementById('setting_email_hint')?.classList.add('hidden');

        } else {
            // Errori di validazione Laravel (es. email già usata, formato non valido…)
            const errors = data.errors ?? {};

            if (errors.first_name?.[0]) showToast(errors.first_name[0], 'error');
            else if (errors.last_name?.[0]) showToast(errors.last_name[0], 'error');
            else if (errors.email?.[0]) showToast(errors.email[0], 'error');
            else showToast(data.message ?? 'Errore nel salvataggio.', 'error');
        }
    } catch {
        showToast('Errore di rete. Riprova più tardi.', 'error');
    } finally {
        triggerBtn.disabled  = false;
        triggerBtn.innerHTML = originalHtml;
    }
}

// ─── SEZIONE PASSWORD ─────────────────────────────────────────────────────────
function initPassword() {
    const modal   = document.getElementById('modal_password');
    const btnSave = document.getElementById('btn_save_password');
    if (!modal || !btnSave) return;

    // Reset del form ogni volta che il modale si apre
    modal.addEventListener('show', resetPasswordForm);

    // Il tag <dialog> non emette 'show', ma possiamo intercettare la chiamata
    // tramite MutationObserver sull'attributo 'open'
    new MutationObserver(() => {
        if (modal.hasAttribute('open')) resetPasswordForm();
    }).observe(modal, { attributes: true, attributeFilter: ['open'] });

    btnSave.addEventListener('click', savePassword);
}

function resetPasswordForm() {
    ['pwd_current', 'pwd_new', 'pwd_confirm'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.value = ''; el.classList.remove('input-error'); }
    });
    ['pwd_current_error', 'pwd_new_error', 'pwd_confirm_error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.textContent = ''; el.classList.add('hidden'); }
    });
    const box = document.getElementById('pwd_error_box');
    if (box) box.classList.add('hidden');
}

function showPwdFieldError(fieldId, errorId, message) {
    const field = document.getElementById(fieldId);
    const err   = document.getElementById(errorId);
    if (field) field.classList.add('input-error');
    if (err)   { err.textContent = message; err.classList.remove('hidden'); }
}

async function savePassword() {
    const current = document.getElementById('pwd_current')?.value ?? '';
    const newPwd  = document.getElementById('pwd_new')?.value     ?? '';
    const confirm = document.getElementById('pwd_confirm')?.value  ?? '';

    // Reset errori precedenti
    resetPasswordForm();

    // Validazione lato client
    let hasError = false;
    if (!current) { showPwdFieldError('pwd_current', 'pwd_current_error', 'Inserisci la password attuale.'); hasError = true; }
    if (!newPwd)  { showPwdFieldError('pwd_new',     'pwd_new_error',     'Inserisci la nuova password.'); hasError = true; }
    if (!confirm) { showPwdFieldError('pwd_confirm', 'pwd_confirm_error', 'Conferma la nuova password.');  hasError = true; }
    if (newPwd && confirm && newPwd !== confirm) {
        showPwdFieldError('pwd_confirm', 'pwd_confirm_error', 'Le password non coincidono.');
        hasError = true;
    }
    if (newPwd && newPwd.length < 8) {
        showPwdFieldError('pwd_new', 'pwd_new_error', 'La password deve essere di almeno 8 caratteri.');
        hasError = true;
    }
    if (hasError) return;

    const btn            = document.getElementById('btn_save_password');
    const originalHtml   = btn.innerHTML;
    btn.disabled         = true;
    btn.innerHTML        = '<span class="loading loading-spinner loading-xs"></span> Salvataggio…';

    try {
        const { ok, data } = await apiRequest('/password', 'PUT', {
            current_password:      current,
            password:              newPwd,
            password_confirmation: confirm,
        });

        if (ok && data.status === 'ok') {
            document.getElementById('modal_password')?.close();
            showToast('Password aggiornata! 🔒', 'success');
        } else {
            // Gestione errori di validazione Laravel
            const errors = data.errors ?? {};

            if (errors.current_password?.[0]) {
                showPwdFieldError('pwd_current', 'pwd_current_error', errors.current_password[0]);
            } else if (errors.password?.[0]) {
                showPwdFieldError('pwd_new', 'pwd_new_error', errors.password[0]);
            } else {
                // Errore generico
                const box  = document.getElementById('pwd_error_box');
                const text = document.getElementById('pwd_error_text');
                if (box && text) {
                    text.textContent = data.message ?? 'Errore nel cambio password.';
                    box.classList.remove('hidden');
                }
            }
        }
    } catch {
        const box  = document.getElementById('pwd_error_box');
        const text = document.getElementById('pwd_error_text');
        if (box && text) {
            text.textContent = 'Errore di rete. Riprova più tardi.';
            box.classList.remove('hidden');
        }
    } finally {
        btn.disabled  = false;
        btn.innerHTML = originalHtml;
    }
}

// ─── Conferma eliminazione account ───────────────────────────────────────────
function initDeleteConfirm() {
    const input = document.getElementById('delete_confirm_input');
    const btn   = document.getElementById('btn_delete_final');
    if (!input || !btn) return;

    input.addEventListener('input', () => {
        btn.disabled = input.value.trim() !== 'ELIMINA';
    });
}

// ─── INIT ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initProfileFields();
    initPassword();
    initDeleteConfirm();
});
