<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfiguracionHardware extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'configuracion_hardware';

    protected $fillable = [
        'tipo',
        'nombre',
        'ip_url',
        'puerto',
        'usuario',
        'password',
        'url_snapshot',
        'modelo',
        'configuracion_adicional',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'configuracion_adicional' => 'array',
    ];

    /**
     * Scope para activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para cámaras IP
     */
    public function scopeCamaras($query)
    {
        return $query->where('tipo', 'camara_ip');
    }

    /**
     * Scope para impresoras
     */
    public function scopeImpresoras($query)
    {
        return $query->where('tipo', 'impresora');
    }
}

