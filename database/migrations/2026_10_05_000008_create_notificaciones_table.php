<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLA notificaciones  (diagrama: "notificacion")
 * CAMBIOS:
 *  - pago_gasto_fijo_id y meta_id son OPCIONALES (null): una notificación apunta a lo que corresponda.
 *  - Se quitó la referencia a cuota_credito (tabla eliminada, decisión #4).
 *  - clave_unica: texto como 'por_vencer:12:2026-10' para que el comando diario (J6) no duplique alertas.
 *  - estado: pendiente | leida | cancelada.   fecha de generación = created_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();                                                     // id_notificacion
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo', 30);                                       // por_vencer | vencido | invitacion_meta | ...
            $table->string('mensaje', 255);                                   // mensaje_notificacion
            $table->string('estado', 20)->default('pendiente');               // estado_notificacion
            $table->foreignId('pago_gasto_fijo_id')->nullable()->constrained('pagos_gasto_fijo')->cascadeOnDelete();
            $table->foreignId('meta_id')->nullable()->constrained('metas')->cascadeOnDelete();
            $table->string('clave_unica')->nullable();                        // NUEVO (idempotencia)
            $table->timestamps();

            $table->unique(['user_id', 'clave_unica']);
            $table->index(['user_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
