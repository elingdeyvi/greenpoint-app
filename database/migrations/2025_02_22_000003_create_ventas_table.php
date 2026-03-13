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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estatus', ['pendiente', 'parcial', 'entregado'])->default('pendiente');
            $table->enum('tipo', ['venta', 'donativo'])->default('venta');
            $table->uuid('ticket_origen_uuid')->nullable()->comment('Para ventas originadas en Macuspana');
            $table->text('qr_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
