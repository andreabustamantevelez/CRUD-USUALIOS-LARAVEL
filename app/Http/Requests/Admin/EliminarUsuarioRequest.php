<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Re-autenticación para eliminar un usuario.
 * Exige contraseña del admin actual (current_password).
 */
class EliminarUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'administrador';
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'current_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required'         => 'Ingresa tu contraseña para confirmar.',
            'password.current_password' => 'La contraseña no es correcta.',
        ];
    }
}