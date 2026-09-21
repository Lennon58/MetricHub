<?php

namespace App\Http\Controllers;

use App\Models\MetricaManual;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MetricaManualController extends Controller
{
    /**
     * Exibe a listagem de métricas e os usuários disponíveis.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Colaboradores veem apenas suas métricas; Gestores veem todas
        $metricas = MetricaManual::query()
            ->with('user:id,name')
            ->when($user->isColaborador(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->orderBy('data_inicio', 'desc')
            ->get();

        // Envia a lista de usuários apenas se for Gestor
        $usuarios = $user->isGestor()
            ? User::select('id', 'name')->orderBy('name', 'asc')->get()
            : [];

        return Inertia::render('Metricas/Index', [
            'metricas'      => $metricas,
            'usuarios'      => $usuarios,
            'isGestor'      => $user->isGestor(),
            'currentUserId' => $user->id,
        ]);
    }

    /**
     * Cadastra uma nova métrica com vínculo de usuário.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'user_id'     => [$user->isGestor() ? 'required' : 'nullable', 'exists:users,id'],
            'data_inicio' => ['required', 'date'],
            'data_fim'    => ['required', 'date', 'after_or_equal:data_inicio'],
            'tipo'        => ['required', 'string', 'max:255'],
            'valor'       => ['required', 'numeric'],
            'observacao'  => ['nullable', 'string', 'max:1000'],
        ], [
            'user_id.required'        => 'Selecione o usuário responsável pela métrica.',
            'user_id.exists'          => 'O usuário selecionado é inválido.',
            'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ]);

        // Se for colaborador, força o ID dele; se for gestor, usa o selecionado (ou o dele se não selecionar nada)
        $validated['user_id'] = $user->isColaborador() ? $user->id : ($validated['user_id'] ?? $user->id);

        MetricaManual::create($validated);

        return redirect()->back()->with('success', 'Métrica cadastrada com sucesso!');
    }

    /**
     * Atualiza um registro existente.
     */
    public function update(Request $request, MetricaManual $metricaManual): RedirectResponse
    {
        $user = $request->user();

        // Bloqueia se um colaborador tentar editar registro de terceiro
        if ($user->isColaborador() && $metricaManual->user_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }

        $validated = $request->validate([
            'user_id'     => [$user->isGestor() ? 'required' : 'nullable', 'exists:users,id'],
            'data_inicio' => ['required', 'date'],
            'data_fim'    => ['required', 'date', 'after_or_equal:data_inicio'],
            'tipo'        => ['required', 'string', 'max:255'],
            'valor'       => ['required', 'numeric'],
            'observacao'  => ['nullable', 'string', 'max:1000'],
        ], [
            'user_id.required'        => 'Selecione o usuário responsável pela métrica.',
            'user_id.exists'          => 'O usuário selecionado é inválido.',
            'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ]);

        // Força a regra de segurança no update
        $validated['user_id'] = $user->isColaborador() ? $user->id : ($validated['user_id'] ?? $user->id);

        $metricaManual->update($validated);

        return redirect()->back()->with('success', 'Métrica atualizada com sucesso!');
    }

    /**
     * Remove o registro do banco de dados.
     */
    public function destroy(Request $request, MetricaManual $metricaManual): RedirectResponse
    {
        $user = $request->user();

        // Bloqueia se um colaborador tentar excluir registro de terceiro
        if ($user->isColaborador() && $metricaManual->user_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }

        $metricaManual->delete();

        return redirect()->back()->with('success', 'Métrica removida com sucesso!');
    }
}