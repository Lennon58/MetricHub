<script setup lang="ts">
defineProps<{
    modelValue: [number, string] | string | number;
    usuarios: Array<{ id: number; name: string }>;
}>();

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
            Colaborador
        </label>
        <select
            :value="modelValue"
            @input="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs font-medium py-2.5 px-3 focus:ring-2 focus:ring-indigo-500"
        >
            <option value="" disabled>Selecione um usuário...</option>
            <option v-for="user in usuarios" :key="user.id" :value="user.id">
                {{ user.name }}
            </option>
        </select>
    </div>
</template>