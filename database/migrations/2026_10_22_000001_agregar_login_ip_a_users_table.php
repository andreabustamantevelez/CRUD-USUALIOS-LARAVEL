<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade last_login_ip y last_login_at a users.
 * Sirve para detectar "login desde IP nueva" (punto 2 de J9).
 * Se asignan programáticamente con forceFill, no son fillable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_login_ip', 45)->nullable()->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('last_login_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_login_ip', 'last_login_at']);
        });
    }
};