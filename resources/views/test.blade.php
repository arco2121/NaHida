@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')

@section('content')
    <div style="max-width:600px; margin:40px auto; font-family:monospace; padding:0 16px;">

        <h2>🌿 NaHida — Pagina di Test</h2>
        <hr>

        {{-- TOKEN --}}
        <div style="margin-bottom:24px;">
            <label><b>Device Token</b></label><br>
            <input id="token" type="text" value="eccbc87e" style="width:100%; padding:6px; margin-top:4px;">
        </div>

        {{-- STATUS --}}
        <div style="margin-bottom:24px;">
            <h3>📡 Stato Dispositivo</h3>
            <button onclick="checkStatus()">Aggiorna Stato</button>
            <div id="status-box" style="margin-top:8px; padding:8px; border:1px solid #ccc;">
                In attesa...
            </div>
            <small style="opacity:.6">Si aggiorna automaticamente ogni 10 secondi</small>
        </div>

        {{-- SEND CONFIG --}}
        <div style="margin-bottom:24px;">
            <h3>⚙️ Invia Configurazione all'ESP</h3>
            <button onclick="sendConfig()">Invia Config</button>
            <div id="config-box" style="margin-top:8px; padding:8px; border:1px solid #ccc; white-space:pre-wrap;">
                —
            </div>
        </div>

        {{-- LED --}}
        <div style="margin-bottom:24px;">
            <h3>💡 Controllo LED</h3>
            <button onclick="toggleLed('ON')">Accendi</button>
            <button onclick="toggleLed('OFF')">Spegni</button>
            <div id="led-box" style="margin-top:8px; padding:8px; border:1px solid #ccc;">—</div>
        </div>

        {{-- LOG EVENTI REVERB --}}
        <div style="margin-bottom:24px;">
            <h3>🔔 Eventi in tempo reale (Reverb)</h3>
            <div id="event-log" style="padding:8px; border:1px solid #ccc; min-height:60px; max-height:200px; overflow-y:auto;">
                <span style="opacity:.5">In ascolto...</span>
            </div>
        </div>

    </div>

    <script>
        const csrfToken = '{{ csrf_token() }}';

        function getToken() {
            return document.getElementById('token').value.trim();
        }

        // ---- STATUS ----
        async function checkStatus() {
            const box = document.getElementById('status-box');
            try {
                const res  = await fetch('/device/status?device_token=' + getToken());
                const data = await res.json();
                const color = data.online ? 'green' : 'red';
                box.innerHTML = `<span style="color:${color}"><b>${data.online ? '🟢 ONLINE' : '🔴 OFFLINE'}</b></span><br>
                             Ultimo ping: ${data.last_seen_at ?? 'mai'}`;
            } catch (e) {
                box.innerText = 'Errore: ' + e.message;
            }
        }

        // ---- SEND CONFIG ----
        async function sendConfig() {
            const box = document.getElementById('config-box');
            box.innerText = 'Invio in corso...';
            try {
                const res  = await fetch('/device/send-config', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ device_token: getToken() })
                });
                const data = await res.json();
                box.innerText = JSON.stringify(data, null, 2);
            } catch (e) {
                box.innerText = 'Errore: ' + e.message;
            }
        }

        // ---- LED ----
        async function toggleLed(action) {
            const box = document.getElementById('led-box');
            try {
                const res  = await fetch('/device/toggle-led', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ action: action, device_token: getToken() })
                });
                const data = await res.json();
                box.innerText = data.status;
            } catch (e) {
                box.innerText = 'Errore: ' + e.message;
            }
        }

        // ---- REVERB / ECHO ----
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Echo) {
                document.getElementById('event-log').innerText = 'Laravel Echo non trovato. Avvia npm run dev.';
                return;
            }

            // Ascolta il canale della pianta con plant_id=3 (token eccbc87e → plant 3)
            // TODO: rendere dinamico quando c'è il FE completo
            window.Echo.channel('plant.3')
                .listen('.ButtonPressed', (e) => {
                    addLog(`💧 ${e.message}`);
                });
        });

        function addLog(msg) {
            const log  = document.getElementById('event-log');
            const time = new Date().toLocaleTimeString();
            log.innerHTML = `<div>[${time}] ${msg}</div>` + log.innerHTML;
        }

        // Auto-refresh stato ogni 10s
        checkStatus();
        setInterval(checkStatus, 10000);
    </script>
@endsection
