<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de auditoría: registra acciones del administrador.
 * Regla #44: solo acciones del admin (no de usuarios finales, para no inflar).
 * No se actualiza ni se borra: solo INSERT. Inmutable por diseño.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            // FK sin cascade: si se elimina un admin, el histórico de sus acciones
            // sigue siendo relevante. Se pone null en ese caso.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('accion', 50);             // 'usuario.rol', 'categoria.desactivar'
            $table->string('modelo', 50)->nullable(); // 'User', 'Categoria'
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->json('antes')->nullable();        // estado previo
            $table->json('despues')->nullable();      // estado nuevo
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['modelo', 'modelo_id']);
            $table->index('accion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};