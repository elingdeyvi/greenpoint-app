<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de permisos (buena práctica al sembrar)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos de administración de usuarios/roles/configuración crítica
        $permissions = [
            'administracion.usuarios',
            'administracion.roles',
            'administracion.configuracion_critica',

            // Catálogos
            'catalogos.servicios',
            'catalogos.clientes',
            'catalogos.galeria',
            'catalogos.banners',
            'catalogos.contactos',
            'catalogos.redes_sociales',

            // Módulos administrables
            'modulos.nosotros',
            'modulos.historia',
            'modulos.tecnologia',
            'modulos.aviso',

            // Formularios de contacto
            'formularios_contacto.ver',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Rol Administrador: todos los permisos
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Rol Capturista: solo catálogos, módulos administrables y lectura de formularios_contacto
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

        // Usuario administrador por defecto
        $defaultPassword = Hash::make('12345678'); // Cambiar en producción

        $admin = User::firstOrCreate(
            ['email' => 'admin@greenpoint.com'],
            [
                'name' => 'Administrador GreenPoint',
                'password' => $defaultPassword,
                'estatus' => 'activo',
            ]
        );

        // Asegurar que el admin siempre tenga el rol Administrador y la misma contraseña base
        $admin->update(['password' => $defaultPassword]);
        if (!$admin->hasRole($adminRole->name)) {
            $admin->assignRole($adminRole);
        }

        // Usuarios operativos por defecto con dominio @greenpoint.com
        $operativos = [
            [
                'name' => 'Capturista General',
                'email' => 'capturista@greenpoint.com',
                'role' => $capturistaRole,
            ],
            [
                'name' => 'Contacto Tabasco',
                'email' => 'tabasco@greenpoint.com',
                'role' => $capturistaRole,
            ],
            [
                'name' => 'Contacto Veracruz',
                'email' => 'veracruz@greenpoint.com',
                'role' => $capturistaRole,
            ],
            [
                'name' => 'Contacto Carmen',
                'email' => 'carmen@greenpoint.com',
                'role' => $capturistaRole,
            ],
        ];

        foreach ($operativos as $op) {
            $user = User::firstOrCreate(
                ['email' => $op['email']],
                [
                    'name' => $op['name'],
                    'password' => $defaultPassword,
                    'estatus' => 'activo',
                ]
            );

            // Homologar contraseña en cada seed
            $user->update(['password' => $defaultPassword]);

            if (!$user->hasRole($op['role']->name)) {
                $user->assignRole($op['role']);
            }
        }
    }
}

