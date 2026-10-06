<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * QUÉ HACE : valida el formulario de registro (POST /crear-usuario, ruta registro.guardar).
 * QUIÉN LO USA : RegistroController@store.
 * DEPENDE DE : tabla users (cedula y email únicos).
 * Los nombres de campo (nombre, identificacion, correo, ...) son los del formulario y NO se
 * cambian; el controlador los traduce a las columnas reales (identificacion -> cedula, correo -> email).
 * EJEMPLO válido: nombre "Ana Pérez", identificacion "1020304050", correo "ana@correo.com",
 *   password "Clave2026*" + confirmación igual, tipo_cuenta "estandar", consentimiento "1".
 * SEGURIDAD: tipo_cuenta solo admite estandar o familiar; "administrador" jamás se acepta aquí.
 */
class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre'         => trim((string) $this->input('nombre')),
            'identificacion' => trim((string) $this->input('identificacion')),
            'correo'         => mb_strtolower(trim((string) $this->input('correo'))),
            'tipo_cuenta'    => $this->input('tipo_cuenta', 'estandar'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre'         => ['required', 'string', 'max:100'],
            'identificacion' => ['required', 'regex:/^[0-9]{5,15}$/', 'unique:users,cedula'],
            'correo'         => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
            'tipo_cuenta'    => ['required', Rule::in(['estandar', 'familiar'])],
            'consentimiento' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'         => 'Escribe tu nombre completo.',
            'nombre.max'              => 'El nombre no puede tener más de 100 caracteres.',
            'identificacion.required' => 'Escribe tu número de identificación.',
            'identificacion.regex'    => 'La identificación debe tener solo números (entre 5 y 15 dígitos).',
            'identificacion.unique'   => 'Esta identificación ya se encuentra registrada.',
            'correo.required'         => 'Escribe tu correo electrónico.',
            'correo.email'            => 'Escribe un correo válido, por ejemplo andrea@ejemplo.com.',
            'correo.unique'           => 'Este correo ya se encuentra registrado',
            'password.required'       => 'Escribe una contraseña.',
            'password.min'            => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.max'            => 'La contraseña no puede tener más de 72 caracteres.',
            'password.confirmed'      => 'Las contraseñas no coinciden.',
            'tipo_cuenta.in'          => 'Selecciona un tipo de cuenta válido.',
            'tipo_cuenta.required'    => 'Selecciona un tipo de cuenta.',
            'consentimiento.accepted' => 'Debes aceptar el tratamiento de tus datos personales para registrarte.',
        ];
    }
}
