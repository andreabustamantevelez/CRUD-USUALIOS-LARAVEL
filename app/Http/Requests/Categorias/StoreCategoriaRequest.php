<?php

namespace App\Http\Requests\Categorias;

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
            'nombre' => trim($this->nombre),
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

                    $query = \App\Models\Categoria::where(function ($query) {
    $query->whereNull('user_id')
          ->orWhere('user_id', auth()->id());
})
->whereRaw(
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
                Rule::in(\App\Models\Categoria::BLOQUES),
            ],
        ];
    }
}   