<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Servicio $servicio) {
            if (empty($servicio->slug) && !empty($servicio->nombre)) {
                $servicio->slug = Str::slug($servicio->nombre);
            }
        });
    }

    /**
     * Resolución por slug para rutas (API pública: servicios/slug/{slug}).
     */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        if ($field === 'slug') {
            return static::query()->where('slug', $value)->where('activo', true)->first();
        }
        return parent::resolveRouteBinding($value, $field);
    }
}

