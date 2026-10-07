<?php

namespace App\Models;

use App\Models\Concerns\PerteneceAUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AporteMeta extends Model
{
    use HasFactory, PerteneceAUsuario;   // delUsuario($id): "solo mis aportes"

    protected $table = 'aportes_meta';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    protected $casts = ['fecha' => 'date', 'monto' => 'integer'];

    public function meta(): BelongsTo { return $this->belongsTo(Meta::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
