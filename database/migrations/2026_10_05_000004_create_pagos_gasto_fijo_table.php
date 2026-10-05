<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLA pagos_gasto_fijo  (diagrama: "pago_gasto_fijo")
 * CORRECCIÓN CRÍTICA: faltaba la llave foránea al gasto fijo (decisión #9).
 * unique(gasto_fijo_id, periodo): un gasto fijo solo puede tener UN pago por mes.
 * periodo = texto 'AAAA-MM', por ejemplo '2026-10'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_gasto_fijo', function (Blueprint $table) {
            $table->id();                                                     // id_pago_fijo
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gasto_fijo_id')->constrained('gastos_fijos')->cascadeOnDelete(); // NUEVO
            $table->string('periodo', 7);                                     // periodo_gasto_fijo
            $table->date('fecha_vencimiento');                                // fecha_vencimiento_fijo
            $table->string('estado', 20)->default('pendiente');               // pendiente | pagado | vencido
            $table->date('fecha_pago')->nullable();                           // fecha_pago_fijo_real
            $table->unsignedBigInteger('monto');                              // NUEVO: monto de ese mes
            $table->timestamps();

            $table->unique(['gasto_fijo_id', 'periodo']);
            $table->index(['user_id', 'fecha_vencimiento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_gasto_fijo');
    }
};
