<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega monto_recibido a pago_detalles para auditoría (pagos con cambio).
     * monto = importe aplicado a la venta; monto_recibido = lo entregado por el cliente (efectivo).
     */
    public function up(): void
    {
        Schema::table('pago_detalles', function (Blueprint $table) {
            $table->decimal('monto_recibido', 12, 2)->nullable()->after('monto');
        });
    }

    public function down(): void
    {
        Schema::table('pago_detalles', function (Blueprint $table) {
            $table->dropColumn('monto_recibido');
        });
    }
};
