<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PagoGastoFijo extends Model
{
    use HasFactory, PerteneceAUsuario;

    protected $table = 'pagos_gasto_fijo';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    protected $casts = ['fecha_vencimiento' => 'date', 'fecha_pago' => 'date'];

    public function gastoFijo(): BelongsTo { return $this->belongsTo(GastoFijo::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
