<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagina_historia', function (Blueprint $table) {
            $table->string('cv_pdf')->nullable()->after('estado');
            $table->string('cv_etiqueta')->nullable()->after('cv_pdf');
        });
    }

    public function down(): void
    {
        Schema::table('pagina_historia', function (Blueprint $table) {
            $table->dropColumn(['cv_pdf', 'cv_etiqueta']);
        });
    }
};
