<?php

namespace App\Http\Requests\Metas;

use App\Models\Meta;
use Illuminate\Foundation\Http\FormRequest;

/** Crear meta: nombre, monto objetivo > 0, fecha límite opcional (hoy o futura) y correo de la otra persona. */
class StoreMetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Meta::class);
    }

    protected function prepareForValidation(): void
    {
        // Acepta "$ 4.000.000" o "4,000,000": quita separadores pero NO el signo menos (un negativo debe fallar).
        $this->merge([
            'monto_objetivo' => preg_replace('/[\s$.,]/', '', (string) $this->input('monto_objetivo')),
            'correo'         => trim((string) $this->input('correo')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre'         => ['required', 'string', 'max:100'],
            'monto_objetivo' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'fecha_limite'   => ['nullable', 'date', 'after_or_equal:today'],
            'correo'         => ['required', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'              => 'Escribe el nombre de la meta',
            'nombre.max'                   => 'El nombre no puede superar 100 caracteres',
            'monto_objetivo.required'      => 'Escribe el monto objetivo',
            'monto_objetivo.integer'       => 'El monto objetivo debe ser un número entero en pesos',
            'monto_objetivo.min'           => 'El monto objetivo debe ser mayor que cero',
            'monto_objetivo.max'           => 'El monto objetivo es demasiado alto',
            'fecha_limite.date'            => 'La fecha límite no es válida',
            'fecha_limite.after_or_equal'  => 'La fecha límite no puede ser anterior a hoy',
            'correo.required'              => 'Escribe el correo de la persona a invitar',
            'correo.email'                 => 'Escribe un correo válido',
        ];
    }
}
