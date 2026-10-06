<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Usuario de Cuentas Claras. REEMPLAZA al User.php que trae Laravel.
 * Roles: estandar | familiar | administrador
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Campos que se pueden llenar con create()/update(). 'rol' y 'activo' NO van aquí a propósito:
    // así nadie puede ponerse administrador mandando un campo extra en un formulario (J3/J4).
    protected $fillable = ['nombre', 'cedula', 'email', 'password', 'ingreso_mensual', 'consentimiento_datos_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'        => 'hashed',   // Laravel aplica Hash automáticamente al guardar
            'activo'          => 'boolean',
            'ingreso_mensual' => 'integer',
            'consentimiento_datos_at' => 'datetime',
        ];
    }

    public function gastos(): HasMany { return $this->hasMany(Gasto::class); }
    public function categorias(): HasMany { return $this->hasMany(Categoria::class); }
    public function gastosFijos(): HasMany { return $this->hasMany(GastoFijo::class); }
    public function simulaciones(): HasMany { return $this->hasMany(SimulacionCredito::class); }
    public function informes(): HasMany { return $this->hasMany(Informe::class); }
    public function notificaciones(): HasMany { return $this->hasMany(Notificacion::class); }
}
