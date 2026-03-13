<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagina_aviso_secciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagina_aviso_id')
                ->constrained('pagina_aviso')
                ->cascadeOnDelete();
            $table->string('titulo');
            $table->text('contenido');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagina_aviso_secciones');
    }
};

