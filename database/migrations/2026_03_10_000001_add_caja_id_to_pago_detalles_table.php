<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade caja_id a pago_detalles para trazabilidad de pagos con la caja abierta.
     */
    public function up(): void
    {
        Schema::table('pago_detalles', function (Blueprint $table) {
            $table->foreignId('caja_id')->nullable()->after('venta_id')->constrained('cajas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pago_detalles', function (Blueprint $table) {
            $table->dropForeign(['caja_id']);
        });
    }
};
