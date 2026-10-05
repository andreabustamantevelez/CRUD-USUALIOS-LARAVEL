<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetalleInforme extends Model
{
    use HasFactory;

    protected $table = 'detalles_informe';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    public $timestamps = false;   // esta tabla no tiene created_at/updated_at
    protected $casts = ['excede' => 'boolean'];

    public function informe(): BelongsTo { return $this->belongsTo(Informe::class); }
}
