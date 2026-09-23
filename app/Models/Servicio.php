<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory, SoftDeletes;

    // Campos asignables en masa (snake_case en BD)
    protected $fillable = [
        'nombre',
        'subtitulo',
        'titulo_seccion',
        'descripcion',
        'imagen',
        'imagen_secundaria',
        'plantilla',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];
}

