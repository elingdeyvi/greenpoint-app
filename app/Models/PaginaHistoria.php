<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaHistoria extends Model
{
    use HasFactory;

    protected $table = 'pagina_historia';

    protected $fillable = [
        'titulo',
        'meta_descripcion',
        'meta_keywords',
        'estado',
        'cv_pdf',
        'cv_etiqueta',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function eventos()
    {
        return $this->hasMany(PaginaHistoriaEvento::class)->orderBy('orden');
    }

    public function imagenes()
    {
        return $this->hasMany(PaginaHistoriaImagen::class)->orderBy('orden');
    }
}

