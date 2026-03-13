<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permitir venta_id nulo en entregas para registros de vigilante (uuid_qr) sin venta vinculada.
     */
    public function up(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
        });
        Schema::table('entregas', function (Blueprint $table) {
            $table->unsignedBigInteger('venta_id')->nullable()->change();
        });
        Schema::table('entregas', function (Blueprint $table) {
            $table->foreign('venta_id')->references('id')->on('ventas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
        });
        Schema::table('entregas', function (Blueprint $table) {
            $table->unsignedBigInteger('venta_id')->nullable(false)->change();
        });
        Schema::table('entregas', function (Blueprint $table) {
            $table->foreign('venta_id')->references('id')->on('ventas')->cascadeOnDelete();
        });
    }
};
