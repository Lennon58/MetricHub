<script setup>
import { ref, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import DarkModeToggle from "@/Components/DarkModeToggle.vue";
import { Link, usePage } from "@inertiajs/vue3";

// LÊ O ESTADO SALVO NO NAVEGADOR
const isSidebarOpen = ref(true);
const isMobileOpen = ref(false);

onMounted(() => {
    const savedState = localStorage.getItem("sidebar_open");
    if (savedState !== null) {
        isSidebarOpen.value = savedState === "true";
    }
});

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
    // SALVA A PREFERÊNCIA PARA NÃO RESETAR AO NAVEGAR
    localStorage.setItem("sidebar_open", isSidebarOpen.value);
};

const user = usePage().props.auth.user;
</script>

<template>
    <div
        class="min-h-screen bg-gray-100 dark:bg-gray-900 flex transition-colors duration-200"
    >
        <!-- BACKDROP MOBILE -->
        <div
            v-if="isMobileOpen"
            @click="isMobileOpen = false"
            class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm sm:hidden"
        ></div>

        <!-- SIDEBAR LATERAL -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700/80 flex flex-col justify-between transition-all duration-300 shadow-lg sm:shadow-none',
                isSidebarOpen ? 'w-64' : 'w-20',
                isMobileOpen
                    ? 'translate-x-0'
                    : '-translate-x-full sm:translate-x-0',
            ]"
        >
            <!-- TOP SECTION -->
            <div class="flex flex-col">
                <!-- CABEÇALHO / LOGO -->
                <div
                    :class="[
                        'h-16 flex items-center border-b border-gray-100 dark:border-gray-700/60 px-4 transition-all duration-300',
                        isSidebarOpen ? 'justify-between' : 'justify-center',
                    ]"
                >
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center gap-3 shrink-0"
                    >
                        <ApplicationLogo
                            class="block h-8 w-8 fill-current text-indigo-600 dark:text-indigo-400 shrink-0"
                        />
                        <span
                            v-if="isSidebarOpen"
                            class="font-bold text-gray-800 dark:text-gray-100 text-base tracking-tight whitespace-nowrap"
                        >
                            MetricHub
                        </span>
                    </Link>

                    <!-- APENAS ESTE BOTÃO CONTROLA O TOGGLE DA SIDEBAR -->
                    <button
                        type="button"
                        @click.stop="toggleSidebar"
                        class="hidden sm:flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors shrink-0"
                        :title="
                            isSidebarOpen ? 'Recolher Menu' : 'Expandir Menu'
                        "
                    >
                        <svg
                            class="w-5 h-5 transform transition-transform duration-300"
                            :class="{ 'rotate-180': !isSidebarOpen }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                            />
                        </svg>
                    </button>
                </div>

                <!-- MENU DE NAVEGAÇÃO -->
                <nav class="p-3 space-y-1.5">
                    <!-- Dashboard -->
                    <Link
                        :href="route('dashboard')"
                        :class="[
                            'flex items-center gap-3 py-3 rounded-xl font-semibold text-sm transition-all duration-150',
                            isSidebarOpen ? 'px-3.5' : 'justify-center px-0',
                            route().current('dashboard')
                                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700/50',
                        ]"
                        :title="!isSidebarOpen ? 'Dashboard' : ''"
                    >
                        <svg
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                        <span v-if="isSidebarOpen" class="whitespace-nowrap"
                            >Painel</span
                        >
                    </Link>

                    <!-- Métricas -->
                    <Link
                        :href="route('metricas.index')"
                        :class="[
                            'flex items-center gap-3 py-3 rounded-xl font-semibold text-sm transition-all duration-150',
                            isSidebarOpen ? 'px-3.5' : 'justify-center px-0',
                            route().current('metricas.*')
                                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700/50',
                        ]"
                        :title="!isSidebarOpen ? 'Métricas' : ''"
                    >
                        <svg
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            />
                        </svg>
                        <span v-if="isSidebarOpen" class="whitespace-nowrap"
                            >Lançar Métricas</span
                        >
                    </Link>
                </nav>
            </div>

            <!-- BOTTOM SECTION -->
            <div
                class="p-3 border-t border-gray-100 dark:border-gray-700/60 space-y-2"
            >
                <!-- Alternar Tema -->
                <div
                    :class="[
                        'flex items-center rounded-xl transition-all',
                        isSidebarOpen
                            ? 'justify-between px-3 py-2 bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/40'
                            : 'justify-center p-2',
                    ]"
                >
                    <span
                        v-if="isSidebarOpen"
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400"
                    >
                        Tema
                    </span>
                    <div class="flex items-center justify-center shrink-0">
                        <DarkModeToggle />
                    </div>
                </div>

                <!-- Perfil -->
                <Link
                    :href="route('profile.edit')"
                    :class="[
                        'flex items-center gap-3 py-2 rounded-xl transition-all',
                        isSidebarOpen ? 'px-2.5' : 'justify-center px-0',
                        route().current('profile.edit')
                            ? 'bg-gray-100 dark:bg-gray-700/70'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-700/40',
                    ]"
                    :title="!isSidebarOpen ? user.name : ''"
                >
                    <div
                        class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex items-center justify-center shrink-0"
                    >
                        <img
                            :src="
                                $page.props.auth.user.avatar
                                    ? `/storage/${$page.props.auth.user.avatar}`
                                    : `https://ui-avatars.com/api/?name=${encodeURIComponent($page.props.auth.user.name)}&background=random`
                            "
                            class="w-10 h-10 rounded-xl object-cover border border-slate-700 shadow-sm flex-shrink-0"
                            alt="Avatar"
                        />
                    </div>
                    <div v-if="isSidebarOpen" class="overflow-hidden flex-1">
                        <p
                            class="text-xs font-bold text-gray-800 dark:text-gray-100 truncate leading-tight"
                        >
                            {{ user.name }}
                        </p>
                        <p
                            class="text-[10px] text-gray-400 truncate leading-tight mt-0.5"
                        >
                            {{ user.email }}
                        </p>
                    </div>
                </Link>

                <!-- Logout -->
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    :class="[
                        'w-full flex items-center gap-3 py-2.5 rounded-xl font-semibold text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors',
                        isSidebarOpen ? 'px-3' : 'justify-center px-0',
                    ]"
                    :title="!isSidebarOpen ? 'Sair' : ''"
                >
                    <svg
                        class="w-4 h-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>
                    <span
                        v-if="isSidebarOpen"
                        class="whitespace-nowrap uppercase tracking-wider font-bold"
                        >Sair</span
                    >
                </Link>
            </div>
        </aside>

        <!-- CONTEÚDO PRINCIPAL -->
        <div
            :class="[
                'flex-1 flex flex-col transition-all duration-300 min-w-0',
                isSidebarOpen ? 'sm:ml-64' : 'sm:ml-20',
            ]"
        >
            <div
                class="sm:hidden h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4"
            >
                <button
                    @click="isMobileOpen = !isMobileOpen"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>
                <ApplicationLogo
                    class="h-7 w-auto fill-current text-indigo-600 dark:text-indigo-400"
                />
            </div>

            <header
                class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 dark:border-gray-700/60"
                v-if="$slots.header"
            >
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
