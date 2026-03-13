<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaNosotrosImagen extends Model
{
    use HasFactory;

    protected $table = 'pagina_nosotros_imagenes';

    protected $fillable = [
        'pagina_nosotros_id',
        'ruta_imagen',
        'orden',
    ];

    protected $casts = [
        'pagina_nosotros_id' => 'integer',
        'orden' => 'integer',
    ];

    public function paginaNosotros()
    {
        return $this->belongsTo(PaginaNosotros::class);
    }
}

