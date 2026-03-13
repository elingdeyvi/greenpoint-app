<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use LaravelAndVueJS\Traits\LaravelPermissionToVueJS;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    use LaravelPermissionToVueJS;
    public const ADMIN_ROL = 'Administrador';
    public const DESPACHADOR_ROL = 'Despachador';
    public const VIGILANTE_ROL = 'Vigilante';
    public const GERENTE_PRODUCCION_ROL = 'Gerente de Producción';
    public const ROLES = [
        self::ADMIN_ROL,
        self::DESPACHADOR_ROL,
        self::VIGILANTE_ROL,
        self::GERENTE_PRODUCCION_ROL,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'estatus',
        'autorizado',
        'cliente_id',
        'nota',
        'uuid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relación con boletos generados (para Despachador)
     */
    public function boletosGenerados(): HasMany
    {
        return $this->hasMany(Boleto::class, 'usuario_generador_id');
    }

    /**
     * Relación con boletos validados (para Vigilante)
     */
    public function boletosValidados(): HasMany
    {
        return $this->hasMany(Boleto::class, 'usuario_validador_id');
    }

    /**
     * Relación con ventas creadas por el usuario
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'usuario_id');
    }

    /**
     * Relación con entregas registradas por el usuario
     */
    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'usuario_id');
    }

    /**
     * Relación con cajas (apertura/cierre)
     */
    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class, 'usuario_id');
    }
}
