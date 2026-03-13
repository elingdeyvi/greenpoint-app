<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagina_aviso_listas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagina_aviso_seccion_id')
                ->constrained('pagina_aviso_secciones')
                ->cascadeOnDelete();
            $table->string('texto');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagina_aviso_listas');
    }
};

