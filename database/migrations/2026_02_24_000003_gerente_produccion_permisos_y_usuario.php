<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Rol Gerente de Producción: puede agregar inventario, ventas, entregas, caja, etc.
     * No puede modificar precios (precios.modificar solo Administrador).
     * Crea usuario de prueba gerente@gmail.com si no existe.
     */
    public function up(): void
    {
        $gerenteRole = Role::where('name', 'Gerente de Producción')->where('guard_name', 'web')->first();
        if (! $gerenteRole) {
            $gerenteRole = Role::create(['name' => 'Gerente de Producción', 'guard_name' => 'web']);
        }

        // Asegurar que exista el permiso inventario.ajustar
        Permission::firstOrCreate(['name' => 'inventario.ajustar', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'reportes.ver', 'guard_name' => 'web']);

        // Gerente: puede agregar/ajustar inventario, ventas, productos, entregas, caja, clientes, reportes. Sin precios.modificar
        $gerenteRole->syncPermissions([
            'dashboard.ver',
            'ventas.crear',
            'ventas.ver',
            'ventas.entregar',
            'productos.ver',
            'productos.administrar',
            'inventario.consultar',
            'inventario.ajustar',
            'entregas.registrar',
            'cajas.abrir_cerrar',
            'gastos.registrar',
            'clientes.ver',
            'clientes.administrar',
            'reportes.consultar',
            'reportes.consultar_propios',
            'reportes.ver',
        ]);

        // Quitar precios.modificar al Gerente si lo tuviera
        $gerenteRole->revokePermissionTo('precios.modificar');

        // Usuario de prueba Gerente de Producción
        $user = User::where('email', 'gerente@gmail.com')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Gerente de Producción',
                'email' => 'gerente@gmail.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole($gerenteRole);
        } else {
            $user->syncRoles([$gerenteRole]);
        }
    }

    public function down(): void
    {
        $user = User::where('email', 'gerente@gmail.com')->first();
        if ($user) {
            $user->removeRole('Gerente de Producción');
            $user->delete();
        }

        $gerenteRole = Role::where('name', 'Gerente de Producción')->where('guard_name', 'web')->first();
        if ($gerenteRole) {
            $gerenteRole->revokePermissionTo('inventario.ajustar');
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
                'clientes.ver',
                'clientes.administrar',
                'reportes.consultar_propios',
            ]);
        }
    }
};
