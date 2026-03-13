<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Una sola sucursal en configuración: la del equipo/instalación.
     * Reemplaza sucursal_venta_id y sucursal_almacen_id por sucursal_id.
     */
    public function up(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->foreignId('sucursal_id')->nullable()->after('activo')
                ->constrained('sucursales')->nullOnDelete();
        });

        DB::table('configuracion_empresa')->update([
            'sucursal_id' => DB::raw('COALESCE(sucursal_venta_id, sucursal_almacen_id)'),
        ]);

        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->dropForeign(['sucursal_venta_id']);
            $table->dropForeign(['sucursal_almacen_id']);
            $table->dropColumn(['sucursal_venta_id', 'sucursal_almacen_id']);
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->foreignId('sucursal_venta_id')->nullable()->after('activo')
                ->constrained('sucursales')->nullOnDelete();
            $table->foreignId('sucursal_almacen_id')->nullable()->after('sucursal_venta_id')
                ->constrained('sucursales')->nullOnDelete();
        });

        DB::table('configuracion_empresa')->update([
            'sucursal_venta_id' => DB::raw('sucursal_id'),
            'sucursal_almacen_id' => DB::raw('sucursal_id'),
        ]);

        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
        });
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->dropColumn('sucursal_id');
        });
    }
};
