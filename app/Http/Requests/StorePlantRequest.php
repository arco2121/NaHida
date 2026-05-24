<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StorePlantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plant_name'    => ['required', 'string', 'max:100'],
            'notes'         => ['nullable', 'string', 'max:500'],

            // Condizioni ottimali
            'hum_min'       => ['required', 'numeric', 'min:0', 'max:100'],
            'hum_max'       => ['required', 'numeric', 'min:0', 'max:100', 'gte:hum_min'],
            'temp_min'      => ['required', 'numeric', 'min:-10', 'max:60'],
            'temp_max'      => ['required', 'numeric', 'min:-10', 'max:60', 'gte:temp_min'],
            'soil_hum_min'  => ['required', 'numeric', 'min:0', 'max:100'],
            'soil_hum_max'  => ['required', 'numeric', 'min:0', 'max:100', 'gte:soil_hum_min'],
            'watering_cycle'=> ['required', 'integer', 'min:1'],

            // Personalizzazione modello
            'plant_variant' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:7'],
            'plant_color'   => ['nullable', 'string', 'max:20'],
            'flower_color'  => ['nullable', 'string', 'max:20'],
            'pot_color'     => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'plant_name.required'     => 'Il nome della pianta è obbligatorio.',
            'hum_min.required'        => 'Inserisci l\'umidità minima.',
            'hum_max.required'        => 'Inserisci l\'umidità massima.',
            'hum_max.gte'             => 'L\'umidità massima deve essere ≥ al minimo.',
            'temp_min.required'       => 'Inserisci la temperatura minima.',
            'temp_max.required'       => 'Inserisci la temperatura massima.',
            'temp_max.gte'            => 'La temperatura massima deve essere ≥ al minimo.',
            'soil_hum_min.required'   => 'Inserisci l\'umidità del terreno minima.',
            'soil_hum_max.required'   => 'Inserisci l\'umidità del terreno massima.',
            'soil_hum_max.gte'        => 'L\'umidità del terreno massima deve essere ≥ al minimo.',
            'watering_cycle.required' => 'Specifica ogni quante ore annaffiare.',
            'watering_cycle.min'      => 'Il ciclo di annaffiatura deve essere di almeno 1 ora.',
        ];
    }
}
