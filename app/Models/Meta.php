<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * QUÉ ES : una meta de ahorro de un usuario familiar: INDIVIDUAL (solo él) o COMPARTIDA con otra persona (máx. 2).
 * PRIVACIDAD (RQNF-03): el avance es el TOTAL conjunto (suma en SQL). Nunca se cargan las filas de
 *   aportes del otro participante: para mostrar "tus aportes" se usa aportesDelUsuario($id).
 */
class Meta extends Model
{
    use HasFactory;

    protected $table = 'metas';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    protected $casts = ['fecha_limite' => 'date', 'compartida' => 'boolean'];

    public function creador(): BelongsTo { return $this->belongsTo(User::class, 'creador_id'); }
    public function participaciones(): HasMany { return $this->hasMany(ParticipacionMeta::class); }
    public function aportes(): HasMany { return $this->hasMany(AporteMeta::class); }

    /** Metas donde la persona es participante ACEPTADO (la creadora entra como aceptada al crear). */
    public function scopeDelParticipante(Builder $consulta, int $usuarioId): Builder
    {
        return $consulta->whereHas('participaciones', fn (Builder $q) => $q
            ->where('user_id', $usuarioId)
            ->where('estado_invitacion', 'aceptada'));
    }

    public function esParticipante(User $usuario): bool
    {
        return $this->participaciones()
            ->where('user_id', $usuario->id)
            ->where('estado_invitacion', 'aceptada')
            ->exists();
    }

    public function estaActiva(): bool { return $this->estado === 'activa'; }

    /** Compartida (con otra persona) o individual. Si el atributo no vino en la consulta, rige el default de la BD (true). */
    public function esCompartida(): bool { return (bool) ($this->getAttribute('compartida') ?? true); }

    // El avance se CALCULA (no se guarda). Privacidad: esto es el total conjunto, no el desglose.
    public function totalConjunto(): int
    {
        // Si la consulta ya trajo withSum('aportes', 'monto') se reutiliza; si no, se suma en SQL.
        return (int) ($this->aportes_sum_monto ?? $this->aportes()->sum('monto'));
    }

    /** Porcentaje de avance 0-100 (tope 100). Se calcula siempre, nunca se guarda. */
    public function porcentaje(): int
    {
        if ((int) $this->monto_objetivo <= 0) {
            return 0;
        }

        return (int) min(100, floor($this->totalConjunto() * 100 / $this->monto_objetivo));
    }

    public function objetivoAlcanzado(): bool
    {
        return $this->totalConjunto() >= (int) $this->monto_objetivo;
    }

    /**
     * CONTRATO C6 (dashboard e informes): consulta de los aportes de UNA persona, opcionalmente
     * dentro de un periodo. Nunca devuelve aportes de otra persona.
     * Ejemplo: $meta->aportesDelUsuario(auth()->id(), now()->startOfMonth(), now()->endOfMonth())->sum('monto');
     */
    public function aportesDelUsuario(int $usuarioId, ?CarbonInterface $desde = null, ?CarbonInterface $hasta = null)
    {
        return $this->aportes()
            ->where('user_id', $usuarioId)
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta));
    }
}
