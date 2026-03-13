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
        Schema::table('entregas', function (Blueprint $table) {
            $table->string('foto_path')->nullable()->after('foto_ruta');
            $table->string('uuid_qr')->nullable()->after('foto_path');
            $table->unsignedInteger('numero_viaje')->nullable()->after('uuid_qr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn(['foto_path', 'uuid_qr', 'numero_viaje']);
        });
    }
};

