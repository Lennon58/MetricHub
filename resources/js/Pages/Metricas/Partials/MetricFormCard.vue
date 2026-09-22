<script setup>
import InputName from './InputName.vue';
import InputDate from './InputDate.vue';
import InputObs from './InputObs.vue';
import MetricTypeValueField from './MetricTypeValueField.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
    isGestor: {
        type: Boolean,
        default: false,
    },
    usuarios: {
        type: Array,
        default: () => [],
    },
    isHorasExcedidas: {
        type: Boolean,
        default: false,
    },
    isNpsInvalido: {
        type: Boolean,
        default: false,
    },
    limiteHorasPeriodo: {
        type: Number,
        default: null,
    },
});

defineEmits(['submit', 'cancel']);
</script>

<template>
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

            <form @submit.prevent="$emit('submit')" class="mt-6 space-y-6">
                <!-- 1. Nome / Usuário Responsável -->
                <div v-if="isGestor">
                    <InputName v-model="form.user_id" :usuarios="usuarios" />
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
                        @click="$emit('cancel')"
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
</template>