<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerfilRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $ingreso = $this->input('ingreso_mensual');

        if ($ingreso === null || trim((string) $ingreso) === '') {
            $this->merge(['ingreso_mensual' => null]);
        } elseif (
            is_string($ingreso)
            && preg_match('/^(?:\d+|\d{1,3}(?:[.\s]\d{3})+)$/', trim($ingreso))
        ) {
            $this->merge([
                'ingreso_mensual' => str_replace(['.', ' '], '', trim($ingreso)),
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->user();

        return [
            'nombre' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($usuario->id),
                Rule::unique('users', 'pending_email')->ignore($usuario->id),
            ],

            'ingreso_mensual' => ['nullable', 'integer', 'min:0', 'max:999999999999'],

            // La contraseña actual es obligatoria si:
            //   a) se va a cambiar la contraseña, o
            //   b) se va a cambiar el correo (protege ante sesión robada).
            'contrasena_actual' => [
                'nullable',
                Rule::requiredIf(function () use ($usuario) {
                    $cambiaPassword = !empty($this->input('nueva_contrasena'));
                    $cambiaEmail    = strtolower(trim((string) $this->input('email')))
                                      !== $usuario->email;
                    return $cambiaPassword || $cambiaEmail;
                }),
                'current_password',
            ],

            // NOTA: aquí NO va 'required_with:contrasena_actual'. Si el usuario
            // solo cambia el correo, envía contrasena_actual pero deja
            // nueva_contrasena vacía a propósito. La simetría al revés (si
            // viene nueva_contrasena, exigir contrasena_actual) ya la cubre
            // Rule::requiredIf del bloque anterior.
            'nueva_contrasena' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Ingresa un correo electrónico válido.',
            'email.unique'   => 'Este correo electrónico ya está registrado.',

            'ingreso_mensual.integer' => 'El ingreso mensual debe ser un número entero.',
            'ingreso_mensual.min'     => 'El ingreso mensual no puede ser negativo.',

            'contrasena_actual.required'         => 'Ingresa tu contraseña actual para cambiar el correo o la contraseña.',
            'contrasena_actual.current_password' => 'La contraseña actual no es correcta.',

            'nueva_contrasena.min'       => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'nueva_contrasena.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ];
    }
}