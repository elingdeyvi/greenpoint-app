<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Elimina columnas tipo_carga_id y destino_id de boletos y las tablas
     * tipos_carga, destinos y placas_frecuentes (módulos no requeridos).
     */
    public function up(): void
    {
        if (Schema::hasTable('boletos')) {
            if (Schema::hasColumn('boletos', 'tipo_carga_id')) {
                Schema::table('boletos', function (Blueprint $table) {
                    $table->dropForeign(['tipo_carga_id']);
                    $table->dropColumn('tipo_carga_id');
                });
            }
            if (Schema::hasColumn('boletos', 'destino_id')) {
                Schema::table('boletos', function (Blueprint $table) {
                    $table->dropForeign(['destino_id']);
                    $table->dropColumn('destino_id');
                });
            }
        }

        Schema::dropIfExists('placas_frecuentes');
        Schema::dropIfExists('destinos');
        Schema::dropIfExists('tipos_carga');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tipos_carga', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 50)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('destinos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 50)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('placas_frecuentes', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 20)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::table('boletos', function (Blueprint $table) {
            $table->foreignId('tipo_carga_id')->nullable()->after('conductor')->constrained('tipos_carga')->nullOnDelete();
            $table->foreignId('destino_id')->nullable()->after('tipo_carga_id')->constrained('destinos')->nullOnDelete();
        });
    }
};
