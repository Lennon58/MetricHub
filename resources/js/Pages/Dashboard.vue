<script setup lang="ts">
import { ref, computed, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage, Link } from "@inertiajs/vue3";
import UserSelectFilter from "@/Components/UserSelectFilter.vue";
import DateRangeFilter from "@/Components/DateRangeFilter.vue";
import DashboardStats from "@/Components/DashboardStats.vue";
import DashboardChartsGrid from "@/Components/DashboardChartsGrid.vue";
import { useDashboardCharts } from "@/Composables/useDashboardCharts";

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

const { graficosPorUsuarioETipo } = useDashboardCharts(
    computed(() => props.metricas),
);
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