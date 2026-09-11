<script setup lang="ts">
import { computed } from 'vue';

interface Metrica {
    id: number;
    tipo: string;
    valor: number;
    data_inicio?: string;
    data_fim?: string;
    created_at?: string;
    user_id?: number;
    user?: { id: number; name: string };
}

const props = defineProps<{
    metricas?: Metrica[];
}>();

const chartColors = ['#6366F1', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6'];

const graficoConsolidado = computed(() => {
    if (!props.metricas || props.metricas.length === 0) return null;

    const metricasOrdenadas = [...props.metricas].sort((a, b) => {
        const dateA = new Date(a.data_inicio || a.created_at || 0).getTime();
        const dateB = new Date(b.data_inicio || b.created_at || 0).getTime();
        return dateA - dateB;
    });

    const colaboradoresMap: Record<string, { name: string; data: Array<{ x: string; y: number }> }> = {};

    metricasOrdenadas.forEach((m) => {
        const userId = m.user_id || 0;
        const userName = m.user?.name || 'Não identificado';
        const rawDate = m.data_inicio || m.created_at;
        const formattedDate = rawDate
            ? new Date(rawDate).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })
            : 'S/D';

        const label = `${formattedDate} (#${m.id})`;

        if (!colaboradoresMap[userId]) {
            colaboradoresMap[userId] = {
                name: userName,
                data: [{ x: 'Início', y: 0 }]
            };
        }

        colaboradoresMap[userId].data.push({
            x: label,
            y: Number(m.valor)
        });
    });

    return {
        series: Object.values(colaboradoresMap),
        options: {
            chart: {
                type: 'line',
                fontFamily: 'Inter, sans-serif',
                toolbar: {
                    show: true,
                    offsetY: -65,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false,
                    },
                },
                background: 'transparent',
                zoom: { enabled: false },
            },
            colors: chartColors,
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 6, hover: { size: 8 } },
            dataLabels: { enabled: false },
            xaxis: { type: 'category', axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { min: 0, forceNiceScale: true, labels: { formatter: (val: number) => val.toFixed(0) } },
            grid: { strokeDashArray: 4 },
            legend: { show: true, position: 'top', horizontalAlign: 'right', offsetY: -20, labels: { colors: '#64748B' } },
            tooltip: { shared: true, intersect: false },
        },
    };
});
</script>

<template>
    <div
        v-if="graficoConsolidado"
        class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-[0_15px_30px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] transition-all"
    >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                    Comparativo de Crescimento
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Acompanhe as trajetórias e parábolas de desempenho de todos os colaboradores selecionados.
                </p>
            </div>
        </div>

        <div class="min-h-[380px] w-full">
            <apexchart
                type="line"
                height="380"
                :options="graficoConsolidado?.options"
                :series="graficoConsolidado?.series"
            ></apexchart>
        </div>
    </div>

    <!-- Caixa Sem Dados -->
    <div
        v-else
        class="h-72 flex flex-col items-center justify-center text-center p-8 rounded-[2rem] bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-[0_20px_50px_-10px_rgba(0,0,0,0.1)] dark:shadow-[0_25px_60px_-15px_rgba(0,0,0,0.8)] transition-all"
    >
        <div class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 mb-4">
            <svg
                class="w-8 h-8"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                ></path>
            </svg>
        </div>
        <p class="text-sm font-semibold text-slate-900 dark:text-white">
            Nenhuma métrica encontrada para os filtros aplicados.
        </p>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Selecione outro usuário ou cadastre novas métricas para visualizar os gráficos.
        </p>
    </div>
</template>

<style>
/* Força a remoção do fundo em qualquer div ou elemento dentro da toolbar */
.apexcharts-toolbar,
.apexcharts-toolbar *,
.apexcharts-toolbar div,
.apexcharts-menu-icon,
.apexcharts-icon {
    background: none !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
}

/* Garante transparência mesmo no hover */
.apexcharts-toolbar *:hover {
    background: none !important;
    background-color: transparent !important;
}

/* Fundo azul do menu suspenso */
.apexcharts-menu {
    background-color: #0b1329 !important;
    border: 1px solid #1e293b !important;
    color: #f3f4f6 !important;
}

.apexcharts-menu-item:hover {
    background-color: #1e293b !important;
}

/* Cor do Ícone SVG */
.apexcharts-toolbar svg,
.apexcharts-menu-icon svg {
    fill: #0f172a !important;
    stroke: #0f172a !important;
}

.dark .apexcharts-toolbar svg,
.dark .apexcharts-menu-icon svg {
    fill: #dbdcdf !important;
    stroke: #dbdcdf !important;
}
</style>