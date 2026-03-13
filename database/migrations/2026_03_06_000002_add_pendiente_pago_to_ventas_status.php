<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega estatus pendiente_pago y pagado al enum de ventas.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estatus ENUM('pendiente', 'pendiente_pago', 'pagado', 'parcial', 'entregado', 'cancelado') DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        // Volver al enum anterior sin los nuevos estatus.
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estatus ENUM('pendiente', 'parcial', 'entregado', 'cancelado') DEFAULT 'pendiente'");
    }
};

