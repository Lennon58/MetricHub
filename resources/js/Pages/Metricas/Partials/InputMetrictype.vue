<script setup lang="ts">
defineProps<{
    modelValue: string;
    tipos: string[];
}>();

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <div>
        <label
            class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2"
        >
            Tipo de Métrica
        </label>
        <select
            :value="modelValue"
            @input="
                emit(
                    'update:modelValue',
                    ($event.target as HTMLSelectElement).value,
                )
            "
            class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs font-medium py-2.5 px-3 focus:ring-2 focus:ring-indigo-500"
        >
            <option value="Meta de Vendas (R$)">Meta de Vendas (R$)</option>
            <option value="Custo Operacional (R$)">Custo Operacional (R$)</option>
            <option value="Horas Trabalhadas">Horas Trabalhadas</option>
            <option value="Satisfação (NPS / 0 a 10)">Satisfação (NPS / 0 a 10)</option>
        </select>
    </div>
</template>
