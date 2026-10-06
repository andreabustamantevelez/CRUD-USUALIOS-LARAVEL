<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParticipacionMeta extends Model
{
    use HasFactory;

    protected $table = 'participaciones_meta';

    protected $guarded = [];   // Los controladores usan FormRequest + $request->validated(), nunca $request->all().

    protected $casts = ['fecha_union' => 'date'];

    public function meta(): BelongsTo { return $this->belongsTo(Meta::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
