<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSimulacionRequest extends FormRequest
{
    /**
     * Valida los datos enviados para guardar una simulación de crédito.
     *
     * El controlador utiliza estos datos para calcular y guardar
     * la simulación asociada al usuario autenticado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para guardar una simulación.
     *
     * Los nombres corresponden a los campos del formulario:
     * nombre, monto, tasa y plazo.
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'monto' => ['required', 'integer', 'min:1'],
            'tasa' => ['required', 'numeric', 'min:0'],
            'plazo' => ['required', 'integer', 'min:2', 'max:60'],
        ];
    }
}
