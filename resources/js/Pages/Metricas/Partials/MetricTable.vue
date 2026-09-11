<script setup>
import EditDeletMetrica from './EditDeletMetrica.vue';

const props = defineProps({
    metricas: {
        type: Array,
        default: () => [],
    },
    isGestor: {
        type: Boolean,
        default: false,
    },
    currentUserId: {
        type: [Number, String],
        default: null,
    },
});

defineEmits(['edit', 'delete']);

const minutesToTime = (val) => {
    if (val === null || val === undefined || val === '') return '';
    if (typeof val === 'string' && val.includes(':')) return val;
    
    let totalMinutos = Number(val);
    if (isNaN(totalMinutos)) return '';
    if (!Number.isInteger(totalMinutos)) {
        totalMinutos = Math.round(totalMinutos * 60);
    }
    const horas = Math.floor(totalMinutos / 60);
    const minutos = totalMinutos % 60;
    return `${String(horas).padStart(2, '0')}:${String(minutos).padStart(2, '0')}`;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const [year, month, day] = dateString.split('-');
    return `${day}/${month}/${year}`;
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val || 0);
};

const formatValorDisplay = (tipo, valor) => {
    if (valor === null || valor === undefined) return '-';
    if (tipo && tipo.includes('(R$)')) return formatCurrency(valor);
    if (tipo === 'Horas Trabalhadas') return `${minutesToTime(valor)} h`;
    if (tipo === 'Satisfação (NPS / 0 a 10)') return `${valor} pts`;
    return valor;
};

const getBadgeStyle = (tipo) => {
    if (tipo?.includes('Vendas')) return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950/40 dark:text-emerald-400 dark:ring-emerald-500/30';
    if (tipo?.includes('Custo')) return 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-950/40 dark:text-rose-400 dark:ring-rose-500/30';
    if (tipo?.includes('Horas')) return 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-950/40 dark:text-blue-400 dark:ring-blue-500/30';
    return 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950/40 dark:text-amber-400 dark:ring-amber-500/30';
};
</script>

<template>
    <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-[0_15px_30px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] overflow-hidden transition-all">
        <div class="p-6 sm:p-8 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                    Histórico de Métricas
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Registros lançados no sistema ordenados por período.
                </p>
            </div>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/60 px-3 py-1 rounded-full border border-slate-200/50 dark:border-slate-700/50">
                {{ props.metricas.length }} {{ props.metricas.length === 1 ? 'registro' : 'registros' }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-bold tracking-wider">
                        <th class="py-3.5 px-6">Usuário</th>
                        <th class="py-3.5 px-6">Período</th>
                        <th class="py-3.5 px-6">Tipo</th>
                        <th class="py-3.5 px-6">Valor</th>
                        <th class="py-3.5 px-6">Observação</th>
                        <th class="py-3.5 px-6 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/60 text-slate-700 dark:text-slate-200">
                    <tr 
                        v-for="item in props.metricas" 
                        :key="item.id" 
                        class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors"
                    >
                        <td class="py-4 px-6 font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ item.user?.name ? item.user.name.charAt(0).toUpperCase() : '?' }}
                                </div>
                                <span>{{ item.user ? item.user.name : 'Não informado' }}</span>
                            </div>
                        </td>

                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            <div class="flex items-center gap-1.5 font-medium">
                               <span>{{ formatDate(item.data_inicio) }}</span>
                                <span class="text-slate-300 dark:text-slate-600">→</span>
                                <span>{{ formatDate(item.data_fim) }}</span>
                            </div>
                        </td>

                        <td class="py-4 px-6 whitespace-nowrap">
                            <span :class="['inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold ring-1 ring-inset', getBadgeStyle(item.tipo)]">
                                {{ item.tipo }}
                            </span>
                        </td>

                        <td class="py-4 px-6 whitespace-nowrap font-bold text-slate-900 dark:text-white">
                            {{ formatValorDisplay(item.tipo, item.valor) }}
                        </td>

                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400 max-w-xs truncate">
                            {{ item.observacao || '-' }}
                        </td>

                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            <EditDeletMetrica 
                                :can-edit="props.isGestor || item.user_id === props.currentUserId"
                                @edit="$emit('edit', item)"
                                @delete="$emit('delete', item.id)"
                            />
                        </td>
                    </tr>

                    <tr v-if="props.metricas.length === 0">
                        <td colspan="6" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-12 h-12 stroke-1 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">Nenhum registro cadastrado no momento.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>