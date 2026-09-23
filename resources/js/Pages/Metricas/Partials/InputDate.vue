<script setup lang="ts">
defineProps<{
    startDate: string;
    endDate?: string;
}>();

const emit = defineEmits(["update:startDate", "update:endDate"]);

// Data de hoje no formato YYYY-MM-DD (formato exigido pelo input type="date")
const hoje = new Date().toLocaleDateString('sv-SE');
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                Data Início
            </label>
            <input
                type="date"
                :value="startDate"
                :max="hoje"
                @input="emit('update:startDate', ($event.target as HTMLInputElement).value)"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-indigo-500"
            />
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                Data Fim (Opcional)
            </label>
            <input
                type="date"
                :value="endDate"
                :max="hoje"
                @input="emit('update:endDate', ($event.target as HTMLInputElement).value)"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-indigo-500"
            />
        </div>
    </div>
</template>