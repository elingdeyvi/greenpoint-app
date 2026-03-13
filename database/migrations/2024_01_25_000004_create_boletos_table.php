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
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique();
            $table->text('codigo_qr');
            $table->enum('estatus', ['pendiente', 'utilizado', 'cancelado'])->default('pendiente');
            $table->string('foto_ruta')->nullable();
            $table->string('placa', 20);
            $table->string('conductor', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_generador_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('usuario_validador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_generacion')->useCurrent();
            $table->timestamp('fecha_validacion')->nullable();
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
        Schema::dropIfExists('boletos');
    }
};

