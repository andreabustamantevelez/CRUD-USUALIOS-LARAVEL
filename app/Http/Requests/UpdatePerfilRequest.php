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
            $this->merge([
                'ingreso_mensual' => null,
            ]);
        } elseif (
            is_string($ingreso)
            && preg_match(
                '/^(?:\d+|\d{1,3}(?:[.\s]\d{3})+)$/',
                trim($ingreso)
            )
        ) {
            $this->merge([
                'ingreso_mensual' => str_replace(
                    ['.', ' '],
                    '',
                    trim($ingreso)
                ),
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
            'nombre' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],

            'ingreso_mensual' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999999999',
            ],

            'contrasena_actual' => [
                'required_with:nueva_contrasena',
                'current_password',
            ],

            'nueva_contrasena' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'required_with:contrasena_actual',
            ],
        ];
    }

    public function messages(): array
{
    return [
        'nombre.required' => 'El nombre es obligatorio.',

        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Ingresa un correo electrónico válido.',
        'email.unique' => 'Este correo electrónico ya está registrado.',

        'ingreso_mensual.integer' => 'El ingreso mensual debe ser un número entero.',
        'ingreso_mensual.min' => 'El ingreso mensual no puede ser negativo.',

        'contrasena_actual.required_with' => 'Ingresa tu contraseña actual para cambiarla.',
        'contrasena_actual.current_password' => 'La contraseña actual no es correcta.',

        'nueva_contrasena.required_with' => 'Ingresa la nueva contraseña.',
        'nueva_contrasena.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
        'nueva_contrasena.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
    ];
}
}