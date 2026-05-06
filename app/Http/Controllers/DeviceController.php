<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Plant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;

class DeviceController extends Controller
{
    /**
     * Invia un comando ON/OFF al LED dell'ESP via MQTT
     */
    public function toggleLed(Request $request): JsonResponse
    {
        $request->validate([
            'action'       => 'required|in:ON,OFF',
            'device_token' => 'required|string|exists:devices,device_token',
        ]);

        $action = $request->input('action');
        $token  = $request->input('device_token');

        try {
            MQTT::connection()->publish("device/{$token}", $action, 0, false);
            Log::info("LED {$action} inviato al dispositivo {$token}");
            return response()->json(['status' => "LED {$action} inviato"]);
        } catch (\Exception $e) {
            Log::error("Errore MQTT toggleLed: " . $e->getMessage());
            return response()->json(['status' => 'Errore server'], 500);
        }
    }

    /**
     * Invia le condizioni ottimali della pianta all'ESP via MQTT.
     */
    public function sendConfig(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => 'required|string|exists:devices,device_token',
        ]);

        $token  = $request->input('device_token');
        $device = Device::where('device_token', $token)->firstOrFail();
        $plant  = Plant::findOrFail($device->plant_id);

        $payload = json_encode([
            'plant_name'   => $plant->plant_name,
            'hum_min'      => $plant->hum_min,
            'hum_max'      => $plant->hum_max,
            'temp_min'     => $plant->temp_min,
            'temp_max'     => $plant->temp_max,
            'soil_hum_min' => $plant->soil_hum_min,
            'soil_hum_max' => $plant->soil_hum_max,
        ]);

        try {
            MQTT::connection()->publish("device/{$token}/config", $payload, 0, true);
            Log::info("Config inviata a {$token}: {$payload}");
            return response()->json(['status' => 'Config inviata', 'payload' => json_decode($payload)]);
        } catch (\Exception $e) {
            Log::error("Errore MQTT sendConfig: " . $e->getMessage());
            return response()->json(['status' => 'Errore server'], 500);
        }
    }

    /**
     * Ritorna lo stato online/offline di un dispositivo.
     * Online = last_seen_at aggiornato negli ultimi 60 secondi.
     */
    public function getStatus(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => 'required|string|exists:devices,device_token',
        ]);

        $device   = Device::where('device_token', $request->input('device_token'))->firstOrFail();
        $isOnline = $device->last_seen_at && $device->last_seen_at->diffInSeconds(now()) < 60;

        return response()->json([
            'online'       => $isOnline,
            'last_seen_at' => $device->last_seen_at?->toDateTimeString(),
        ]);
    }
}
