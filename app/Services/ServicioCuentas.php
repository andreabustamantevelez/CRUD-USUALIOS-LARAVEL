<?php

namespace App\Services;

use App\Models\Meta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * QUÉ HACE : cambiar el tipo de cuenta (estandar ↔ familiar) y eliminar la cuenta
 *            con manejo de metas compartidas activas. Contrato C7.
 * CONTRATO C7 : lo llama AjustesController (V4). Valeria solo arma la pantalla.
 * REGLAS :
 *   - estandar → familiar : siempre permitido.
 *   - familiar → estandar : bloqueado si el usuario tiene METAS COMPARTIDAS ACTIVAS.
 *   - Mismo tipo : no hace nada.
 *   - 'rol' NO es fillable: se asigna a mano.
 *   - eliminarCuenta: no se puede borrar al último administrador activo.
 *                     Si el usuario tiene metas compartidas activas, se cancelan y se
 *                     avisa al otro participante (decisión #18).
 */
class ServicioCuentas
{
    public function __construct(private ServicioMetas $metas)
    {
    }

    public function cambiarTipo(User $usuario, string $nuevoTipo): void
    {
        if (!in_array($nuevoTipo, ['estandar', 'familiar'], true)) {
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

    /**
     * Elimina la cuenta del usuario y todo lo suyo (borrado en cascada).
     *
     * Reglas:
     *  1. No se puede eliminar al último administrador activo del sistema.
     *  2. Las metas compartidas activas donde participa se cancelan. Al otro
     *     participante se le avisa con una notificación (decisión #18).
     *  3. Todo dentro de una transacción.
     */
    public function eliminarCuenta(User $usuario): void
    {
        $this->verificarUltimoAdministrador($usuario);

        DB::transaction(function () use ($usuario) {
            $this->cancelarMetasCompartidasActivas($usuario);

            $usuario->delete();
        });
    }

    /**
     * Impide que el sistema quede sin ningún administrador activo.
     */
    private function verificarUltimoAdministrador(User $usuario): void
    {
        if ($usuario->rol !== 'administrador' || !$usuario->activo) {
            return;
        }

        $otrosAdministradoresActivos = User::query()
            ->where('rol', 'administrador')
            ->where('activo', true)
            ->where('id', '!=', $usuario->id)
            ->exists();

        if (!$otrosAdministradoresActivos) {
            throw ValidationException::withMessages([
                'cuenta' => 'No puedes eliminar tu cuenta porque eres el único administrador activo del sistema.',
            ]);
        }
    }

    /**
     * Cancela las metas compartidas activas del usuario y avisa al otro
     * participante. La meta conserva su historial de aportes.
     */
    private function cancelarMetasCompartidasActivas(User $usuario): void
    {
        $notificaciones = app(ServicioNotificaciones::class);

        $metas = Meta::query()
            ->where('compartida', true)
            ->where('estado', 'activa')
            ->where(function ($consulta) use ($usuario) {
                $consulta
                    ->where('creador_id', $usuario->id)
                    ->orWhereHas('participaciones', function ($sub) use ($usuario) {
                        $sub->where('user_id', $usuario->id)
                            ->where('estado_invitacion', 'aceptada');
                    });
            })
            ->with(['participaciones'])
            ->get();

        foreach ($metas as $meta) {
            $otrosParticipantes = $meta->participaciones
                ->filter(
                    fn($p) =>
                        (int) $p->user_id !== (int) $usuario->id
                        && $p->estado_invitacion === 'aceptada'
                );

            // Si el usuario es el creador, la FK metas.creador_id tiene ON DELETE CASCADE:
            // hay que transferirla antes de borrar para que la meta sobreviva.
            if ((int) $meta->creador_id === (int) $usuario->id) {
                $nuevoCreador = $otrosParticipantes->first();

                if ($nuevoCreador === null) {
                    // Nadie más participa: la meta se borrará por cascada. No hay a quién avisar.
                    continue;
                }

                $meta->creador_id = $nuevoCreador->user_id;
            }

            $meta->estado = 'cancelada';
            $meta->save();

            foreach ($otrosParticipantes as $participacion) {
                $otro = User::find($participacion->user_id);

                if ($otro === null) {
                    continue;
                }

                $notificaciones->crear(
                    $otro,
                    ServicioNotificaciones::TIPO_META_ABANDONADA,
                    sprintf(
                        'La meta compartida "%s" se canceló porque el otro participante eliminó su cuenta.',
                        $meta->nombre
                    ),
                    [
                        'meta_id' => $meta->id,
                        'clave_unica' => 'meta_abandonada:' . $meta->id . ':' . $otro->id,
                    ]
                );
            }
        }
    }
}