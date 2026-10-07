<?php

namespace App\Services;

use App\Models\AporteMeta;
use App\Models\Meta;
use App\Models\Notificacion;
use App\Models\ParticipacionMeta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * QUÉ HACE : toda la lógica de las metas compartidas (los controladores solo llaman aquí).
 * REGLAS (decisiones J5):
 *   #7  fecha límite opcional; si se escribe debe ser hoy o futura (se valida en StoreMetaRequest).
 *   #8  los gastos NO se ligan a metas en la primera versión.
 *   #18 si alguien abandona, la meta se CANCELA para ambos; los aportes se conservan (historial).
 *       Si una cuenta se elimina, sus filas se borran en cascada (migración J2).
 *   - Máximo 2 participantes (aceptados + pendientes cuentan, para reservar el cupo).
 *   - El avance es la suma de aportes; el porcentaje se calcula, no se guarda.
 * CONTRATO C6 : tieneMetasActivas($usuario) para Valeria (bloquear el cambio a estándar).
 */
class ServicioMetas
{
    public const MAX_PARTICIPANTES = 2;

    // Mensajes que ve la persona. Revisar contra la sección de mensajes de la especificación.
    public const MSG_NO_EXISTE   = 'No existe un usuario registrado con ese correo';
    public const MSG_ESTANDAR    = 'Esa persona no es de tipo familiar y no puede participar en metas';
    public const MSG_YA_PARTICIPA = 'Esa persona ya participa en la meta';
    public const MSG_LLENA       = 'La meta ya tiene el máximo de 2 participantes';
    public const MSG_YO_MISMO    = 'No puedes invitarte a ti mismo';

    /** Crea la meta, deja a la creadora como participante aceptada e invita a la otra persona. */
    public function crear(User $creador, array $datos): Meta
    {
        return DB::transaction(function () use ($creador, $datos) {
            $meta = Meta::create([
                'creador_id'     => $creador->id,
                'nombre'         => $datos['nombre'],
                'monto_objetivo' => (int) $datos['monto_objetivo'],
                'fecha_limite'   => $datos['fecha_limite'] ?? null,
                'estado'         => 'activa',
            ]);

            ParticipacionMeta::create([
                'meta_id'           => $meta->id,
                'user_id'           => $creador->id,
                'estado_invitacion' => 'aceptada',
                'fecha_union'       => now()->toDateString(),
            ]);

            // Si la invitación no cumple las validaciones, lanza ValidationException y TODO se deshace.
            $this->invitar($meta, $datos['correo']);

            return $meta;
        });
    }

    /** Invita por correo. Valida (en este orden): existe, no soy yo, es familiar, ya participa, cupo. */
    public function invitar(Meta $meta, string $correo): ParticipacionMeta
    {
        return DB::transaction(function () use ($meta, $correo) {
            $meta = Meta::whereKey($meta->id)->lockForUpdate()->firstOrFail();

            $invitado = User::query()
                ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($correo))])
                ->where('activo', true)
                ->first();

            if (! $invitado || $invitado->rol === 'administrador') {
                $this->fallar(self::MSG_NO_EXISTE);
            }
            if ($invitado->id === (int) $meta->creador_id) {
                $this->fallar(self::MSG_YO_MISMO);
            }
            if ($invitado->rol !== 'familiar') {
                $this->fallar(self::MSG_ESTANDAR);
            }

            $existente = $meta->participaciones()->where('user_id', $invitado->id)->first();
            if ($existente && in_array($existente->estado_invitacion, ['pendiente', 'aceptada'], true)) {
                $this->fallar(self::MSG_YA_PARTICIPA);
            }

            $ocupados = $meta->participaciones()->whereIn('estado_invitacion', ['pendiente', 'aceptada'])->count();
            if ($ocupados >= self::MAX_PARTICIPANTES) {
                $this->fallar(self::MSG_LLENA);
            }

            // unique(meta_id, user_id): quien rechazó o abandonó antes se REACTIVA en la misma fila.
            $participacion = $existente ?? new ParticipacionMeta(['meta_id' => $meta->id, 'user_id' => $invitado->id]);
            $participacion->estado_invitacion = 'pendiente';
            $participacion->fecha_union = null;
            $participacion->save();

            $this->notificar($invitado->id, 'invitacion_meta',
                "{$meta->creador->nombre} te invitó a la meta \"{$meta->nombre}\"", $meta);

