<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega rol Gerente de Producción, nuevos permisos (ventas, productos, inventario, entregas, cajas, gastos)
     * y actualiza permisos del rol Vigilante.
     */
    public function up(): void
    {
        $nuevosPermisos = [
            'ventas.crear',
            'ventas.ver',
            'ventas.entregar',
            'productos.ver',
            'productos.administrar',
            'inventario.consultar',
            'entregas.registrar',
            'cajas.abrir_cerrar',
            'gastos.registrar',
        ];

        foreach ($nuevosPermisos as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Rol: Gerente de Producción
        $gerenteRole = Role::firstOrCreate(['name' => 'Gerente de Producción', 'guard_name' => 'web']);
        $gerenteRole->syncPermissions([
            'dashboard.ver',
            'ventas.crear',
            'ventas.ver',
            'ventas.entregar',
            'productos.ver',
            'productos.administrar',
            'inventario.consultar',
            'entregas.registrar',
            'cajas.abrir_cerrar',
            'gastos.registrar',
            'reportes.consultar_propios',
        ]);

        // Administrador: tener también los nuevos permisos
        $adminRole = Role::where('name', 'Administrador')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($nuevosPermisos);
        }

        // Vigilante: agregar permisos nuevos (entregas, ver ventas para validación)
        $vigilanteRole = Role::where('name', 'Vigilante')->where('guard_name', 'web')->first();
        if ($vigilanteRole) {
            $vigilanteRole->givePermissionTo([
                'ventas.ver',
                'entregas.registrar',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $gerenteRole = Role::where('name', 'Gerente de Producción')->where('guard_name', 'web')->first();
        if ($gerenteRole) {
            $gerenteRole->syncPermissions([]);
            $gerenteRole->delete();
        }

        $vigilanteRole = Role::where('name', 'Vigilante')->where('guard_name', 'web')->first();
        if ($vigilanteRole) {
            $vigilanteRole->revokePermissionTo(['ventas.ver', 'entregas.registrar']);
        }

        $nuevosPermisos = [
            'ventas.crear', 'ventas.ver', 'ventas.entregar',
            'productos.ver', 'productos.administrar', 'inventario.consultar',
            'entregas.registrar', 'cajas.abrir_cerrar', 'gastos.registrar',
        ];
        Permission::whereIn('name', $nuevosPermisos)->where('guard_name', 'web')->delete();
    }
};
