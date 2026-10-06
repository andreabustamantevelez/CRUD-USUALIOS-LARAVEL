<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;

/**
 * Quién puede usar, editar o borrar una categoría.
 *   - Propias (user_id = yo): ver, usar, editar y borrar.
 *   - Del sistema (user_id vacío): las VE y las USA cualquier persona de la zona privada, pero
 *     nadie las edita ni borra desde la zona del usuario (contrato C11).
 *   - Ajenas: nada.
 *   - Administrador: solo gestiona las del sistema (ver y editar); no ve las categorías de nadie;
 *     borrar queda en false hasta cerrar la decisión #23 (desactivar, no borrar).
 * 'usar' se pregunta al guardar un gasto o gasto fijo: la categoría elegida debe ser visible para
 * quien la elige (si no, alguien podría ligar su gasto a una categoría ajena mandando otro id).
 */
class CategoriaPolicy
{
    public function viewAny(User $usuario): bool
    {
        return true;
    }

    public function create(User $usuario): bool
    {
        return $usuario->rol !== 'administrador';
    }

    public function view(User $usuario, Categoria $categoria): bool
    {
        return $categoria->esDelSistema() || $this->esDueno($usuario, $categoria);
    }

    public function usar(User $usuario, Categoria $categoria): bool
    {
        return $usuario->rol !== 'administrador' && $this->view($usuario, $categoria);
    }

    public function update(User $usuario, Categoria $categoria): bool
    {
        if ($usuario->rol === 'administrador') {
            return $categoria->esDelSistema();
        }

        return $this->esDueno($usuario, $categoria);
    }

    public function delete(User $usuario, Categoria $categoria): bool
    {
        return $usuario->rol !== 'administrador' && $this->esDueno($usuario, $categoria);
    }

    private function esDueno(User $usuario, Categoria $categoria): bool
    {
        return $categoria->user_id !== null && $usuario->id === (int) $categoria->user_id;
    }
}
