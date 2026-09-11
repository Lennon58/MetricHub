<script setup lang="ts">
import { useForm, Head, Link, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed } from "vue";

interface User {
    id: number;
    name: string;
}

interface MetricaManual {
    id: number;
    user_id: number;
    tipo: string;
    valor: number;
    data_inicio?: string;
    data_fim?: string;
    observacao?: string;
}

const props = defineProps<{
    metricaManual: MetricaManual;
    usuarios: User[];
    isGestor: boolean;
}>();

const page = usePage();
// Captura a mensagem flash vinda do backend após atualizar com sucesso
const flash = computed(() => (page.props.flash as any) || {});

const form = useForm({
    user_id: props.metricaManual.user_id,
    tipo: props.metricaManual.tipo,
    valor: props.metricaManual.valor,
    data_inicio: props.metricaManual.data_inicio || "",
    data_fim: props.metricaManual.data_fim || "",
    observacao: props.metricaManual.observacao || "",
});

const submit = () => {
    // Certifique-se de que o nome da rota confere com o gerado no seu web.php (ex: metricas-manuais.update)
    form.put(route("metricas-manuais.update", props.metricaManual.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Editar Métrica" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-extrabold text-2xl tracking-tight leading-tight text-slate-900 dark:text-white transition-colors">
                Editar Métrica
            </h2>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">
            
            <!-- CARD DE SUCESSO APÓS ATUALIZAR (IDÊNTICO AO DE LANÇAMENTO) -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
            >
                <div
                    v-if="flash.success"
                    class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 sm:p-5 rounded-2xl bg-emerald-600 dark:bg-emerald-700 text-white shadow-lg border border-emerald-500/50"
                >
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-white/10 backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base">Edição realizada com sucesso!</h4>
                            <p class="text-xs text-emerald-100">Deseja acompanhar os novos consolidados diretamente no painel?</p>
                        </div>
                    </div>
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-extrabold tracking-wide uppercase transition-all shadow-sm whitespace-nowrap"
                    >
                        Visualizar Métricas
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </Link>
                </div>
            </transition>

            <!-- Formulário de Edição -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl transition-all">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Tipo de Métrica *
                        </label>
                        <input
                            type="text"
                            v-model="form.tipo"
                            class="w-full rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Valor *
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            v-model="form.valor"
                            class="w-full rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <Link
                            :href="route('metricas-manuais.index')"
                            class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold tracking-wide uppercase transition-all shadow-md"
                        >
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>