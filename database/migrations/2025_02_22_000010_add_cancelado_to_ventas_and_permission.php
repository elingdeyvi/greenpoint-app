<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estatus ENUM('pendiente', 'parcial', 'entregado', 'cancelado') DEFAULT 'pendiente'");
        Permission::firstOrCreate(['name' => 'ventas.cancelar', 'guard_name' => 'web']);
        $admin = Role::where('name', 'Administrador')->where('guard_name', 'web')->first();
        if ($admin) {
            $admin->givePermissionTo('ventas.cancelar');
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estatus ENUM('pendiente', 'parcial', 'entregado') DEFAULT 'pendiente'");
        Permission::where('name', 'ventas.cancelar')->where('guard_name', 'web')->delete();
    }
};
