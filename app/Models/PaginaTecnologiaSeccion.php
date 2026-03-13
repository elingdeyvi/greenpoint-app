<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaTecnologiaSeccion extends Model
{
    use HasFactory;

    protected $table = 'pagina_tecnologia_secciones';

    protected $fillable = [
        'pagina_tecnologia_id',
        'titulo',
        'contenido',
        'orden',
    ];

    protected $casts = [
        'pagina_tecnologia_id' => 'integer',
        'orden' => 'integer',
    ];

    public function paginaTecnologia()
    {
        return $this->belongsTo(PaginaTecnologia::class);
    }
}

