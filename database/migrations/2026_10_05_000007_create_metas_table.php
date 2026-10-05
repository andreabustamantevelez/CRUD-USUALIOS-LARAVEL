<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLAS metas, participaciones_meta y aportes_meta
 * (diagrama: "meta_ahorro", "participacion_meta" y "ahorro_individual")
 *
 * CAMBIOS:
 *  - metas.creador_id (NUEVO): quién creó la meta; solo él invita.
 *  - metas.monto_actual_meta se ELIMINÓ: el avance se CALCULA sumando aportes_meta
 *    (nunca guardado, para que no se desincronice y para proteger la privacidad).
 *  - participaciones_meta ahora tiene llave primaria y unique(meta_id, user_id).
 *  - aportes_meta (antes ahorro_individual) ahora tiene llave primaria.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metas', function (Blueprint $table) {
            $table->id();                                                     // id_meta
            $table->foreignId('creador_id')->constrained('users')->cascadeOnDelete(); // NUEVO
            $table->string('nombre', 100);                                    // nombre_meta
            $table->unsignedBigInteger('monto_objetivo');                     // monto_objetivo_meta (COP)
            $table->date('fecha_limite')->nullable();                         // NUEVO (opcional)
            $table->string('estado', 20)->default('activa');                  // activa | completada | cancelada
            $table->timestamps();
        });

        Schema::create('participaciones_meta', function (Blueprint $table) {
            $table->id();                                                     // id_participacion
            $table->foreignId('meta_id')->constrained('metas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('estado_invitacion', 20)->default('pendiente');    // pendiente | aceptada | rechazada | abandonada
            $table->date('fecha_union')->nullable();                          // se llena al aceptar
            $table->timestamps();

            $table->unique(['meta_id', 'user_id']);                           // no invitar dos veces a la misma persona
        });

        Schema::create('aportes_meta', function (Blueprint $table) {
            $table->id();                                                     // id_ahorro
            $table->foreignId('meta_id')->constrained('metas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('monto');                              // monto_ahorro (COP)
            $table->date('fecha');                                            // fecha_ahorro
            $table->timestamps();

            $table->index(['meta_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aportes_meta');
        Schema::dropIfExists('participaciones_meta');
        Schema::dropIfExists('metas');
    }
};
