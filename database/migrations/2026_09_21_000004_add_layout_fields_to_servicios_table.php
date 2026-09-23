<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->string('plantilla')->default('detalle')->after('activo');
            $table->string('subtitulo')->nullable()->after('nombre');
            $table->string('titulo_seccion')->nullable()->after('subtitulo');
            $table->string('imagen_secundaria')->nullable()->after('imagen');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn(['plantilla', 'subtitulo', 'titulo_seccion', 'imagen_secundaria']);
        });
    }
};
