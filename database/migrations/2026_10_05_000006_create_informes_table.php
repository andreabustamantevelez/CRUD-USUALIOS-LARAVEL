<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLAS informes y detalles_informe  (diagrama: "informe_mensual" y "detalle_informe")
 * CORRECCIÓN CRÍTICA: la llave primaria era solo la fecha, y dos usuarios chocaban.
 * Ahora: id propio + unique(user_id, anio, mes).
 * El informe es una FOTO del mes: guarda el ingreso y el total de ese momento, y el detalle
 * guarda el nombre y el bloque de la categoría tal como estaban (así no cambia si luego
 * se renombra o elimina la categoría).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informes', function (Blueprint $table) {
            $table->id();                                                     // id_informe
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('anio');                             // 2026
            $table->unsignedTinyInteger('mes');                               // 1-12
            $table->unsignedBigInteger('ingreso');                            // NUEVO: ingreso de ese mes
            $table->unsignedBigInteger('total_gastado');                      // NUEVO
            $table->timestamps();                                             // created_at = fecha_informe

            $table->unique(['user_id', 'anio', 'mes']);
        });

        Schema::create('detalles_informe', function (Blueprint $table) {
            $table->id();                                                     // id_detalle
            $table->foreignId('informe_id')->constrained('informes')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('categoria_nombre', 50);                           // NUEVO (foto)
            $table->string('bloque', 20);                                     // NUEVO (foto)
            $table->unsignedBigInteger('monto_gastado');                      // monto_gastado_detalle
            $table->decimal('porcentaje_real', 5, 2);                         // porcentaje_real_detalle
            $table->unsignedTinyInteger('porcentaje_recomendado');            // porcentaje_recomendado_detalle
            $table->boolean('excede')->default(false);                        // excede_detalle

            $table->index('informe_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_informe');
        Schema::dropIfExists('informes');
    }
};