            return $participacion;
        });
    }

    public function aceptar(ParticipacionMeta $participacion): void
    {
        $meta = $participacion->meta;
        if (! $meta->estaActiva()) {
            $this->fallar('Esta meta ya no está activa', 'invitacion');
        }

        $participacion->update(['estado_invitacion' => 'aceptada', 'fecha_union' => now()->toDateString()]);

        $this->notificar($meta->creador_id, 'respuesta_meta',
            "{$participacion->usuario->nombre} aceptó tu invitación a \"{$meta->nombre}\"", $meta);
    }

    public function rechazar(ParticipacionMeta $participacion): void
    {
        $meta = $participacion->meta;
        $participacion->update(['estado_invitacion' => 'rechazada']);

        $this->notificar($meta->creador_id, 'respuesta_meta',
            "{$participacion->usuario->nombre} rechazó tu invitación a \"{$meta->nombre}\"", $meta);
    }

    public function aportar(Meta $meta, User $usuario, int $monto, ?string $fecha = null): AporteMeta
    {
        if (! $meta->estaActiva()) {
            $this->fallar('La meta no está activa y no recibe más aportes', 'monto');
        }
        if ($monto <= 0) {
            $this->fallar('El monto debe ser mayor que cero', 'monto');
        }

        return AporteMeta::create([
            'meta_id' => $meta->id,
            'user_id' => $usuario->id,
            'monto'   => $monto,
            'fecha'   => $fecha ?: now()->toDateString(),
        ]);
    }

    /** Avance conjunto: total, porcentaje (tope 100) y lo que falta. Sin desglose por persona. */
    public function avance(Meta $meta): array
    {
        $total = $meta->totalConjunto();

        return [
            'total'      => $total,
            'porcentaje' => $meta->porcentaje(),
            'falta'      => max(0, (int) $meta->monto_objetivo - $total),
        ];
    }

    /** Finaliza solo si se alcanzó el objetivo. */
    public function finalizar(Meta $meta, User $quien): void
    {
        if (! $meta->objetivoAlcanzado()) {
            $this->fallar('Aún no se alcanza el monto objetivo de la meta', 'meta');
        }

        $meta->update(['estado' => 'completada']);

        $this->avisarALosDemas($meta, $quien, 'meta_finalizada', 'finalizó la meta');
    }

    /** #18: quien abandona deja la meta cancelada para los dos; el historial de aportes se conserva. */
    public function abandonar(Meta $meta, User $quien): void
    {
        DB::transaction(function () use ($meta, $quien) {
            $meta->participaciones()->where('user_id', $quien->id)->update(['estado_invitacion' => 'abandonada']);
            // Invitaciones aún pendientes de esta meta ya no tienen sentido.
            $meta->participaciones()->where('estado_invitacion', 'pendiente')->update(['estado_invitacion' => 'rechazada']);
            $meta->update(['estado' => 'cancelada']);
        });

        $this->avisarALosDemas($meta, $quien, 'meta_abandonada', 'abandonó la meta');
    }

    /** CONTRATO C6 (Valeria): ¿participa la persona en alguna meta activa? */
    public function tieneMetasActivas(User $usuario): bool
    {
        return Meta::query()->where('estado', 'activa')->delParticipante($usuario->id)->exists();
    }

    // ------------------------------------------------------------------ internos

    private function avisarALosDemas(Meta $meta, User $quien, string $tipo, string $accion): void
    {
        $meta->participaciones()
            ->where('user_id', '!=', $quien->id)
            ->where('estado_invitacion', 'aceptada')
            ->pluck('user_id')
            ->each(fn ($id) => $this->notificar($id, $tipo, "{$quien->nombre} {$accion} \"{$meta->nombre}\"", $meta));
    }

    /** Versión mínima del servicio de notificaciones (C4). Cuando exista el de J6, se cambia aquí. */
    private function notificar(int $usuarioId, string $tipo, string $mensaje, Meta $meta): void
    {
        Notificacion::create([
            'user_id' => $usuarioId,
            'tipo'    => $tipo,
            'mensaje' => mb_substr($mensaje, 0, 255),
            'estado'  => 'pendiente',
            'meta_id' => $meta->id,
        ]);
    }

    private function fallar(string $mensaje, string $campo = 'correo'): never
    {
        throw ValidationException::withMessages([$campo => $mensaje]);
    }
}
