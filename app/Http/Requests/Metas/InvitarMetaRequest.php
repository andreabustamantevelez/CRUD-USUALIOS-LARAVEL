<?php

namespace App\Http\Requests\Metas;

use Illuminate\Foundation\Http\FormRequest;

/** Invitar a una meta existente. Solo la creadora (MetaPolicy@invitar), así un ajeno recibe 403 y no 422. */
class InvitarMetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('invitar', $this->route('meta'));
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['correo' => trim((string) $this->input('correo'))]);
    }

    public function rules(): array
    {
        return ['correo' => ['required', 'email', 'max:255']];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'Escribe el correo de la persona a invitar',
            'correo.email'    => 'Escribe un correo válido',
        ];
    }
}
