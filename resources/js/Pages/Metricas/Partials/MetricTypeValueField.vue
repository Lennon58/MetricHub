<script setup>
import InputMetrictype from './InputMetrictype.vue';
import InputValue from './InputValue.vue';

defineProps({
    tipo: {
        type: String,
        default: '',
    },
    valor: {
        type: [String, Number],
        default: '',
    },
    errorsTipo: {
        type: String,
        default: '',
    },
    errorsValor: {
        type: String,
        default: '',
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

defineEmits(['update:tipo', 'update:valor']);
</script>

<template>
    <div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="sm:col-span-2">
            <InputMetrictype :model-value="tipo" @update:model-value="$emit('update:tipo', $event)" />
            <span v-if="errorsTipo" class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5 block">
                {{ errorsTipo }}
            </span>
        </div>

        <div>
            <InputValue
                :model-value="valor"
                @update:model-value="$emit('update:valor', $event)"
                :tipo="tipo"
            />
            <span v-if="errorsValor" class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5 block">
                {{ errorsValor }}
            </span>
        </div>
    </div>

    <div v-if="isHorasExcedidas" class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 flex items-start gap-3">
        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-xs font-medium text-rose-700 dark:text-rose-300">
            Atenção: O total digitado (<strong>{{ valor }}h</strong>) excede o limite do período selecionado (<strong>{{ limiteHorasPeriodo }}h</strong>).
        </p>
    </div>

    <div v-if="isNpsInvalido" class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 flex items-start gap-3">
        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-xs font-medium text-rose-700 dark:text-rose-300">
            A nota do NPS precisa estar dentro do intervalo permitido (entre 0 e 10).
        </p>
    </div>
    </div>
</template>