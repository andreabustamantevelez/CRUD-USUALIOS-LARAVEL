<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    protected $casts = [
        'peso_recomendado' => 'integer',
        'activa'           => 'boolean',
    ];

    // C2: constante con los 3 bloques válidos del 50/30/20.
    public const BLOQUES = ['necesidades', 'discrecionales', 'ahorro'];

    // Catálogo de íconos disponibles para las categorías.
    // El valor (string) es la clave del SVG dentro de <x-categoria-icono />.
    // Si un usuario o el admin no eligen ninguno, el componente cae al fallback
    // por nombre (categorías del sistema) o por bloque.
    public const ICONOS = [
        'home'     => 'Casa / Arriendo',
        'bolt'     => 'Servicios / Luz',
        'cart'     => 'Mercado / Compras',
        'car'      => 'Transporte',
        'heart'    => 'Salud',
        'cap'      => 'Educación',
        'utensils' => 'Restaurantes',
        'film'     => 'Entretenimiento',
        'shirt'    => 'Ropa',
        'refresh'  => 'Suscripciones',
        'plane'    => 'Viajes',
        'piggy'    => 'Ahorro',
        'trending' => 'Inversión',
        'card'     => 'Tarjetas / Deudas',
        'sparkles' => 'Otros / Gustos',
        'tag'      => 'Etiqueta genérica',
    ];

    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function gastos(): HasMany { return $this->hasMany(Gasto::class); }
    public function gastosFijos(): HasMany { return $this->hasMany(GastoFijo::class); }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function esDelSistema(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Devuelve la clave del ícono que debe usar esta categoría.
     * Orden de prioridad:
     *   1. Ícono guardado en la BD (elegido por el usuario o por el admin) → ese ícono
     *   2. Categoría del sistema sin ícono explícito → su nombre mapeado (arriendo, mercado, etc.)
     *   3. Fallback por bloque → tag / sparkles / piggy
     *
     * Útil para casos donde solo tienes el modelo y no puedes llamar al componente.
     * El componente <x-categoria-icono> hace lo mismo, pero recibe el modelo directo.
     */
    public function iconoResuelto(): string
    {
        // 1º: ícono elegido explícitamente (por admin o por usuario).
        // Esto permite que el admin pueda sobreescribir el ícono por defecto de
        // una categoría del sistema (ej. cambiar "Mercado" de cart a piggy).
        if (!empty($this->icono) && array_key_exists($this->icono, self::ICONOS)) {
            return $this->icono;
        }

        // 2º: mapa por nombre (categorías del sistema con nombre conocido).
        if ($this->esDelSistema()) {
            $mapa = [
                'arriendo'             => 'home',
                'servicios públicos'   => 'bolt',
                'servicios publicos'   => 'bolt',
                'mercado'              => 'cart',
                'transporte'           => 'car',
                'salud'                => 'heart',
                'educación'            => 'cap',
                'educacion'            => 'cap',
                'restaurantes'         => 'utensils',
                'entretenimiento'      => 'film',
                'ropa'                 => 'shirt',
                'suscripciones'        => 'refresh',
                'viajes'               => 'plane',
                'ahorro'               => 'piggy',
                'inversión'            => 'trending',
                'inversion'            => 'trending',
                'pago extra de deudas' => 'card',
            ];

            $nombre = mb_strtolower(trim((string) $this->nombre));

            if (isset($mapa[$nombre])) {
                return $mapa[$nombre];
            }
        }

        // 3º: fallback por bloque 50/30/20.
        return match ($this->bloque) {
            'ahorro'         => 'piggy',
            'discrecionales' => 'sparkles',
            default          => 'tag',
        };
    }
}
