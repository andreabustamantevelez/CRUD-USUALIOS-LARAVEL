<?php

namespace App\Http\Requests\Admin;

use App\Models\Categoria;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaSistemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'administrador';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim((string) $this->nombre),
            // Normaliza el string vacío del "icono automático" a null.
            'icono' => filled($this->icono) ? $this->icono : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required', 'string', 'max:50',
                'unique:categorias,nombre,NULL,id,user_id,NULL',
            ],
            'bloque' => ['required', 'in:' . implode(',', Categoria::BLOQUES)],
            'peso_recomendado' => ['nullable', 'integer', 'min:0', 'max:100'],
            'icono' => [
                'nullable',
                'string',
                Rule::in(array_keys(Categoria::ICONOS)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Esta categoría ya se encuentra registrada.',
            'bloque.required' => 'Selecciona un bloque.',
            'bloque.in' => 'Selecciona un bloque válido.',
            'icono.in' => 'El icono seleccionado no es válido.',
        ];
    }
}
