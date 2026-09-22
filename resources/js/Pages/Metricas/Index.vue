<script setup>
import { ref, computed, watch, h } from 'vue';
import { useForm, router, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputName from './Partials/InputName.vue';
import InputDate from './Partials/InputDate.vue';
import InputObs from './Partials/InputObs.vue';
import MetricTable from './Partials/MetricTable.vue';
import MetricTypeValueField from './Partials/MetricTypeValueField.vue';
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
            <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-[0_15px_30px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] overflow-hidden transition-all">
                <div class="p-6 sm:p-8">
                    <header class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-5">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                    {{ isEditing ? 'Editar Registro' : 'Cadastrar Nova Métrica' }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Preencha os campos abaixo para atualizar o sistema de indicadores.
                                </p>
                            </div>
                        </div>

                        <span v-if="isEditing" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Modo Edição
                        </span>
                    </header>

                    <form @submit.prevent="submit" class="mt-6 space-y-6">
                        <!-- 1. Nome / Usuário Responsável -->
                        <div v-if="props.isGestor">
                            <InputName v-model="form.user_id" :usuarios="props.usuarios" />
                            <span v-if="form.errors.user_id" class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5 block">
                                {{ form.errors.user_id }}
                            </span>
                        </div>

                        <!-- 2. Datas (Início e Fim) -->
                        <InputDate 
                            v-model:start-date="form.data_inicio" 
                            v-model:end-date="form.data_fim" 
                        />
                        <div v-if="form.errors.data_inicio || form.errors.data_fim">
                            <span v-if="form.errors.data_inicio" class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5 block">
                                {{ form.errors.data_inicio }}
                            </span>
                            <span v-if="form.errors.data_fim" class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5 block">
                                {{ form.errors.data_fim }}
                            </span>
                        </div>

                        <!-- 3. Tipo de Métrica e Valor integrados -->
                        <MetricTypeValueField
                            v-model:tipo="form.tipo"
                            v-model:valor="form.valor"
                            :errors-tipo="form.errors.tipo"
                            :errors-valor="form.errors.valor"
                            :is-horas-excedidas="isHorasExcedidas"
                            :is-nps-invalido="isNpsInvalido"
                            :limite-horas-periodo="limiteHorasPeriodo"
                        />

                        <!-- 4. Observação -->
                        <div>
                            <InputObs v-model="form.observacao" />
                            <span v-if="form.errors.observacao" class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5 block">
                                {{ form.errors.observacao }}
                            </span>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button
                                v-if="isEditing"
                                type="button"
                                @click="resetForm"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-semibold text-xs text-slate-600 dark:text-slate-300 uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all cursor-pointer"
                            >
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                :disabled="form.processing || isHorasExcedidas || isNpsInvalido"
                                :class="[
                                    'inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-white shadow-lg transition-all cursor-pointer',
                                    (form.processing || isHorasExcedidas || isNpsInvalido)
                                        ? 'bg-slate-300 dark:bg-slate-800 shadow-none cursor-not-allowed opacity-60' 
                                        : 'bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] shadow-indigo-500/20'
                                ]"
                            >
                                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ isEditing ? 'Atualizar Métrica' : 'Salvar Registro' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

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