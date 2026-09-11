<script setup>
import { ref, onMounted } from 'vue';

const theme = ref('system');

const applyTheme = (val) => {
    theme.value = val;
    localStorage.setItem('color-theme', val);

    if (val === 'dark') {
        document.documentElement.classList.add('dark');
    } else if (val === 'light') {
        document.documentElement.classList.remove('dark');
    } else {
        // Opção 'system': segue a preferência do sistema operacional
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
};

onMounted(() => {
    const saved = localStorage.getItem('color-theme');
    if (saved) {
        theme.value = saved;
    }
});
</script>

<template>
    <div class="inline-flex rounded-md shadow-sm border border-gray-200 dark:border-gray-700 p-1 bg-white dark:bg-gray-800">
        <!-- Claro -->
        <button
            type="button"
            @click="applyTheme('light')"
            :class="[
                'px-2.5 py-1 text-xs font-medium rounded transition-colors',
                theme === 'light' 
                    ? 'bg-indigo-600 text-white' 
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
            ]"
            title="Modo Claro"
        >
            ☀️ Claro
        </button>

        <!-- Escuro -->
        <button
            type="button"
            @click="applyTheme('dark')"
            :class="[
                'px-2.5 py-1 text-xs font-medium rounded transition-colors',
                theme === 'dark' 
                    ? 'bg-indigo-600 text-white' 
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
            ]"
            title="Modo Escuro"
        >
            🌙 Escuro
        </button>

        <!-- Sistema -->
        <button
            type="button"
            @click="applyTheme('system')"
            :class="[
                'px-2.5 py-1 text-xs font-medium rounded transition-colors',
                theme === 'system' 
                    ? 'bg-indigo-600 text-white' 
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
            ]"
            title="Padrão do Sistema"
        >
            💻 Sistema
        </button>
    </div>
</template>