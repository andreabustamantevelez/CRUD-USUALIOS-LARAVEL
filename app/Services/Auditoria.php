<?php

namespace App\Services;

use App\Models\Auditoria as ModeloAuditoria;

/**
 * Punto único de registro de auditoría.
 * Toda acción del admin que modifique estado DEBE pasar por aquí.
 */
class Auditoria
{
    public function registrar(
        string $accion,
        ?string $modelo = null,
        ?int $modeloId = null,
        ?array $antes = null,
        ?array $despues = null
    ): void {
        // Defensa por si se llama sin sesión (no debería pasar en admin).
        if (! auth()->check()) {
            return;
        }

        ModeloAuditoria::registrar($accion, $modelo, $modeloId, $antes, $despues);
    }
}