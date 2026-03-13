<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        Schema::table('entregas', function (Blueprint $table) {
            $table->foreignId('venta_detalle_id')->nullable()->after('venta_id')->constrained('venta_detalles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('entregas', function (Blueprint $table) {
            $table->dropForeign(['venta_detalle_id']);
            $table->dropColumn('venta_detalle_id');
        });
    }
};
