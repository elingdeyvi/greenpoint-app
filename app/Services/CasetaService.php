<?php

namespace App\Services;

use App\Models\Producto;

/**
 * Lógica de negocio de la caseta (vigilante) en sucursales tipo venta_almacen.
 * Restricción: en generación de QR local solo se permiten ciertos productos.
 * El escaneo/validación de QRs externos y la importación silenciosa no se restringen.
 */
class CasetaService
{
    /**
     * Nombres de productos permitidos para generar nuevo ticket/pedido desde caseta (QR local).
     * Solo aplica cuando la sucursal activa es tipo venta_almacen.
     */
    private const ALLOWED_PRODUCT_NAMES_FOR_LOCAL_QR = [
        'Polvo',
        'Rezaga',
        'Balastre',
    ];

    /**
     * Indica si el producto está permitido para generar un QR local (nuevo pedido en caseta).
     * No aplica al escaneo de QRs externos ni a validarYRegistrarAcceso.
     *
     * @param  \App\Models\Producto  $producto
     * @return bool
     */
    public function isProductAllowedForLocalQr(Producto $producto): bool
    {
        return in_array($producto->nombre, self::ALLOWED_PRODUCT_NAMES_FOR_LOCAL_QR, true);
    }

    /**
     * Nombres de productos permitidos (para mensajes o listados en front).
     *
     * @return array<string>
     */
    public function getAllowedProductNamesForLocalQr(): array
    {
        return self::ALLOWED_PRODUCT_NAMES_FOR_LOCAL_QR;
    }
}
