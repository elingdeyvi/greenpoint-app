<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->string('subtitulo')->nullable()->after('ubicacion');
            $table->text('mapa_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->dropColumn('subtitulo');
            $table->string('mapa_url')->nullable()->change();
        });
    }
};
