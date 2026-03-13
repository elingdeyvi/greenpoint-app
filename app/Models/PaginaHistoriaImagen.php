<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaHistoriaImagen extends Model
{
    use HasFactory;

    protected $table = 'pagina_historia_imagenes';

    protected $fillable = [
        'pagina_historia_id',
        'ruta_imagen',
        'orden',
    ];

    protected $casts = [
        'pagina_historia_id' => 'integer',
        'orden' => 'integer',
    ];

    public function paginaHistoria()
    {
        return $this->belongsTo(PaginaHistoria::class);
    }
}

