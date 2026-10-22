<?php

namespace App\Http\Requests\Admin;

use App\Models\Categoria;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Re-autenticación para activar/desactivar una categoría del sistema.
 * - authorize(): doble check (rol admin + categoría del sistema). Así el 403
 *   ocurre ANTES de la validación de contraseña.
 * - rules(): password actual obligatoria.
 */
class EliminarCategoriaSistemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user()?->rol !== 'administrador') {
            return false;
        }

        $categoria = $this->route('categoria');

        // Solo categorías del sistema (user_id = null).
        return $categoria instanceof Categoria && $categoria->user_id === null;
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