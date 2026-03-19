<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CleanupLegacyGmailUsersAndRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Solo limpiamos los usuarios/roles legacy que venían de una migración vieja.
        // No se tocan roles reales del proyecto (p.ej. Administrador / Capturista).

        $legacyEmails = [
            'admin@gmail.com',
            'despachador@gmail.com',
            'vigilante@gmail.com',
        ];

        foreach ($legacyEmails as $email) {
            $user = User::query()->where('email', $email)->first();
            if (!$user) {
                continue;
            }

            // Evitar inconsistencias en pivot de spatie
            try {
                $user->syncRoles([]);
            } catch (\Throwable $e) {
                // No bloquea el cleanup si la relación ya no existe
            }

            $user->delete();
        }

        $legacyRoles = [
            'Despachador',
            'Vigilante',
            'Gerente de Producción',
        ];

        foreach ($legacyRoles as $roleName) {
            $role = Role::query()->where('name', $roleName)->first();
            if (!$role) {
                continue;
            }

            try {
                $role->syncPermissions([]);
            } catch (\Throwable $e) {
                // No bloquea el cleanup
            }

            $role->delete();
        }
    }
}

