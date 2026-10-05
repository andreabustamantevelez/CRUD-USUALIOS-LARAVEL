<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function pagoGastoFijo(): BelongsTo { return $this->belongsTo(PagoGastoFijo::class); }
    public function meta(): BelongsTo { return $this->belongsTo(Meta::class); }
}
