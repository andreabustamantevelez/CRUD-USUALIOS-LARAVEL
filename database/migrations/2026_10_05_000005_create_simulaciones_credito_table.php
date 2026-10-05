<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLA simulaciones_credito  (diagrama: "credito")
 * Decisión #4: solo SIMULACIONES (no créditos reales). Por eso se quitó 'cupo_credito'
 * y se agregaron cuota, total e intereses (los calcula SIEMPRE el servidor, V1).
 * La fecha de la simulación es created_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulaciones_credito', function (Blueprint $table) {
            $table->id();                                                     // id_credito
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nombre', 100);                                    // nombre_credito
            $table->unsignedBigInteger('monto');                              // préstamo en COP
            $table->decimal('tasa_mensual', 5, 2);                            // tasa_interes_credito (% mensual)
            $table->unsignedSmallInteger('plazo_meses');                      // plazo_credito
            $table->unsignedBigInteger('cuota');                              // NUEVO
            $table->unsignedBigInteger('total_pagar');                        // NUEVO
            $table->unsignedBigInteger('intereses');                          // NUEVO
            $table->timestamps();                                             // created_at = fecha_simulacion_credito

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulaciones_credito');
    }
};
