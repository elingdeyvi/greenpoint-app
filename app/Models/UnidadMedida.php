<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    protected $table = 'unidades_medida';

    protected $fillable = ['codigo', 'nombre', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'unidad_medida_id');
    }
}
