<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * QUÉ HACE : valida el correo del formulario "Recuperar contraseña" (POST /recuperar-contrasena).
 * QUIÉN LO USA : PasswordResetController@enviarEnlace.
 * No comprueba que el correo exista (eso se calla a propósito: mensaje neutro).
 */
class SolicitarEnlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['correo' => mb_strtolower(trim((string) $this->input('correo')))]);
    }

    public function rules(): array
    {
        return ['correo' => ['required', 'email']];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'Escribe tu correo electrónico.',
            'correo.email'    => 'Escribe un correo válido.',
        ];
    }
}
