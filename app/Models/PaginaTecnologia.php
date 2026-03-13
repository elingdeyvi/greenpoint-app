<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaTecnologia extends Model
{
    use HasFactory;

    protected $table = 'pagina_tecnologia';

    protected $fillable = [
        'titulo',
        'contenido',
        'imagen_destacada',
        'meta_descripcion',
        'meta_keywords',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function secciones()
    {
        return $this->hasMany(PaginaTecnologiaSeccion::class)->orderBy('orden');
    }
}

