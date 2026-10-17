<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\RestablecerContrasena;
use Illuminate\Notifications\Notifiable;

/**
 * Usuario de Cuentas Claras. REEMPLAZA al User.php que trae Laravel.
 * Roles: estandar | familiar | administrador
 *
 * Los campos pending_email, email_token y pending_email_expires_at NO están en
 * $fillable a propósito: se asignan a mano desde ServicioCuentas para que nadie
 * pueda mass-assign un token o un correo pendiente desde un formulario.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre',
        'cedula',
        'email',
        'password',
        'ingreso_mensual',
        'consentimiento_datos_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_token',
        'pending_email',
        'pending_email_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'password'                  => 'hashed',
            'activo'                    => 'boolean',
            'ingreso_mensual'           => 'integer',
            'consentimiento_datos_at'   => 'datetime',
            'pending_email_expires_at'  => 'datetime',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new RestablecerContrasena($token));
    }

    public function gastos(): HasMany { return $this->hasMany(Gasto::class); }
    public function categorias(): HasMany { return $this->hasMany(Categoria::class); }
    public function gastosFijos(): HasMany { return $this->hasMany(GastoFijo::class); }
    public function simulaciones(): HasMany { return $this->hasMany(SimulacionCredito::class); }
    public function informes(): HasMany { return $this->hasMany(Informe::class); }
    public function notificaciones(): HasMany { return $this->hasMany(Notificacion::class); }
}