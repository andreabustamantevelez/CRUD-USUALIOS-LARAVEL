<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    protected $casts = ['fecha' => 'date', 'monto' => 'integer'];

    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function categoria(): BelongsTo { return $this->belongsTo(Categoria::class); }

    // Ayuda para no olvidar el filtro por usuario: Gasto::delUsuario(auth()->id())->get()
    public function scopeDelUsuario($q, int $userId) { return $q->where('user_id', $userId); }
}
