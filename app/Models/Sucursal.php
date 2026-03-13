<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    public const TIPO_VENTA = 'venta';
    public const TIPO_VENTA_ALMACEN = 'venta_almacen';

    protected $fillable = [
        'nombre',
        'tipo_sucursal',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /** Sucursal solo ventas (ej. Villahermosa): genera ticket con QR. */
    public function isSucursalVenta(): bool
    {
        return $this->tipo_sucursal === self::TIPO_VENTA;
    }

    /** Sucursal venta y almacén (ej. Macuspana): recibe tickets, entregas, genera ticket seguimiento. */
    public function isSucursalVentaAlmacen(): bool
    {
        return $this->tipo_sucursal === self::TIPO_VENTA_ALMACEN;
    }

    /** @deprecated Use isSucursalVenta() */
    public function isVillahermosa(): bool
    {
        return $this->isSucursalVenta();
    }

    /** @deprecated Use isSucursalVentaAlmacen() */
    public function isMacuspana(): bool
    {
        return $this->isSucursalVentaAlmacen();
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'sucursal_id');
    }

    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class, 'sucursal_id');
    }
}
