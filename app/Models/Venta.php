<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'uuid',
        'folio',
        'sucursal_id',
        'usuario_id',
        'cliente_id',
        'total',
        'estatus',
        'tipo',
        'es_donativo',
        'observaciones',
        'ticket_origen_uuid',
        'qr_payload',
        'viajes_permitidos',
        'viajes_usados',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'es_donativo' => 'boolean',
        'viajes_permitidos' => 'integer',
        'viajes_usados' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Venta $venta) {
            if (empty($venta->uuid)) {
                $venta->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Genera el payload del QR: uuid:idSucursal|idProducto|cantidad|idUnidad|idProducto|cantidad|idUnidad|...
     * idUnidad = unidades_medida.id (número). Debe llamarse después de que los detalles estén cargados/guardados.
     */
    public function generarQrPayload(): string
    {
        $this->loadMissing('detalles.producto.unidadMedida');
        $uuid = $this->uuid ?? (string) Str::uuid();
        $this->uuid = $uuid;

        $cabecera = $uuid . ':' . $this->sucursal_id;
        $lineas = [];
        foreach ($this->detalles as $detalle) {
            $prod = $detalle->producto;
            $idUnidad = $prod->unidad_medida_id ?? 1;
            $lineas[] = $detalle->producto_id . '|' . $detalle->cantidad_pedida . '|' . $idUnidad;
        }
        $this->qr_payload = $cabecera . '|' . implode('|', $lineas);
        $this->saveQuietly();

        return $this->qr_payload;
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'venta_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoDetalle::class, 'venta_id');
    }

    /**
     * Comprueba si todos los detalles están totalmente entregados.
     */
    public function isEntregadoCompleto(): bool
    {
        return $this->detalles->every(fn (VentaDetalle $d) => $d->isEntregadoCompleto());
    }
}
