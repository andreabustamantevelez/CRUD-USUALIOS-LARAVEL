<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLA gastos_fijos  (diagrama: "gasto_fijo")
 * Nuevo: periodicidad (decisión #6, solo 'mensual') y activo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos_fijos', function (Blueprint $table) {
            $table->id();                                                     // id_gasto_fijo
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('nombre', 100);                                    // nombre_gasto_fijo
            $table->unsignedBigInteger('monto');                              // monto_fijo (COP)
            $table->unsignedTinyInteger('dia_vencimiento');                   // dia_pago_fijo (1-31)
            $table->string('periodicidad', 20)->default('mensual');           // NUEVO (#6)
            $table->string('descripcion', 100)->nullable();                   // desc_gasto_fijo
            $table->boolean('activo')->default(true);                         // NUEVO
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos_fijos');
    }
};
