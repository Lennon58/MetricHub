<script setup>
import { computed, h } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    tipo: {
        type: String,
        default: 'Meta de Vendas (R$)',
    },
    isNpsInvalido: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const RequiredAsterisk = () => h(
    'span',
    { class: 'relative group cursor-help ml-0.5 select-none text-rose-500 font-bold inline-block' },
    [
        '*',
        h(
            'span',
            { 
                class: 'absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover:flex items-center px-2.5 py-1 text-[11px] font-normal text-white bg-slate-900 dark:bg-slate-700 rounded-md shadow-lg whitespace-nowrap z-50 pointer-events-none transition-all duration-150 normal-case tracking-normal' 
            },
            [
                'Itens marcados com * são obrigatórios',
                h('span', { class: 'absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-slate-900 dark:border-t-slate-700' })
            ]
        )
    ]
);

const inputBaseClass = computed(() => {
    return 'w-full rounded-xl text-sm shadow-sm transition-colors bg-[#FFFFFF] dark:bg-[#020617] text-slate-900 dark:text-slate-100 border-slate-200 dark:border-slate-700 focus:border-indigo-500 focus:ring-indigo-500';
});

// 1. Moeda: Remove tudo que não for dígito e formata
const handleMoedaInput = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    if (value === '') {
        emit('update:modelValue', '');
        return;
    }
    const numberValue = (parseInt(value, 10) / 100).toFixed(2);
    const parts = numberValue.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    const formatted = parts.join(',');
    emit('update:modelValue', formatted);
};

// 2. Horas: Remove tudo que não for dígito e formata como HH:MM
const handleHorasInput = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    if (value.length > 4) {
        value = value.slice(0, 4);
    }
    if (value.length >= 3) {
        const horas = value.slice(0, value.length - 2);
        const minutos = value.slice(-2);
        value = `${horas}:${minutos}`;
    }
    emit('update:modelValue', value);
};

// 3. NPS/Pontuação: Garante que apenas números inteiros sejam aceitos
const handleNpsInput = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    if (value !== '') {
        let num = parseInt(value, 10);
        // Limita o valor numérico entre 0 e 10 de forma estrita
        if (num > 10) num = 10;
        value = num.toString();
    }
    event.target.value = value; // Atualiza visualmente o input nativo
    emit('update:modelValue', value);
};

const tipoAtual = computed(() => props.tipo);
</script>

<template>
    <div>
        <!-- Caso 1: Meta de Vendas ou Custo (Moeda R$) -->
        <template v-if="tipoAtual.includes('(R$)')">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                Valor <RequiredAsterisk />
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs font-bold">
                    R$
                </div>
                <input
                    key="input-moeda"
                    type="text"
                    inputmode="numeric"
                    placeholder="0,00"
                    :value="props.modelValue"
                    @input="handleMoedaInput"
                    :class="[inputBaseClass, 'pl-10']"
                    required
                />
            </div>
        </template>

        <!-- Caso 2: Horas Trabalhadas -->
        <template v-else-if="tipoAtual === 'Horas Trabalhadas'">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                Total de Horas <RequiredAsterisk />
            </label>
            <div class="relative">
                <input
                    key="input-horas"
                    type="text"
                    inputmode="numeric"
                    placeholder="HH:MM"
                    :value="props.modelValue"
                    @input="handleHorasInput"
                    :class="[inputBaseClass, 'pr-12']"
                    required
                />
                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 text-xs font-bold">
                    h
                </div>
            </div>
        </template>

        <!-- Caso 3: Satisfação (NPS / 0 a 10) -->
        <template v-else>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                Pontuação (0 - 10) <RequiredAsterisk />
            </label>
            <div class="relative">
                <input
                    key="input-nps"
                    type="text"
                    inputmode="numeric"
                    placeholder="10"
                    :value="props.modelValue"
                    @input="handleNpsInput"
                    :class="[
                        inputBaseClass,
                        'pr-12',
                        isNpsInvalido ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500 text-rose-600' : ''
                    ]"
                    required
                />
                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 text-xs font-bold">
                    pts
                </div>
            </div>
        </template>
    </div>
</template>