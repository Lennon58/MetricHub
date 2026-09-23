<script setup lang="ts">
const props = defineProps<{
    startDate: string;
    endDate: string;
}>();

const emit = defineEmits(["update:startDate", "update:endDate", "filter"]);

const hoje = new Date().toISOString().split("T")[0];
</script>

<template>
    <div class="flex flex-wrap items-center gap-3 bg-white dark:bg-[#0F172A] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2.5 px-4 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] transition-all">
        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-xl px-3 py-1 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">De:</span>
            <input
                type="date"
                :value="startDate"
                :max="hoje"
                @input="emit('update:startDate', ($event.target as HTMLInputElement).value)"
                class="bg-transparent text-xs text-slate-900 dark:text-white focus:outline-none border-none p-0 cursor-pointer"
            />
        </div>

        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-xl px-3 py-1 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Até:</span>
            <input
                type="date"
                :value="endDate"
                :max="hoje"
                @input="emit('update:endDate', ($event.target as HTMLInputElement).value)"
                class="bg-transparent text-xs text-slate-900 dark:text-white focus:outline-none border-none p-0 cursor-pointer"
            />
        </div>

        <button
            @click="emit('filter')"
            class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold tracking-wide uppercase transition-all shadow-sm"
        >
            Filtrar
        </button>
    </div>
</template>