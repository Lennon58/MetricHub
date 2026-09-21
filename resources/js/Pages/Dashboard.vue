<script setup lang="ts">
import { ref, computed, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage, Link } from "@inertiajs/vue3";
import UserSelectFilter from "@/Components/UserSelectFilter.vue";
import DateRangeFilter from "@/Components/DateRangeFilter.vue";
import DashboardStats from "@/Components/DashboardStats.vue";
import DashboardChartsGrid from "@/Components/DashboardChartsGrid.vue";

interface User {
    id: number;
    name: string;
    role?: string;
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
    usuarios?: User[];
    selectedUserIds?: (number | string)[];
    startDate?: string;
    endDate?: string;
    isGestor?: boolean;
}>();

const page = usePage();
const authUser = computed(() => page.props.auth.user as User);

const selectedUsers = ref<(number | string)[]>(props.selectedUserIds || []);
const dataInicio = ref<string>(props.startDate || "");
const dataFim = ref<string>(props.endDate || "");

const aplicarFiltros = () => {
    router.get(
        route("dashboard"),
        {
            user_ids: selectedUsers.value,
            start_date: dataInicio.value,
            end_date: dataFim.value,
        },
        { preserveState: true, replace: true },
    );
};

watch(
    selectedUsers,
    () => {
        aplicarFiltros();
    },
    { deep: true },
);

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
    <Head title="Painel" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-4"
            >
                <div>
                    <h2
                        class="font-extrabold text-2xl tracking-tight leading-tight text-slate-900 dark:text-white transition-colors"
                    >
                        Painel
                    </h2>
                    <p
                        class="text-xs sm:text-sm mt-1 text-slate-500 dark:text-slate-400 transition-colors"
                    >
                        Acompanhe aqui o desempenho e os indicadores em tempo
                        real.
                    </p>
                </div>

                <!-- Filtros de Usuário e Período de Datas Padronizados com Sombra -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Bloco de Período Padronizado com Sombra -->
                    <div class="flex flex-wrap items-center gap-3">
                        <DateRangeFilter
                            v-model:start-date="dataInicio"
                            v-model:end-date="dataFim"
                            @filter="aplicarFiltros"
                        />
                        
                        <!-- Filtro de Colaborador -->
                        <UserSelectFilter
                            v-if="isGestor"
                            :usuarios="usuarios"
                            v-model="selectedUsers"
                        />
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
            <!-- Grid de Cards de KPIs -->

            <DashboardStats :metricas="metricas" />

            <!-- GRADE DE GRÁFICOS OU ESTADO VAZIO GLOBAL -->
            <div>
                <DashboardChartsGrid :metricas="metricas" />
            </div>
                
        </div>
    </AuthenticatedLayout>
</template>

<style>

/* =========================================================
   ESTRUTURA PADRONIZADA - IGUAL NOS DOIS TEMAS
   ========================================================= */

.apexcharts-menu {
    border-radius: 0.75rem !important;
    padding: 6px !important;
    margin-top: 4px !important;
    min-width: 160px !important;
}

.apexcharts-menu-item {
    display: block !important;
    width: 100% !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    border-radius: 0.5rem !important;
    padding: 8px 12px !important;
    margin: 2px 0 !important;
    transition:
        background 0.2s ease,
        color 0.2s ease !important;
    white-space: nowrap !important;
    box-sizing: border-box !important;
}

/* =========================================================
   MODO CLARO
   ========================================================= */

html:not(.dark) .apexcharts-menu {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
}

html:not(.dark) .apexcharts-menu-item {
    color: #334155 !important;
}

html:not(.dark) .apexcharts-menu-item:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}

/* =========================================================
   MODO ESCURO
   ========================================================= */

.dark .apexcharts-menu {
    background: #0f172a !important;
    border: 1px solid #1e293b !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4) !important;
}

.dark .apexcharts-menu-item {
    color: #94a3b8 !important;
}

.dark .apexcharts-menu-item:hover {
    background: #1e293b !important;
    color: #ffffff !important;
}
</style>