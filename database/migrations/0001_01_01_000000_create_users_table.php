<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * TABLA users  (en el diagrama original: "usuario")
 * ------------------------------------------------------------------
 * REEMPLAZA a la migración que trae Laravel por defecto con el mismo nombre.
 * Se conserva el nombre 'users', 'email' y 'password' porque el sistema de
 * login de Laravel (J3) los espera así. En las pantallas se sigue mostrando
 * "Correo" y "Contraseña".
 * También crea password_reset_tokens (recuperar contraseña) y sessions
 * (porque el .env usa SESSION_DRIVER=database).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                   // id_usuario
            $table->string('nombre', 100);                  // nombre_usuario
            $table->string('cedula', 20)->unique();         // decisión #1 (faltaba en el diagrama)
            $table->string('email', 150)->unique();         // correo_usuario (único)
            $table->string('password');                     // usuario_contraseña (guarda el HASH)
            $table->string('rol', 20)->default('estandar'); // estandar | familiar | administrador
            $table->boolean('activo')->default(true);       // estado_usuario (activo / inactivo)
            $table->unsignedBigInteger('ingreso_mensual')->nullable(); // COP enteros; vacío = no registrado (C3)
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
