<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * J5: una meta puede ser INDIVIDUAL (solo la persona que la crea) o COMPARTIDA (con otra persona).
 * metas.compartida = true -> se puede invitar a una persona (máx. 2 participantes).
 * metas.compartida = false -> meta individual: no admite invitaciones.
 * Las metas que ya existían quedan como compartidas (default true), que era lo único que había.
 * Migración NUEVA: las migraciones ya unidas a main no se editan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            $table->boolean('compartida')->default(true)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            $table->dropColumn('compartida');
        });
    }
};
