<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    protected $table = 'cajas';

    protected $fillable = [
        'sucursal_id',
        'usuario_id',
        'monto_inicial',
        'monto_final',
        'estatus',
        'fecha_apertura',
        'fecha_cierre',
    ];

    protected $casts = [
        'monto_inicial' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class, 'caja_id');
    }

    public function pagoDetalles(): HasMany
    {
        return $this->hasMany(PagoDetalle::class, 'caja_id');
    }
}
