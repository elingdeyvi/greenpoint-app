<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VigilanteQr extends Model
{
    protected $table = 'vigilante_qrs';

    protected $fillable = [
        'venta_id',
        'uuid',
        'sucursal_origen_id',
        'sucursal_local_id',
        'origen',
        'payload_original',
        'viajes_permitidos',
        'viajes_usados',
        'estatus',
    ];

    protected $casts = [
        'viajes_permitidos' => 'integer',
        'viajes_usados' => 'integer',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function sucursalOrigen(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_origen_id');
    }

    public function sucursalLocal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_local_id');
    }
}

