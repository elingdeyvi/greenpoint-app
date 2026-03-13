<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaAvisoLista extends Model
{
    use HasFactory;

    protected $table = 'pagina_aviso_listas';

    protected $fillable = [
        'pagina_aviso_seccion_id',
        'texto',
        'orden',
    ];

    protected $casts = [
        'pagina_aviso_seccion_id' => 'integer',
        'orden' => 'integer',
    ];

    public function seccion()
    {
        return $this->belongsTo(PaginaAvisoSeccion::class, 'pagina_aviso_seccion_id');
    }
}

