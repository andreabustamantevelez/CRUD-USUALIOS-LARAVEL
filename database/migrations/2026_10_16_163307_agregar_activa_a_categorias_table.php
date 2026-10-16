<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cierra la decisión #23: las categorías del sistema se DESACTIVAN, no se borran.
 * Agrega categorias.activa (boolean, default true) e índice compuesto
 * con user_id para acelerar las consultas por "categorías activas del usuario
 * + del sistema".
 *
 * Ejemplo: las 14 categorías del sistema con activa=true se ven.
 *          Si el admin desactiva una, activa=false y deja de aparecer
 *          en los formularios de gastos (pero los gastos históricos siguen
 *          mostrándola en el detalle porque la relación no se rompe).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->boolean('activa')->default(true)->after('peso_recomendado');
            $table->index(['user_id', 'activa']);
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'activa']);
            $table->dropColumn('activa');
        });
    }
};