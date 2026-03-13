<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagina_historia_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagina_historia_id')
                ->constrained('pagina_historia')
                ->cascadeOnDelete();
            $table->string('ruta_imagen');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagina_historia_imagenes');
    }
};

