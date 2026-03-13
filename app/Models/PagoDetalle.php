<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoDetalle extends Model
{
    protected $table = 'pago_detalles';

    protected $fillable = [
        'venta_id',
        'caja_id',
        'metodo_pago',
        'monto',
        'monto_recibido',
        'referencia_pago',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }
}

