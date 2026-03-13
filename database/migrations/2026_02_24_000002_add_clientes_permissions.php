<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Agrega permisos clientes.ver y clientes.administrar y los asigna a roles.
     */
    public function up(): void
    {
        $permisos = [
            'clientes.ver',
            'clientes.administrar',
        ];

        foreach ($permisos as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::where('name', 'Administrador')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permisos);
        }

        $gerenteRole = Role::where('name', 'Gerente de Producción')->where('guard_name', 'web')->first();
        if ($gerenteRole) {
            $gerenteRole->givePermissionTo($permisos);
        }
    }

    public function down(): void
    {
        $adminRole = Role::where('name', 'Administrador')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->revokePermissionTo(['clientes.ver', 'clientes.administrar']);
        }
        $gerenteRole = Role::where('name', 'Gerente de Producción')->where('guard_name', 'web')->first();
        if ($gerenteRole) {
            $gerenteRole->revokePermissionTo(['clientes.ver', 'clientes.administrar']);
        }
        Permission::whereIn('name', ['clientes.ver', 'clientes.administrar'])->where('guard_name', 'web')->delete();
    }
};
