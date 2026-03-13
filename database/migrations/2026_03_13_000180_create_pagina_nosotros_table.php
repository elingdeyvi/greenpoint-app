<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagina_nosotros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('subtitulo')->nullable();
            $table->text('texto_descriptivo')->nullable();
            $table->text('texto_adicional')->nullable();
            $table->string('url_video')->nullable();
            $table->string('imagen_destacada')->nullable();
            $table->string('meta_descripcion')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagina_nosotros');
    }
};

