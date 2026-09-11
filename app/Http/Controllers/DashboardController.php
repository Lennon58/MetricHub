<?php

namespace App\Http\Controllers;

use App\Models\MetricaManual;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Exibe a página do Dashboard com regras de acesso e filtros por perfil.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // Se for colaborador, força a busca apenas das métricas do próprio usuário
        if ($user->isColaborador()) {
            $selectedUserIds = [$user->id];
            $usuarios = []; // Oculta a lista de usuários no filtro
        } else {
            // Se for gestor, captura o array 'user_ids' vindo da requisição
            $inputUserIds = $request->input('user_ids', []);

            // Garante que é um array limpo
            if (is_string($inputUserIds)) {
                $inputUserIds = explode(',', $inputUserIds);
            }

            $selectedUserIds = array_filter((array) $inputUserIds);
            $usuarios = User::select('id', 'name')->orderBy('name', 'asc')->get();
        }

        // Consulta as métricas aplicando o filtro de múltiplos usuários
        $metricas = MetricaManual::query()
            ->with('user:id,name')
            ->when(!empty($selectedUserIds), function ($query) use ($selectedUserIds) {
                return $query->whereIn('user_id', $selectedUserIds);
            })
            ->orderBy('data_inicio', 'asc')
            ->get();

        return Inertia::render('Dashboard', [
            'metricas'        => $metricas,
            'usuarios'        => $usuarios,
            'selectedUserIds' => array_map('intval', array_values($selectedUserIds)),
            'isGestor'        => $user->isGestor(),
        ]);
    }
}