<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'administrador';
    }

    public function rules(): array
    {
        return [
            'rol' => ['required', 'in:estandar,familiar,administrador'],
        ];
    }

    public function messages(): array
    {
        return [
            'rol.required' => 'Selecciona un rol.',
            'rol.in' => 'Selecciona un rol válido.',
        ];
    }
}