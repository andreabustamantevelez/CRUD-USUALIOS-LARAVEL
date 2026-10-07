<?php

namespace App\Http\Requests\Metas;

use Illuminate\Foundation\Http\FormRequest;

/** Registrar un aporte: solo participantes y con la meta activa (MetaPolicy@aportar). Monto entero > 0. */
class StoreAporteMetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('aportar', $this->route('meta'));
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['monto' => preg_replace('/[\s$.,]/', '', (string) $this->input('monto'))]);
    }

    public function rules(): array
    {
        return [
            'monto' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'fecha' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'monto.required'        => 'Escribe el monto del aporte',
            'monto.integer'         => 'El monto debe ser un número entero en pesos',
            'monto.min'             => 'El monto debe ser mayor que cero',
            'monto.max'             => 'El monto es demasiado alto',
            'fecha.date'            => 'La fecha no es válida',
            'fecha.before_or_equal' => 'La fecha del aporte no puede ser futura',
        ];
    }
}
