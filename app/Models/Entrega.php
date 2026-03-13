<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrega extends Model
{
    public $timestamps = false;

    protected $table = 'entregas';

    const UPDATED_AT = null;

    protected $fillable = [
        'venta_id',
        'venta_detalle_id',
        'usuario_id',
        'cantidad_despachada',
        'foto_ruta',
        'foto_path',
        'uuid_qr',
        'numero_viaje',
    ];

    protected $casts = [
        'cantidad_despachada' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::created(function (Entrega $entrega) {
            $entrega->actualizarVentaDetalleYEstatus();
        });
    }

    /**
     * Al registrar una entrega parcial: actualiza cantidad_entregada en venta_detalles.
     * Si la suma iguala a la pedida en todos los detalles, cambia estatus de Venta a 'entregado'.
     */
    protected function actualizarVentaDetalleYEstatus(): void
    {
        if (! $this->venta_detalle_id) {
            return;
        }

        $detalle = VentaDetalle::find($this->venta_detalle_id);
        if (! $detalle) {
            return;
        }

        $nuevaEntregada = (float) $detalle->cantidad_entregada + (float) $this->cantidad_despachada;
        $detalle->cantidad_entregada = min($nuevaEntregada, (float) $detalle->cantidad_pedida);
        $detalle->saveQuietly();

        $venta = Venta::with('detalles')->find($this->venta_id);
        if (! $venta) {
            return;
        }

        $todosEntregados = $venta->detalles->every(fn (VentaDetalle $d) => (float) $d->cantidad_entregada >= (float) $d->cantidad_pedida);
        if ($todosEntregados) {
            $venta->estatus = 'entregado';
            $venta->saveQuietly();
        } else {
            $venta->estatus = 'parcial';
            $venta->saveQuietly();
        }
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function ventaDetalle(): BelongsTo
    {
        return $this->belongsTo(VentaDetalle::class, 'venta_detalle_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
