<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * QUÉ HACE : valida el formulario de nueva contraseña (POST /restablecer-contrasena).
 * QUIÉN LO USA : PasswordResetController@actualizar.
 * Campos: token (oculto), correo, password, password_confirmation.
 */
class RestablecerContrasenaRequest extends FormRequest
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
        return [
            'token'    => ['required', 'string'],
            'correo'   => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'El enlace no es válido. Solicita uno nuevo.',
            'correo.required'    => 'Escribe tu correo electrónico.',
            'correo.email'       => 'Escribe un correo válido.',
            'password.required'  => 'Escribe tu nueva contraseña.',
            'password.min'       => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.max'       => 'La contraseña no puede tener más de 72 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
