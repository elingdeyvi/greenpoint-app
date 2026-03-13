<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formularios_contacto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono')->nullable();
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->timestamps();

            $table->index('leido');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formularios_contacto');
    }
};

