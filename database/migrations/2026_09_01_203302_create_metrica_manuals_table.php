<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('metrica_manuals', function (Blueprint $table) {
        $table->id();
        $table->date('data_referencia'); // Mês/Dia da métrica
        $table->string('tipo'); // ex: 'Meta de Vendas', 'Custo Operacional', 'Horas Trabalhadas'
        $table->decimal('valor', 10, 2); // O valor numérico digitado
        $table->text('observacao')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrica_manuals');
    }
};
