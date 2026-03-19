<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear permisos por módulo
        $permissions = [
            // Dashboard
            'dashboard.ver',

            // Módulo de Operaciones
            'operaciones.generar',
            'operaciones.verificar',
            'operaciones.consultar',

            // Boletos
            'boletos.validar_salida',

            // Módulo de Reportes
            'reportes.consultar',
            'reportes.consultar_propios',
            'reportes.ver',

            // Módulo de Administración (Administrador: permisos únicos)
            'administracion.usuarios',
            'administracion.roles',
            'administracion.configuracion_hardware',
            'administracion.catalogos',
            'precios.modificar',
            'usuarios.gestionar',

            // Ventas y entregas
            'ventas.crear',
            'entregas.registrar',

            // Inventario
            'inventario.ajustar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Crear roles
        // En GreenPoint usamos principalmente roles que maneja RolesAndPermissionsSeeder.
        // Este migration conserva permisos y el rol Administrador, pero elimina roles de prueba
        // que no se usan en el sistema actual.
        $adminRole = Role::create(['name' => 'Administrador', 'guard_name' => 'web']);

        // Administrador: todos los permisos (incluye precios.modificar y usuarios.gestionar como únicos de admin)
        $adminRole->givePermissionTo($permissions);

        // Nota: no creamos usuarios de prueba (admin@gmail.com, despachador@gmail.com, vigilante@gmail.com)
        // porque en GreenPoint los usuarios operativos reales se crean en RolesAndPermissionsSeeder.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar roles
        Role::where('name', 'Administrador')->delete();

        // Eliminar permisos
        $permissions = [
            'dashboard.ver',
            'operaciones.generar',
            'operaciones.verificar',
            'operaciones.consultar',
            'boletos.validar_salida',
            'reportes.consultar',
            'reportes.consultar_propios',
            'reportes.ver',
            'administracion.usuarios',
            'administracion.roles',
            'administracion.configuracion_hardware',
            'administracion.catalogos',
            'precios.modificar',
            'usuarios.gestionar',
            'ventas.crear',
            'entregas.registrar',
            'inventario.ajustar',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
