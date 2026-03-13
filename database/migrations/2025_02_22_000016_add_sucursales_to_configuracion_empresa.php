<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sucursal venta (ej. Villahermosa) y sucursal venta+almacén (ej. Macuspana).
     */
    public function up(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->foreignId('sucursal_venta_id')->nullable()->after('activo')
                ->constrained('sucursales')->nullOnDelete();
            $table->foreignId('sucursal_almacen_id')->nullable()->after('sucursal_venta_id')
                ->constrained('sucursales')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_empresa', function (Blueprint $table) {
            $table->dropForeign(['sucursal_venta_id']);
            $table->dropForeign(['sucursal_almacen_id']);
        });
    }
};
