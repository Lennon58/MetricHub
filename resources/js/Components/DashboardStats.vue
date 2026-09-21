<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

defineProps<{
    metricas?: Array<{
        id: number;
        tipo: string;
        valor: number;
        user_id?: number;
    }>;
}>();

const getMetricaTotal = (metricas: any[], tipo: string) => {
    if (!metricas) return 0;
    const filtradas = metricas.filter((m) => m.tipo === tipo);
    if (filtradas.length === 0) return 0;

    return filtradas.reduce((acc, curr) => {
        let val = curr.valor;

        // Se o valor vier em minutos (ex: 600) ou formato "HH:MM", converte para horas
        if (tipo === "Horas Trabalhadas") {
            if (typeof val === "string" && val.includes(":")) {
                const [horas, minutos] = val.split(":").map(Number);
                val = horas + (minutos || 0) / 60;
            } else if (Number(val) >= 60 && Number(val) % 1 === 0) {
                // Caso o backend guarde minutos no banco (ex: 600 minutos = 10 horas)
                val = Number(val) / 60;
            }
        }

        return acc + Number(val);
    }, 0);
};

const getMetricaMedia = (metricas: any[], tipo: string) => {
    if (!metricas) return "0.0";
    
    // Busca flexível: procura por correspondência exata ou por contiver "NPS"
    const filtradas = metricas.filter(
        (m) => m.tipo === tipo || m.tipo.toLowerCase().includes("nps")
    );
    
    if (filtradas.length === 0) return "0.0";
    
    const sum = filtradas.reduce((acc, curr) => acc + Number(curr.valor), 0);
    return (sum / filtradas.length).toFixed(1);
};
const formatCurrency = (val: number) => {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(val);
};
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Meta de Vendas -->
        <div
            class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-[0_15px_30px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] flex justify-between items-start transition-all">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Meta de Vendas
                </span>
                <div class="mt-3 text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    {{ formatCurrency(getMetricaTotal(metricas || [], "Meta de Vendas (R$)")) }}
                </div>
                <div
                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/50 px-3 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Acumulado no Período
                </div>
            </div>
        </div>

        <!-- Card 2: Custo Operacional -->
        <div
            class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-[0_15px_30px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] flex justify-between items-start transition-all">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Custo Operacional
                </span>
                <div class="mt-3 text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    {{ formatCurrency(getMetricaTotal(metricas || [], "Custo Operacional (R$)")) }}
                </div>
                <div
                    class="mt-3 inline-flex items-center text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/50 px-3 py-1 rounded-full">
                    Total Registrado
                </div>
            </div>
        </div>

        <!-- Card 3: Horas & NPS -->
        <div
            class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-[0_15px_30px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] flex flex-col justify-between transition-all">
            <div class="flex justify-between items-center">
                <div class="flex-1 pr-4 border-r border-slate-200 dark:border-slate-800">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Horas
                    </span>
                    <div class="mt-1 text-2xl font-black text-slate-900 dark:text-white">
                        {{ getMetricaTotal(metricas || [], "Horas Trabalhadas") }}
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">hrs</span>
                    </div>
                </div>
                <div class="flex-1 pl-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        NPS
                    </span>
                    <div class="mt-1 text-2xl font-black text-indigo-600 dark:text-indigo-400">
                        {{ getMetricaMedia(metricas || [], "Satisfação (NPS / 1 a 10)") }}
                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400">/ 10</span>
                    </div>
                </div>
            </div>
            <div class="mt-5 pt-3 border-t border-slate-200 dark:border-slate-800">
                <Link :href="route('metricas.index')"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-xs font-bold text-white transition-all shadow-md active:scale-[0.98]">
                    + Gerenciar Métricas
                </Link>
            </div>
        </div>
    </div>
</template>