<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaAviso extends Model
{
    use HasFactory;

    protected $table = 'pagina_aviso';

    protected $fillable = [
        'titulo',
        'meta_descripcion',
        'meta_keywords',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function secciones()
    {
        return $this->hasMany(PaginaAvisoSeccion::class)->orderBy('orden');
    }
}

