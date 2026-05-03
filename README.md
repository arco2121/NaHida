# 🌿 NaHida - Smart Plant Monitor

Progetto IoT per il monitoraggio intelligente delle piante, sviluppato da Colombara e Grammatica.

## Descrizione

NaHida è un sistema IoT composto da un dispositivo ESP32 con sensori ambientali e una Progressive Web App (PWA) che consente all'utente di monitorare in tempo reale lo stato delle proprie piante, ricevere notifiche sulle annaffiature e visualizzare i dati storici.

---

## Hardware Richiesto

- ESP32 con WiFi
- Capacitive Soil Moisture Sensor v1.2
- Sensore di umidità e temperatura
- Display OLED
- Bottone fisico per registrare le annaffiature
- *(Opzionale)* Sensore di luminosità
- *(Opzionale)* Speaker per avvisi

---

## Architettura

```
ESP32 (sensori + OLED)
        │
        │ MQTT (ogni minuto)
        ▼
    Server Backend
        │
        ├── Database (letture sensori, annaffiature)
        │
        └── PWA Frontend (dashboard, dettagli pianta, Live2D)
```

---

## Funzionalità

### Dispositivo ESP32
- Lettura periodica (ogni minuto) di umidità aria, temperatura e umidità del suolo
- Invio dati al server via MQTT
- Display OLED con nome della pianta e stato attuale (faccina)
- Bottone fisico da premere al momento dell'annaffiatura
- Ricezione delle condizioni ottimali dal server

### Web App (PWA)
- Registrazione e login utente
- Dashboard con panoramica delle piante e prossime annaffiature
- Aggiunta piante con condizioni ottimali da template o personalizzate
- Pagina dettagli pianta con:
    - Modello Live2D interattivo (espressioni in base allo stato, reazione al tocco, modalità notte)
    - Monitor sensori in tempo reale
    - Storico annaffiature e letture
    - Note personali
    - Collegamento/scollegamento dispositivo tramite token
- Impostazioni profilo e tema chiaro/scuro

---

## Stack Tecnologico

| Componente | Tecnologia |
|---|---|
| Firmware | C++ (ESP32 / Arduino) |
| Comunicazione | MQTT |
| Backend | Laravel (PHP) |
| Frontend | PWA + DaisyUI |
| Modello 2D | Live2D Cubism SDK |

---

## Installazione

> *Documentazione in corso di sviluppo.*


---
