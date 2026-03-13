<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_unitario',
        'stock_actual',
        'stock_minimo',
        'unidad_medida',
        'unidad_medida_id',
        'activo',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Regla: No vender sin inventario.
     * Reduce el stock y lanza excepción si es insuficiente.
     *
     * @param  float|int  $cantidad
     * @return void
     * @throws \RuntimeException
     */
    public function reducirStock(float|int $cantidad): void
    {
        $cantidad = (float) $cantidad;
        if ($cantidad <= 0) {
            return;
        }
        if ($this->stock_actual < $cantidad) {
            throw new \RuntimeException(
                "Stock insuficiente para el producto \"{$this->nombre}\". Disponible: {$this->stock_actual} {$this->unidad_medida}, solicitado: {$cantidad}."
            );
        }
        $this->decrement('stock_actual', $cantidad);
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    /** Nombre de unidad para mostrar (catálogo o legacy string). */
    public function getUnidadNombreAttribute(): string
    {
        return $this->unidadMedida?->nombre ?? $this->unidad_medida ?? 'm3';
    }

    public function ventaDetalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class, 'producto_id');
    }
}
