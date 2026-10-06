<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLA gastos  (diagrama: "gasto")
 * Dinero en pesos ENTEROS (unsignedBigInteger). Nunca decimal/float.
 * categoria: restrictOnDelete -> no se puede borrar una categoría que tenga gastos
 * (la pantalla pide mover los gastos primero, decisión #17).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();                                                     // id_gasto
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->unsignedBigInteger('monto');                              // monto_gasto (COP)
            $table->date('fecha');                                            // fecha_gasto
            $table->string('descripcion', 200)->nullable();                   // desc_gasto
            $table->timestamps();

            $table->index(['user_id', 'fecha']);   // acelera "gastos del mes de este usuario" (C1)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
