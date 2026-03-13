<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    protected $fillable = [
        'ubicacion',
        'direccion',
        'telefono',
        'email',
        'mapa_url',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];
}

