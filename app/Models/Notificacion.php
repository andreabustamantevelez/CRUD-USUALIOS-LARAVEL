<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notificacion extends Model
{
    use HasFactory, PerteneceAUsuario;

    protected $table = 'notificaciones';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function pagoGastoFijo(): BelongsTo { return $this->belongsTo(PagoGastoFijo::class); }
    public function meta(): BelongsTo { return $this->belongsTo(Meta::class); }
}
