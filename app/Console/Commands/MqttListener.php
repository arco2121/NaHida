<?php

namespace App\Console\Commands;

use App\Events\ButtonPressed;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\WateringEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;

class MqttListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Ascolta i messaggi MQTT in arrivo dagli ESP';

    public function handle(): void
    {
        $this->info('🌿 MQTT Listener avviato, in ascolto...');

        $mqtt = MQTT::connection();

        // Letture sensori e bottone
        $mqtt->subscribe('device/+/updates', function (string $topic, string $message) {
            $token  = explode('/', $topic)[1];
            $device = $this->findDevice($token);
            if (!$device) return;

            $device->touch(); // aggiorna last_seen_at

            $data = json_decode($message, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($data['type'])) {
                $this->handleJson($data, $device->plant_id, $token);
            } else {
                $this->handlePlainMessage($message, $device->plant_id, $token);
            }
        }, 1);

        // Ping online dall'ESP
        $mqtt->subscribe('device/+/status', function (string $topic, string $message) {
            $token  = explode('/', $topic)[1];
            $device = $this->findDevice($token);
            if (!$device) return;

            if ($message === 'ONLINE') {
                $device->last_seen_at = now();
                $device->save();
                $this->info("[{$token}] 🟢 Ping online ricevuto");
            }
        }, 0);

        $mqtt->loop(true);
    }

    private function findDevice(string $token): ?Device
    {
        $device = Device::where('device_token', $token)->first();

        if (!$device) {
            $this->warn("[{$token}] Dispositivo non trovato nel DB, messaggio ignorato.");
            Log::warning("MQTT: token sconosciuto '{$token}'");
        }

        return $device;
    }

    private function handleJson(array $data, int $plantId, string $token): void
    {
        switch ($data['type']) {
            case 'sensor_data':
                SensorReading::create([
                    'plant_id'      => $plantId,
                    'humidity'      => $data['humidity']      ?? null,
                    'temperature'   => $data['temperature']   ?? null,
                    'soil_humidity' => $data['soil_humidity']  ?? null,
                    'luminosity'    => $data['luminosity']    ?? null,
                ]);
                $this->info("[{$token}] 📊 Lettura salvata per pianta #{$plantId}");
                break;

            default:
                $this->warn("[{$token}] Tipo JSON sconosciuto: {$data['type']}");
                break;
        }
    }

    private function handlePlainMessage(string $message, int $plantId, string $token): void
    {
        switch ($message) {
            case 'BUTTON_PRESSED':
                WateringEvent::create([
                    'plant_id' => $plantId,
                    'source'   => 'button',
                ]);
                event(new ButtonPressed($plantId, "Pianta #{$plantId} annaffiata! 💧"));
                $this->info("[{$token}] 💧 Annaffiatura registrata per pianta #{$plantId}");
                break;

            default:
                $this->warn("[{$token}] Messaggio sconosciuto: {$message}");
                break;
        }
    }
}
