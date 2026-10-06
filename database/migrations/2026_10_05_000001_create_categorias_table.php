<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLA categorias  (diagrama: "categoria_gasto")
 * user_id VACÍO (null) = categoría del SISTEMA (decisión #10). Con valor = categoría propia.
 * 'bloque' es nuevo: decisión #12 (necesidades | discrecionales | ahorro). Contrato C2.
 * Nota: 'nombre único por usuario' se valida en la aplicación (StoreCategoriaRequest),
 * porque SQLite distingue mayúsculas en un índice único y trata null como distinto.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();                                              // id_categoria
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('nombre', 50);                              // nombre_categoria
            $table->string('bloque', 20);                              // necesidades | discrecionales | ahorro
            $table->unsignedTinyInteger('peso_recomendado')->nullable(); // 50 / 30 / 20 (se puede derivar del bloque)
            $table->timestamps();

            $table->index(['user_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
