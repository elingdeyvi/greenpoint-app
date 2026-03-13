<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaHistoriaEvento extends Model
{
    use HasFactory;

    protected $table = 'pagina_historia_eventos';

    protected $fillable = [
        'pagina_historia_id',
        'anio',
        'titulo',
        'descripcion',
        'orden',
    ];

    protected $casts = [
        'pagina_historia_id' => 'integer',
        'anio' => 'integer',
        'orden' => 'integer',
    ];

    public function paginaHistoria()
    {
        return $this->belongsTo(PaginaHistoria::class);
    }
}

