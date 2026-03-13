<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boleto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'boletos';

    protected $fillable = [
        'folio',
        'codigo_qr',
        'estatus',
        'foto_ruta',
        'placa',
        'conductor',
        'observaciones',
        'usuario_generador_id',
        'usuario_validador_id',
        'fecha_generacion',
        'fecha_validacion',
    ];

    protected $casts = [
        'fecha_generacion' => 'datetime',
        'fecha_validacion' => 'datetime',
    ];

    /**
     * Relación con usuario generador
     */
    public function usuarioGenerador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_generador_id');
    }

    /**
     * Relación con usuario validador
     */
    public function usuarioValidador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_validador_id');
    }

    /**
     * Scope para boletos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estatus', 'pendiente');
    }

    /**
     * Scope para boletos utilizados
     */
    public function scopeUtilizados($query)
    {
        return $query->where('estatus', 'utilizado');
    }

    /**
     * Obtener la URL de la foto
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto_ruta) {
            return asset('storage/' . $this->foto_ruta);
        }
        return null;
    }
}

