<script setup lang="ts">
import { computed } from "vue";

interface User {
    id: number;
    name: string;
}

interface Metrica {
    id: number;
    tipo: string;
    valor: number;
    data_inicio?: string;
    data_fim?: string;
    created_at?: string;
    user_id?: number;
    user?: User;
}

const props = defineProps<{
    metricas?: Metrica[];
}>();

const chartColors = ["#6366F1", "#10B981", "#EF4444", "#F59E0B", "#8B5CF6"];

const graficosPorUsuarioETipo = computed(() => {
    if (!props.metricas || props.metricas.length === 0) return [];

    const tiposUnicos = [...new Set(props.metricas.map((m) => m.tipo))];

    const usuariosMap: Record<number, { id: number; name: string }> = {};
    props.metricas.forEach((m) => {
        if (m.user_id) {
            usuariosMap[m.user_id] = {
                id: m.user_id,
                name: m.user?.name || `Usuário #${m.user_id}`,
            };
        }
    });

    const listaGraficos: Array<{
        userId: number;
        userName: string;
        tipo: string;
        titulo: string;
        hasData: boolean;
        series: Array<{ name: string; data: Array<{ x: string; y: number }> }>;
        options: any;
    }> = [];

    Object.values(usuariosMap).forEach((usuario) => {
        tiposUnicos.forEach((tipo) => {
            const metricasFiltradas = props.metricas!.filter(
                (m) => m.user_id === usuario.id && m.tipo === tipo,
            );

            const hasData = metricasFiltradas.length > 0;

            const metricasOrdenadas = [...metricasFiltradas].sort((a, b) => {
                const dateA = new Date(
                    a.data_inicio || a.created_at || 0,
                ).getTime();
                const dateB = new Date(
                    b.data_inicio || b.created_at || 0,
                ).getTime();
                return dateA - dateB;
            });

            const dataPoints = metricasOrdenadas.map((m) => {
                const rawDate = m.data_inicio || m.created_at;
                const formattedDate = rawDate
                    ? new Date(rawDate).toLocaleDateString("pt-BR", {
                          day: "2-digit",
                          month: "2-digit",
                      })
                    : "S/D";
                return {
                    x: `${formattedDate} (#${m.id})`,
                    y: Number(m.valor),
                };
            });

            const finalData =
                dataPoints.length > 0
                    ? [{ x: "Início", y: 0 }, ...dataPoints]
                    : [];

            listaGraficos.push({
                userId: usuario.id,
                userName: usuario.name,
                tipo: tipo,
                titulo: `${usuario.name} — ${tipo}`,
                hasData: hasData,
                series: [
                    {
                        name: usuario.name,
                        data: finalData,
                    },
                ],
                options: {
                    chart: {
                        type: "line",
                        fontFamily: "Inter, sans-serif",
                        toolbar: { show: true, offsetY: -65 },
                        background: "transparent",
                        zoom: { enabled: false },
                    },
                    colors: [chartColors[usuario.id % chartColors.length]],
                    stroke: { curve: "smooth", width: 3 },
                    markers: { size: 6, hover: { size: 8 } },
                    dataLabels: { enabled: false },
                    xaxis: {
                        type: "category",
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: {
                                colors: "#64748B",
                            },
                        },
                    },
                    yaxis: {
                        min: 0,
                        forceNiceScale: true,
                        labels: {
                            formatter: (val: number) => val.toFixed(0),
                            style: {
                                colors: "#64748B",
                            },
                        },
                    },
                    grid: { strokeDashArray: 4 },
                    legend: { show: false },
                    tooltip: { shared: false, intersect: true },
                },
            });
        });
    });

    return listaGraficos;
});
</script>

<template>
    <div>
        <!-- GRADE DE GRÁFICOS OU ESTADO VAZIO GLOBAL -->
        <div
            v-if="metricas && metricas.length > 0"
            class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        >
            <div
                v-for="grafico in graficosPorUsuarioETipo"
                :key="`${grafico.userId}-${grafico.tipo}`"
                class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-[0_15px_30px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] transition-all flex flex-col justify-between"
            >
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            {{ grafico.titulo }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Acompanhamento histórico e parâmetro individual de desempenho.
                        </p>
                    </div>
                </div>

                <div v-if="grafico.hasData" class="min-h-[380px] w-full">
                    <apexchart
                        type="line"
                        height="380"
                        :options="grafico.options"
                        :series="grafico.series"
                    ></apexchart>
                </div>

                <div
                    v-else
                    class="h-72 flex flex-col items-center justify-center text-center p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800"
                >
                    <div class="p-3 rounded-2xl bg-white dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 mb-3 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-900 dark:text-white">
                        Nenhuma métrica encontrada para este período.
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Selecione outro intervalo de datas para exibir o gráfico.
                    </p>
                </div>
            </div>
        </div>

        <!-- ESTADO VAZIO GLOBAL CASO NÃO HAJA NENHUMA MÉTRICA NO PERÍODO -->
        <div
            v-else
            class="h-96 flex flex-col items-center justify-center text-center p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm"
        >
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 mb-4 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                Nenhuma métrica encontrada para este período.
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm">
                Tente selecionar outro intervalo de datas ou ajustar os filtros de colaborador para visualizar os gráficos.
            </p>
        </div>
    </div>
</template>