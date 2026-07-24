<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'administracion.usuarios',
            'administracion.roles',
            'administracion.configuracion_critica',
            'catalogos.servicios',
            'catalogos.clientes',
            'catalogos.galeria',
            'catalogos.banners',
            'catalogos.contactos',
            'catalogos.redes_sociales',
            'modulos.nosotros',
            'modulos.historia',
            'modulos.tecnologia',
            'modulos.aviso',
            'formularios_contacto.ver',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $capturistaPermissions = [
            'catalogos.servicios',
            'catalogos.clientes',
            'catalogos.galeria',
            'catalogos.banners',
            'catalogos.contactos',
            'catalogos.redes_sociales',
            'modulos.nosotros',
            'modulos.historia',
            'modulos.tecnologia',
            'modulos.aviso',
            'formularios_contacto.ver',
        ];

        $capturistaRole = Role::firstOrCreate(['name' => 'Capturista', 'guard_name' => 'web']);
        $capturistaRole->syncPermissions($capturistaPermissions);

        $admins = [
            [
                'email' => 'admin@greenpoint.com.mx',
                'name' => 'Administrador GreenPoint',
                'password' => 'admin123456',
                'role' => 'Administrador',
            ],
            [
                'email' => 'capturista@greenpoint.com.mx',
                'name' => 'Capturista GreenPoint',
                'password' => 'password',
                'role' => 'Capturista',
            ],
        ];

        // Elimina cuentas demo con dominio incorrecto / genérico.
        User::query()
            ->whereIn('email', ['admin@greenpoint.com', 'admin@admin.com'])
            ->delete();

        foreach ($admins as $adminData) {
            $admin = User::query()->updateOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => Hash::make($adminData['password']),
                    'estatus' => 'activo',
                    'email_verified_at' => now(),
                ]
            );

            $admin->syncRoles([$adminData['role']]);
        }
    }
}
