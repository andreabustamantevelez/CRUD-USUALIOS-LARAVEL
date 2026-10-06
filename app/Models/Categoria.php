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

    // C2: constante con los 3 bloques válidos del 50/30/20.
    public const BLOQUES = ['necesidades', 'discrecionales', 'ahorro'];

    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function gastos(): HasMany { return $this->hasMany(Gasto::class); }
    public function esDelSistema(): bool { return is_null($this->user_id); }
}
