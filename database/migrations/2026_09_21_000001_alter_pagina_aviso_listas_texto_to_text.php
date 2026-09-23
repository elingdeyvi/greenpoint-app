<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagina_aviso_listas', function (Blueprint $table) {
            $table->text('texto')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pagina_aviso_listas', function (Blueprint $table) {
            $table->string('texto')->change();
        });
    }
};
