<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = ['nombre', 'es_mostrador', 'activo'];

    protected $casts = [
        'es_mostrador' => 'boolean',
        'activo' => 'boolean',
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    public static function clienteMostrador(): ?self
    {
        return self::where('es_mostrador', true)->first();
    }

    protected static function booted(): void
    {
        static::deleting(function (Cliente $cliente) {
            if ($cliente->es_mostrador) {
                throw new \RuntimeException('No se puede eliminar el cliente mostrador.');
            }
        });
    }
}
