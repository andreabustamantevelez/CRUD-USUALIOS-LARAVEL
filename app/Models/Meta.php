<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meta extends Model
{
    protected $table = 'metas';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    protected $casts = ['fecha_limite' => 'date'];

    public function creador(): BelongsTo { return $this->belongsTo(User::class, 'creador_id'); }
    public function participaciones(): HasMany { return $this->hasMany(ParticipacionMeta::class); }
    public function aportes(): HasMany { return $this->hasMany(AporteMeta::class); }

    // El avance se CALCULA (no se guarda). Privacidad: esto es el total conjunto, no el desglose.
    public function totalConjunto(): int { return (int) $this->aportes()->sum('monto'); }
}
