<script setup>
import { ref, computed, watch, h } from 'vue';
import { useForm, router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MetricTable from './Partials/MetricTable.vue';
import MetricFormCard from './Partials/MetricFormCard.vue';
import { minutesToTime } from '@/Composables/useTimeConverter';
import { useMetricaForm } from '@/Composables/useMetricaForm';

const hasCreatedNewMetrica = ref(false);

const props = defineProps({
    metricas: {
        type: Array,
        default: () => [],
    },
    usuarios: {
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

const isEditing = ref(false);

const form = useForm({
    id: null,
    user_id: props.isGestor ? '' : props.currentUserId,
    data_inicio: '',
    data_fim: '',
    tipo: 'Meta de Vendas (R$)',
    valor: '',
    observacao: '',
});

watch(() => form.tipo, () => {
    form.valor = '';
    form.clearErrors('valor');
});

const timeToMinutes = (timeStr) => {
    if (!timeStr) return 0;
    if (typeof timeStr === 'number') return timeStr;
    if (!timeStr.includes(':')) return parseInt(timeStr, 10) || 0;
    const [horas, minutos] = timeStr.split(':').map(Number);
    return (horas * 60) + (minutos || 0);
};

const limiteHorasPeriodo = computed(() => {
    if (!form.data_inicio || !form.data_fim) return null;
    const [y1, m1, d1] = form.data_inicio.split('-').map(Number);
    const [y2, m2, d2] = form.data_fim.split('-').map(Number);
    
    const inicio = new Date(y1, m1 - 1, d1);
    const fim = new Date(y2, m2 - 1, d2);
    const diffTime = fim.getTime() - inicio.getTime();
    if (diffTime < 0) return null;

    const totalDias = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
    return totalDias * 24;
});

const horasDigitadasNumerico = computed(() => {
    if (form.tipo !== 'Horas Trabalhadas' || !form.valor) return 0;
    return timeToMinutes(form.valor) / 60;
});

const isHorasExcedidas = computed(() => {
    if (form.tipo !== 'Horas Trabalhadas' || limiteHorasPeriodo.value === null) return false;
    return horasDigitadasNumerico.value > limiteHorasPeriodo.value;
});

const isNpsInvalido = computed(() => {
    if (form.tipo !== 'Satisfação (NPS / 0 a 10)' || form.valor === '' || form.valor === null) return false;
    const val = Number(form.valor);
    return isNaN(val) || val < 0 || val > 10;
});

const { resetForm, editMetrica, submit } = useMetricaForm(
    form,
    isEditing,
    props,
    { minutesToTime, timeToMinutes },
    { isHorasExcedidas, isNpsInvalido, hasCreatedNewMetrica }
);

const deleteMetrica = (id) => {
    if (confirm('Tem certeza que deseja remover esta métrica?')) {
        router.delete(route('metricas.destroy', id), {
            preserveScroll: true,
            onSuccess: () => resetForm(),
            onError: (err) => console.error('Erro ao excluir:', err),
        });
    }
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
    <Head title="Lançar Métricas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-2xl text-slate-900 dark:text-white tracking-tight leading-tight transition-colors">
                        Lançar Métricas
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors">
                        Registre e acompanhe metas, custos e indicadores operacionais.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

            <!-- Banner Dinâmico -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
            >
                <div v-if="hasCreatedNewMetrica" class="p-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Lançamento realizado com sucesso!</h4>
                            <p class="text-xs text-emerald-100">Deseja acompanhar os novos consolidados diretamente no painel?</p>
                        </div>
                    </div>

                    <Link
                        :href="route('dashboard')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white text-emerald-700 font-bold text-xs uppercase tracking-wider hover:bg-emerald-50 active:scale-95 transition-all shadow-md shrink-0"
                    >
                        <span>Visualizar Métricas</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>
                </div>
            </Transition>
            
            <!-- Card Formulário -->
            <MetricFormCard
                :form="form"
                :is-editing="isEditing"
                :is-gestor="props.isGestor"
                :usuarios="props.usuarios"
                :is-horas-excedidas="isHorasExcedidas"
                :is-nps-invalido="isNpsInvalido"
                :limite-horas-periodo="limiteHorasPeriodo"
                @submit="submit"
                @cancel="resetForm"
            />

            <!-- Card Tabela de Histórico -->
            <MetricTable 
                :metricas="props.metricas"
                :is-gestor="props.isGestor"
                :current-user-id="props.currentUserId"
                @edit="editMetrica"
                @delete="deleteMetrica"
            />
        </div>
    </AuthenticatedLayout>
</template>

<style>
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(7%) sepia(21%) saturate(3474%) hue-rotate(192deg) brightness(96%) contrast(96%) !important;
}

.dark input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: brightness(0) invert(1) !important;
    opacity: 1 !important;
}
</style>