<?php

namespace App\Http\Requests\Categorias;

use App\Models\Categoria;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim((string) $this->nombre),
            // Normaliza el string vacío del "icono automático" a null.
            // Sin esto, '' puede causar líos con Rule::in.
            'icono' => filled($this->icono) ? $this->icono : null,
        ]);
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categoria')?->id;

        return [
            'nombre' => [
                'required',
                'string',
                'max:50',

                function (string $attribute, mixed $value, Closure $fail) use ($categoriaId) {
                    $nombre = mb_strtolower(trim($value));

                    $query = Categoria::where(function ($query) {
                        $query->whereNull('user_id')
                              ->orWhere('user_id', auth()->id());
                    })->whereRaw(
                        'LOWER(TRIM(nombre)) = ?',
                        [$nombre]
                    );

                    if ($categoriaId) {
                        $query->where('id', '!=', $categoriaId);
                    }

                    if ($query->exists()) {
                        $fail('Esta categoría ya se encuentra registrada');
                    }
                },
            ],

            'bloque' => [
                'required',
                Rule::in(Categoria::BLOQUES),
            ],

            // NUEVO: validar que el ícono elegido exista en el catálogo.
            // Es nullable porque el usuario puede dejar "ícono automático".
            'icono' => [
                'nullable',
                'string',
                Rule::in(array_keys(Categoria::ICONOS)),
            ],
        ];
    }
}