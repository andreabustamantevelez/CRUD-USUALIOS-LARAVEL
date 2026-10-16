<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'administrador';
    }

    public function rules(): array
    {
        return [
            'activo' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'activo.required' => 'Selecciona un estado.',
            'activo.boolean' => 'El estado no es válido.',
        ];
    }
}