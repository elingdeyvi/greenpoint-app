<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaNosotros extends Model
{
    use HasFactory;

    protected $table = 'pagina_nosotros';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'texto_descriptivo',
        'texto_adicional',
        'url_video',
        'imagen_destacada',
        'meta_descripcion',
        'meta_keywords',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function imagenes()
    {
        return $this->hasMany(PaginaNosotrosImagen::class)->orderBy('orden');
    }

    public function progreso()
    {
        return $this->hasMany(PaginaNosotrosProgreso::class)->orderBy('orden');
    }
}

