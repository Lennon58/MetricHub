<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

// Estado reativo para o modo escuro
const isDark = ref(false);

// Alterna o tema e salva a preferência no localStorage
const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

// Sincroniza o estado inicial com o tema atual do HTML ao carregar a página
onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
});
</script>

<template>
    <Head title="Bem-vindo ao MetricHub" />

    <div class="min-h-screen bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-200">
        <!-- Header / Navbar -->
        <header class="border-b border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50 backdrop-blur sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Branding com o Favicon como Imagem -->
                <div class="flex items-center space-x-3">
                    <img src="/favicon.ico" alt="MetricHub Logo" class="w-9 h-9 rounded-xl object-contain shadow-sm" />
                    <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">MetricHub</span>
                </div>

                <!-- Navigation Auth Links + Botão de Tema -->
                <div class="flex items-center space-x-4">
                    <!-- Botão de Alternância de Tema -->
                    <button
                        @click="toggleDarkMode"
                        type="button"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition"
                        title="Alternar Modo Escuro/Claro"
                    >
                        <!-- Ícone do Sol (Visível no modo escuro) -->
                        <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Ícone da Lua (Visível no modo claro) -->
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <nav v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition"
                        >
                            Acessar Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-sm font-medium text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white transition"
                            >
                                Entrar
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-sm"
                            >
                                Criar Conta
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 text-center">
            <span class="inline-flex items-center px-3 py-2 rounded-full text-s font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 mb-6">
                Painel de Métricas & Indicadores
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white max-w-4xl mx-auto leading-tight">
                Acompanhe a evolução do seu time em <span class="text-indigo-600 dark:text-indigo-400">tempo real</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                O MetricHub centraliza suas métricas e dados cruciais em um painel simples, ágil e visual. Tome decisões baseadas em dados atualizados.
            </p>
            
            <div class="mt-10 flex items-center justify-center gap-4">
                <Link
                    v-if="canRegister && !$page.props.auth.user"
                    :href="route('register')"
                    class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-base shadow-lg hover:shadow-indigo-500/20 transition"
                >
                    Faça seu Cadastro
                </Link>
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-base shadow-lg transition"
                >
                    Ir para meu Dashboard
                </Link>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-200 dark:border-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xl mb-4">
                        📊
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Métricas em Tempo Real</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Visualize o desempenho de gestores e colaboradores com gráficos e indicadores precisos.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xl mb-4">
                        🛡️
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Acesso Seguro</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Controle de acesso granular baseado em perfis (Gestor vs Colaborador) com verificação de e-mail.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xl mb-4">
                        ⚡
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Performance & Agilidade</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Construído com Laravel 11 e Vue.js 3 para uma experiência fluida de Single Page Application (SPA).
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-gray-200 dark:border-gray-800 py-8 text-center text-xs text-gray-500 dark:text-gray-400">
            <p>MetricHub Analytics &copy; {{ new Date().getFullYear() }} — Todos os direitos reservados.</p>
        </footer>
    </div>
</template>