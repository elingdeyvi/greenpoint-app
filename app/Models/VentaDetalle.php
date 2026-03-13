<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VentaDetalle extends Model
{
    protected $table = 'venta_detalles';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad_pedida',
        'cantidad_entregada',
    ];

    protected $casts = [
        'cantidad_pedida' => 'decimal:2',
        'cantidad_entregada' => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'venta_detalle_id');
    }

    /**
     * Indica si este detalle está totalmente entregado.
     */
    public function isEntregadoCompleto(): bool
    {
        return (float) $this->cantidad_entregada >= (float) $this->cantidad_pedida;
    }
}
