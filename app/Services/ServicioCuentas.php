<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * QUÉ HACE : cambiar el tipo de cuenta (estandar ↔ familiar) del usuario autenticado.
 * CONTRATO C7 : lo llama AjustesController; Valeria solo arma la pantalla.
 * REGLAS :
 *   - estandar → familiar : siempre permitido.
 *   - familiar → estandar : bloqueado si el usuario tiene METAS COMPARTIDAS ACTIVAS (regla 11 del doc de arquitectura).
 *   - Mismo tipo : no hace nada.
 *   - 'rol' NO es fillable: se asigna a mano.
 */
class ServicioCuentas
{
    public function __construct(private ServicioMetas $metas)
    {
    }

    public function cambiarTipo(User $usuario, string $nuevoTipo): void
    {
        if (! in_array($nuevoTipo, ['estandar', 'familiar'], true)) {
            throw ValidationException::withMessages([
                'tipo_cuenta' => 'Selecciona un tipo de cuenta válido.',
            ]);
        }

        if ($usuario->rol === $nuevoTipo) {
            return;
        }

        if ($nuevoTipo === 'estandar' && $this->metas->tieneMetasCompartidasActivas($usuario)) {
            throw ValidationException::withMessages([
                'tipo_cuenta' => 'Debes finalizar o cancelar tus metas compartidas activas antes de cambiar a una cuenta estándar.',
            ]);
        }

        DB::transaction(function () use ($usuario, $nuevoTipo) {
            $usuario->rol = $nuevoTipo;
            $usuario->save();
        });
    }
}