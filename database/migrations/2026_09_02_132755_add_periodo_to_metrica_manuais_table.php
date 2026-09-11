<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('metrica_manuals', function (Blueprint $table) {
            $table->renameColumn('data_referencia', 'data_inicio');
            $table->date('data_fim')->nullable()->after('data_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('metrica_manuals', function (Blueprint $table) {
            $table->renameColumn('data_inicio', 'data_referencia');
            $table->dropColumn('data_fim');
        });
    }
};