<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracion_hardware', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50); // 'camara_ip' o 'impresora'
            $table->string('nombre', 255);
            $table->string('ip_url', 255)->nullable();
            $table->integer('puerto')->nullable();
            $table->string('usuario', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('url_snapshot', 500)->nullable(); // URL completa para snapshot de cámara
            $table->string('modelo', 255)->nullable();
            $table->text('configuracion_adicional')->nullable(); // JSON para configuraciones extra
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracion_hardware');
    }
};

