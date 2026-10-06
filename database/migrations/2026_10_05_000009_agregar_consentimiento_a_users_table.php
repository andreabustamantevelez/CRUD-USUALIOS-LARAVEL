<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * El registro (J3) exige aceptar el tratamiento de datos y GUARDAR LA FECHA en que se aceptó.
 * Faltó en la primera migración de users. Como esa ya está en main, NO se edita:
 * se agrega esta migración nueva (regla del equipo: lo que ya está en main no se modifica).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('consentimiento_datos_at')->nullable();   // fecha de aceptación; null = nunca aceptó
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('consentimiento_datos_at');
        });
    }
};
