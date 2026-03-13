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
        $adminRole = Role::create(['name' => 'Administrador', 'guard_name' => 'web']);
        $despachadorRole = Role::create(['name' => 'Despachador', 'guard_name' => 'web']);
        $vigilanteRole = Role::create(['name' => 'Vigilante', 'guard_name' => 'web']);
        $gerenteRole = Role::create(['name' => 'Gerente de Producción', 'guard_name' => 'web']);

        // Administrador: todos los permisos (incluye precios.modificar y usuarios.gestionar como únicos de admin)
        $adminRole->givePermissionTo($permissions);

        // Gerente de Producción: inventario.ajustar, reportes.ver
        $gerenteRole->givePermissionTo([
            'inventario.ajustar',
            'reportes.ver',
        ]);

        // Despachador: ventas.crear, entregas.registrar + operaciones y reportes propios
        $despachadorRole->givePermissionTo([
            'operaciones.generar',
            'reportes.consultar_propios',
            'ventas.crear',
            'entregas.registrar',
        ]);

        // Vigilante: boletos.validar_salida (+ operaciones.verificar para compatibilidad)
        $vigilanteRole->givePermissionTo([
            'operaciones.verificar',
            'boletos.validar_salida',
        ]);

        // Crear usuarios de prueba
        // Nota: No se incluye 'estatus' aquí porque se agrega en una migración posterior (2024_01_15_000013)
        // El valor por defecto 'activo' se aplicará automáticamente cuando se agregue la columna
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'password' => '$2y$10$jOunoLNJ1KJ5MK3smYZ0q./c.PnQYnM4BAd0rikaRBAmGYveG8ydK',
            'email_verified_at' => now(),
        ]);

        $despachadorUser = User::create([
            'name' => 'Despachador',
            'email' => 'despachador@gmail.com',
            'password' => '$2y$10$jOunoLNJ1KJ5MK3smYZ0q./c.PnQYnM4BAd0rikaRBAmGYveG8ydK',
            'email_verified_at' => now(),
        ]);

        $vigilanteUser = User::create([
            'name' => 'Vigilante',
            'email' => 'vigilante@gmail.com',
            'password' => '$2y$10$jOunoLNJ1KJ5MK3smYZ0q./c.PnQYnM4BAd0rikaRBAmGYveG8ydK',
            'email_verified_at' => now(),
        ]);

        // Asignar roles a los usuarios
        $adminUser->assignRole($adminRole);
        $despachadorUser->assignRole($despachadorRole);
        $vigilanteUser->assignRole($vigilanteRole);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar usuarios de prueba
        User::where('email', 'admin@gmail.com')->delete();
        User::where('email', 'despachador@gmail.com')->delete();
        User::where('email', 'vigilante@gmail.com')->delete();

        // Eliminar roles (Gerente primero por dependencias)
        Role::where('name', 'Gerente de Producción')->delete();
        Role::where('name', 'Administrador')->delete();
        Role::where('name', 'Despachador')->delete();
        Role::where('name', 'Vigilante')->delete();

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
