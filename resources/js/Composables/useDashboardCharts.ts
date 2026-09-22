import { computed, type Ref } from "vue";

interface User {
    id: number;
    name: string;
    role?: string;
}

interface Metrica {
    id: number;
    tipo: string;
    valor: number;
    data_inicio?: string;
    data_fim?: string;
    created_at?: string;
    user_id?: number;
    user?: User;
}

interface GraficoUsuarioTipo {
    userId: number;
    userName: string;
    tipo: string;
    titulo: string;
    hasData: boolean;
    series: Array<{ name: string; data: Array<{ x: string; y: number }> }>;
    options: any;
}

const chartColors = ["#6366F1", "#10B981", "#EF4444", "#F59E0B", "#8B5CF6"];

function buildChartOptions(userId: number) {
    return {
        chart: {
            type: "line",
            fontFamily: "Inter, sans-serif",
            toolbar: { show: true, offsetY: -65 },
            background: "transparent",
            zoom: { enabled: false },
        },
        colors: [chartColors[userId % chartColors.length]],
        stroke: { curve: "smooth", width: 3 },
        markers: { size: 6, hover: { size: 8 } },
        dataLabels: { enabled: false },
        xaxis: {
            type: "category",
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: "#64748B",
                },
            },
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            labels: {
                formatter: (val: number) => val.toFixed(0),
                style: {
                    colors: "#64748B",
                },
            },
        },
        grid: { strokeDashArray: 4 },
        legend: { show: false },
        tooltip: { shared: false, intersect: true },
    };
}

function buildDataPoints(metricasFiltradas: Metrica[]) {
    const metricasOrdenadas = [...metricasFiltradas].sort((a, b) => {
        const dateA = new Date(a.data_inicio || a.created_at || 0).getTime();
        const dateB = new Date(b.data_inicio || b.created_at || 0).getTime();
        return dateA - dateB;
    });

    const dataPoints = metricasOrdenadas.map((m) => {
        const rawDate = m.data_inicio || m.created_at;
        const formattedDate = rawDate
            ? new Date(rawDate).toLocaleDateString("pt-BR", {
                  day: "2-digit",
                  month: "2-digit",
              })
            : "S/D";
        return {
            x: `${formattedDate} (#${m.id})`,
            y: Number(m.valor),
        };
    });

    return dataPoints.length > 0 ? [{ x: "Início", y: 0 }, ...dataPoints] : [];
}

export function useDashboardCharts(metricas: Ref<Metrica[] | undefined>) {
    const graficosPorUsuarioETipo = computed<GraficoUsuarioTipo[]>(() => {
        if (!metricas.value || metricas.value.length === 0) return [];

        const tiposUnicos = [...new Set(metricas.value.map((m) => m.tipo))];

        const usuariosMap: Record<number, { id: number; name: string }> = {};
        metricas.value.forEach((m) => {
            if (m.user_id) {
                usuariosMap[m.user_id] = {
                    id: m.user_id,
                    name: m.user?.name || `Usuário #${m.user_id}`,
                };
            }
        });

        const listaGraficos: GraficoUsuarioTipo[] = [];

        Object.values(usuariosMap).forEach((usuario) => {
            tiposUnicos.forEach((tipo) => {
                const metricasFiltradas = metricas.value!.filter(
                    (m) => m.user_id === usuario.id && m.tipo === tipo,
                );

                listaGraficos.push({
                    userId: usuario.id,
                    userName: usuario.name,
                    tipo: tipo,
                    titulo: `${usuario.name} — ${tipo}`,
                    hasData: metricasFiltradas.length > 0,
                    series: [
                        {
                            name: usuario.name,
                            data: buildDataPoints(metricasFiltradas),
                        },
                    ],
                    options: buildChartOptions(usuario.id),
                });
            });
        });

        return listaGraficos;
    });

    return { graficosPorUsuarioETipo };
}