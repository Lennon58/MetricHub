<?php

namespace App\Policies;

use App\Models\MetricaManual;
use App\Models\User;

class MetricaManualPolicy
{
    /**
     * Determina se o usuário pode ver a listagem geral.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina se o usuário pode ver uma métrica específica.
     */
    public function view(User $user, MetricaManual $metricaManual): bool
    {
        return $user->isGestor() || $metricaManual->user_id === $user->id;
    }

    /**
     * Determina se o usuário pode criar métricas.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina se o usuário pode atualizar a métrica.
     */
    public function update(User $user, MetricaManual $metricaManual): bool
    {
        return $user->isGestor() || $metricaManual->user_id === $user->id;
    }

    /**
     * Determina se o usuário pode excluir a métrica.
     */
    public function delete(User $user, MetricaManual $metricaManual): bool
    {
        return $user->isGestor() || $metricaManual->user_id === $user->id;
    }
}