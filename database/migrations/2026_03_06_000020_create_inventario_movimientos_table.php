<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create inventory movements history table.
     */
    public function up(): void
    {
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('tipo', 30); // entrada, ajuste, venta, cancelacion, donativo, etc.
            $table->decimal('cantidad', 12, 2);
            $table->decimal('stock_anterior', 12, 2)->nullable();
            $table->decimal('stock_nuevo', 12, 2)->nullable();
            $table->string('motivo', 255)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};

