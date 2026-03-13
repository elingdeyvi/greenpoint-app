<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    public $timestamps = false;

    protected $table = 'gastos';

    const UPDATED_AT = null;

    protected $fillable = [
        'caja_id',
        'descripcion',
        'monto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }
}
