<?php

use App\Http\Controllers\MetricaManualController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->group(function () {

    // Lista e exibe a página principal com a tabela de métricas cadastradas
    Route::get('/metricas', [MetricaManualController::class, 'index'])
        ->name('metricas.index');

    // Recebe os dados do formulário e salva uma nova métrica no banco de dados
    Route::post('/metricas', [MetricaManualController::class, 'store'])
        ->name('metricas.store');

    // Atualiza os dados de uma métrica existente com base no ID recebido
    Route::put('/metricas/{metricaManual}', [MetricaManualController::class, 'update'])
        ->name('metricas.update');

    // Remove uma métrica específica do banco de dados
    Route::delete('/metricas/{metricaManual}', [MetricaManualController::class, 'destroy'])
        ->name('metricas.destroy');

});