<script setup lang="ts">
defineProps<{
    modelValue: string;
}>();

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
            Observação
        </label>
        <textarea
            :value="modelValue"
            @input="emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
            rows="3"
            placeholder="Adicione detalhes relevantes..."
            class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs font-medium p-3 focus:ring-2 focus:ring-indigo-500 resize-none"
        ></textarea>
    </div>
</template>