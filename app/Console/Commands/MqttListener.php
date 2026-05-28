<?php

namespace App\Console\Commands;

use App\Events\ButtonPressed;
use App\Events\SensorUpdated;
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

        while (true) {
            try {
                $mqtt = MQTT::connection();

                $mqtt->subscribe('device/+/updates', function (string $topic, string $message) {
                    $token  = explode('/', $topic)[1];
                    $device = $this->findDevice($token);
                    if (!$device) return;

                    $data = json_decode($message, true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($data['type'])) {
                        $this->handleJson($data, $device);
                    } else {
                        $this->handlePlainMessage($message, $device);
                    }
                }, 1);

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

                $this->info('✅ Connesso al broker MQTT');
                $mqtt->loop(true);

            } catch (\Exception $e) {
                $this->warn('⚠️ Connessione persa: ' . $e->getMessage());
                $this->info('🔄 Riconnessione tra 5 secondi...');

                try {
                    MQTT::disconnect();
                } catch (\Exception) {}

                sleep(5);
            }
        }
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

    private function handleJson(array $data, Device $device): void
    {
        switch ($data['type']) {
            case 'sensor_data':
                $reading = SensorReading::create([
                    'plant_id'      => $device->plant_id,
                    'humidity'      => $data['humidity']      ?? null,
                    'temperature'   => $data['temperature']   ?? null,
                    'soil_humidity' => $data['soil_humidity']  ?? null,
                    'luminosity'    => $data['luminosity']     ?? null,
                ]);

                event(new SensorUpdated(
                    plantId:       $device->plant_id,
                    humidity:      $reading->humidity,
                    temperature:   $reading->temperature,
                    soil_humidity: $reading->soil_humidity,
                    luminosity:    $reading->luminosity,
                    recordedAt:    $reading->recorded_at->toDateTimeString(),
                ));

                $device->last_seen_at = now();
                $device->save();

                $this->info("[{$device->device_token}] 📊 Lettura salvata per pianta #{$device->plant_id}");
                break;

            default:
                $this->warn("[{$device->device_token}] Tipo JSON sconosciuto: {$data['type']}");
                break;
        }
    }

    private function handlePlainMessage(string $message, Device $device): void
    {
        switch ($message) {
            case 'BUTTON_PRESSED':
                WateringEvent::create([
                    'plant_id' => $device->plant_id,
                    'source'   => 'button',
                ]);
                event(new ButtonPressed($device->plant_id, "Pianta #{$device->plant_id} annaffiata! 💧"));
                $device->last_seen_at = now();
                $device->save();
                $this->info("[{$device->device_token}] 💧 Annaffiatura registrata per pianta #{$device->plant_id}");
                break;

            default:
                $this->warn("[{$device->device_token}] Messaggio sconosciuto: {$message}");
                break;
        }
    }
}
