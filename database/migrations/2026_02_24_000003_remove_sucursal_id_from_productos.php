<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Quita la relación producto-sucursal. Los productos son globales;
     * los 3 compartidos (Polvo, Rezaga, Balustre) usan el mismo ID en ambos sistemas.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('productos', 'sucursal_id')) {
            return;
        }
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn('sucursal_id');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('sucursal_id')->nullable()->after('unidad_medida')
                ->constrained('sucursales')->nullOnDelete();
        });
    }
};
