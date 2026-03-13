<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Viajes a nivel pedido (venta) global: ventas tienen viajes_permitidos/usados;
     * vigilante_qrs se asocian a una venta para compartir el mismo pool de viajes.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->unsignedInteger('viajes_permitidos')->nullable()->after('qr_payload');
            $table->unsignedInteger('viajes_usados')->default(0)->after('viajes_permitidos');
        });

        Schema::table('vigilante_qrs', function (Blueprint $table) {
            $table->foreignId('venta_id')->nullable()->after('id')->constrained('ventas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vigilante_qrs', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
        });
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['viajes_permitidos', 'viajes_usados']);
        });
    }
};
