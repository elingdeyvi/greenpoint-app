<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vigilante_qrs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('sucursal_origen_id')->nullable();
            $table->unsignedBigInteger('sucursal_local_id')->nullable();
            $table->string('origen', 20)->default('local'); // local | importado
            $table->text('payload_original');
            $table->unsignedInteger('viajes_permitidos')->default(1);
            $table->unsignedInteger('viajes_usados')->default(0);
            $table->string('estatus', 20)->default('activo'); // activo | agotado
            $table->timestamps();

            $table->index('uuid');
            $table->index('sucursal_origen_id');
            $table->index('sucursal_local_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vigilante_qrs');
    }
};

