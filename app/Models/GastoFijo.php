<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GastoFijo extends Model
{
    use HasFactory, PerteneceAUsuario;

    protected $table = 'gastos_fijos';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    protected $casts = ['activo' => 'boolean', 'monto' => 'integer'];

    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function categoria(): BelongsTo { return $this->belongsTo(Categoria::class); }
    public function pagos(): HasMany { return $this->hasMany(PagoGastoFijo::class); }
}
