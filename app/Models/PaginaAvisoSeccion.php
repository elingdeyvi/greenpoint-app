<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaAvisoSeccion extends Model
{
    use HasFactory;

    protected $table = 'pagina_aviso_secciones';

    protected $fillable = [
        'pagina_aviso_id',
        'titulo',
        'contenido',
        'orden',
    ];

    protected $casts = [
        'pagina_aviso_id' => 'integer',
        'orden' => 'integer',
    ];

    public function paginaAviso()
    {
        return $this->belongsTo(PaginaAviso::class);
    }

    public function listas()
    {
        return $this->hasMany(PaginaAvisoLista::class)->orderBy('orden');
    }
}

