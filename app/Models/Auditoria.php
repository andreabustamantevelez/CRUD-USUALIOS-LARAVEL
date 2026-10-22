<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Auditoría de acciones del administrador (decisión #44).
 * Solo INSERT: no se actualiza ni se borra.
 */
class Auditoria extends Model
{
    protected $table = 'auditorias';

    protected $fillable = [
        'user_id', 'accion', 'modelo', 'modelo_id',
        'antes', 'despues', 'ip', 'user_agent',
    ];

    protected $casts = [
        'antes'    => 'array',
        'despues'  => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registra una acción. Solo INSERT, nunca UPDATE.
     */
    public static function registrar(
        string $accion,
        ?string $modelo = null,
        ?int $modeloId = null,
        ?array $antes = null,
        ?array $despues = null
    ): self {
        return self::create([
            'user_id'    => auth()->id(),
            'accion'     => $accion,
            'modelo'     => $modelo,
            'modelo_id'  => $modeloId,
            'antes'      => $antes,
            'despues'    => $despues,
            'ip'         => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
        ]);
    }
}