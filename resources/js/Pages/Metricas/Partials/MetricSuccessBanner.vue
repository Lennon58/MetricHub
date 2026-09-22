<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['close']);
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-4 opacity-0 scale-95"
        enter-to-class="transform translate-y-0 opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform translate-y-0 opacity-100 scale-100"
        leave-to-class="transform -translate-y-4 opacity-0 scale-95"
    >
        <div 
            v-if="show" 
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 p-5 sm:p-6 text-white shadow-xl shadow-emerald-500/20 border border-emerald-400/30"
        >
            <!-- Elemento decorativo de brilho de fundo (Glow Effect) -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <!-- Ícone + Conteúdo do Texto -->
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/15 dark:bg-white/10 rounded-2xl backdrop-blur-md border border-white/20 shadow-inner shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-base tracking-tight">
                            Lançamento realizado com sucesso!
                        </h4>
                        <p class="text-xs sm:text-sm text-emerald-100/90 mt-0.5 font-medium">
                            Deseja acompanhar os novos consolidados diretamente no painel?
                        </p>
                    </div>
                </div>

                <!-- Ações: Botão de Ação Principal e Botão Fechar -->
                <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-white/10">
                    <Link
                        :href="route('dashboard')"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white text-emerald-800 font-bold text-xs uppercase tracking-wider hover:bg-emerald-50 active:scale-95 transition-all shadow-md"
                    >
                        <span>Visualizar Métricas</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>

                    <!-- Botão de Fechar Banner -->
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer"
                        title="Fechar alerta"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>