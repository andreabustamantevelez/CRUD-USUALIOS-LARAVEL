<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Informe extends Model
{
    protected $table = 'informes';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function detalles(): HasMany { return $this->hasMany(DetalleInforme::class); }
}
