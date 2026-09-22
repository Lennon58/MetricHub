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