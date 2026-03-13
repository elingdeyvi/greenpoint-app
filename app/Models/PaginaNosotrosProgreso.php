<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaNosotrosProgreso extends Model
{
    use HasFactory;

    protected $table = 'pagina_nosotros_progreso';

    protected $fillable = [
        'pagina_nosotros_id',
        'titulo',
        'porcentaje',
        'descripcion',
        'orden',
    ];

    protected $casts = [
        'pagina_nosotros_id' => 'integer',
        'porcentaje' => 'integer',
        'orden' => 'integer',
    ];

    public function paginaNosotros()
    {
        return $this->belongsTo(PaginaNosotros::class);
    }
}

