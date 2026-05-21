# Guida: Flusso Annaffiatura Bottone Fisico

## Cosa succede quando si preme il bottone

```
[Bottone ESP] → MQTT → [MqttListener] → DB + [Reverb] → [Browser]
```

Passo per passo:
1. L'utente preme il bottone fisico sull'ESP
2. L'ESP pubblica "BUTTON_PRESSED" su MQTT
3. Il MqttListener riceve il messaggio
4. Laravel salva l'evento in `watering_events`
5. Laravel spara l'evento ButtonPressed su Reverb
6. Il browser riceve l'evento via WebSocket e aggiorna la UI

---

## Parte 1 — ESP (C++)

### Dove avviene nel codice (`main.cpp`)

```cpp
void loop() {
    // ...

    // Bottone fisico (annaffiatura)
    static bool lastBtnState = HIGH;
    bool currentBtnState = digitalRead(BTN_PIN);

    if (lastBtnState == HIGH && currentBtnState == LOW) {
        client.publish(
            (String("device/") + DEVICE_TOKEN + "/updates").c_str(),
            "BUTTON_PRESSED"
        );
        Serial.println("BUTTON_PRESSED inviato");
        delay(200); // debounce: evita rimbalzi del bottone
    }
    lastBtnState = currentBtnState;
}
```

### Come funziona
- Il pin BTN_PIN è in `INPUT_PULLUP`: quando il bottone NON è premuto legge HIGH, quando è premuto legge LOW
- La condizione `lastBtnState == HIGH && currentBtnState == LOW` rileva il momento esatto in cui il bottone viene premuto (fronte di discesa), non mentre è tenuto premuto
- `delay(200)` è il debounce: i bottoni fisici "rimbalzano" elettricamente, 200ms evita letture multiple
- Il messaggio viene pubblicato sul topic `device/{DEVICE_TOKEN}/updates`

### Per testare senza ESP
Puoi simulare il bottone pubblicando manualmente da HiveMQ Cloud:
- Vai su `cloud.hivemq.com` → il tuo cluster → Web Client
- Pubblica su `device/eccbc87e/updates` il messaggio `BUTTON_PRESSED`

---

## Parte 2 — Laravel (MqttListener)

### Dove avviene nel codice (`MqttListener.php`)

```php
// Il listener è iscritto a device/+/updates
// Quando arriva un messaggio chiama handlePlainMessage()

private function handlePlainMessage(string $message, int $plantId, string $token): void
{
    switch ($message) {
        case 'BUTTON_PRESSED':
            // 1. Salva nel DB
            WateringEvent::create([
                'plant_id' => $plantId,
                'source'   => 'button',
            ]);

            // 2. Notifica il browser via Reverb
            event(new ButtonPressed($plantId, "Pianta #{$plantId} annaffiata! 💧"));

            $this->info("[{$token}] 💧 Annaffiatura registrata per pianta #{$plantId}");
            break;
    }
}
```

### Come funziona
- `WateringEvent::create()` inserisce una riga in `watering_events` con `plant_id`, `source=button` e `watered_at=now()` (automatico)
- `event(new ButtonPressed(...))` spara l'evento — Laravel lo passa a Reverb che lo manda via WebSocket a tutti i browser connessi sul canale `plant.{plantId}`

### Come verificare che funziona
Mentre `php artisan mqtt:listen` è attivo, guarda il terminale:
```
[eccbc87e] 💧 Annaffiatura registrata per pianta #3
```
E controlla il DB in phpMyAdmin: `watering_events` deve avere una nuova riga.

---

## Parte 3 — Reverb & WebSocket (Browser)

### Come funziona Reverb
Reverb è il server WebSocket di Laravel. Quando `event(new ButtonPressed(...))` viene sparato:
1. Laravel lo manda a Reverb (`php artisan reverb:start`)
2. Reverb lo trasmette via WebSocket a tutti i browser connessi sul canale giusto
3. Il browser lo riceve istantaneamente senza fare polling

### Dove avviene nel browser (`test.blade.php`)

```javascript
// Si connette al canale della pianta
window.Echo.channel('plant.3')
    .listen('.ButtonPressed', (e) => {
        // e.plantId  = ID della pianta
        // e.message  = testo del messaggio
        addLog(`💧 ${e.message}`);
    });
```

### Cose importanti
- `.listen('.ButtonPressed')` — il punto davanti è necessario quando usi `broadcastAs()` nel tuo evento
- `window.Echo` è disponibile perché `app.js` importa Laravel Echo configurato con Reverb
- Il canale è `plant.3` perché il dispositivo `eccbc87e` è collegato alla pianta con `plant_id=3`

### Per testare il WebSocket separatamente
Apri la console del browser sulla pagina di test e scrivi:
```javascript
window.Echo.channel('plant.3').listen('.ButtonPressed', (e) => console.log(e))
```
Poi premi il bottone fisico — deve apparire l'oggetto in console.

---

## Parte 4 — Checklist per domani

Prima di iniziare verifica che tutti e 4 i processi girino:

```powershell
# Terminale 1 — server Laravel
php artisan serve --host=127.0.0.1

# Terminale 2 — server WebSocket
php artisan reverb:start

# Terminale 3 — listener MQTT
php artisan mqtt:listen

# Terminale 4 — assets frontend
npm run dev
```

Poi vai su `http://127.0.0.1:8000/test` e premi il bottone fisico.
Dovresti vedere contemporaneamente:
- Nel terminale 3: `[eccbc87e] 💧 Annaffiatura registrata per pianta #3`
- Nella pagina web: l'evento nel log eventi in tempo reale

Se qualcosa non va, controlla in questo ordine:
1. **Terminale 3** — il listener ha ricevuto il messaggio?
2. **phpMyAdmin** — la riga è stata inserita in `watering_events`?
3. **Console browser** — ci sono errori WebSocket?
4. **`storage/logs/laravel.log`** — ci sono errori PHP?

---

## Riepilogo topic MQTT

| Topic | Chi pubblica | Chi ascolta | Contenuto |
|---|---|---|---|
| `device/{token}` | Laravel | ESP | Comandi: ON, OFF |
| `device/{token}/updates` | ESP | Laravel | BUTTON_PRESSED, sensor_data JSON |
| `device/{token}/config` | Laravel | ESP | JSON condizioni ottimali pianta |
| `device/{token}/status` | ESP | Laravel | Ping: ONLINE |
