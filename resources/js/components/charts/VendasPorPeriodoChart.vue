<script setup lang="ts">
import { computed } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
} from 'chart.js';
import { Line, Bar, Doughnut } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement
);

interface VendasPorPeriodoProps {
    data: Array<{
        data: string;
        total: number;
        quantidade: number;
    }>;
}

const props = defineProps<VendasPorPeriodoProps>();

const chartData = computed(() => ({
    labels: props.data.map(item => {
        const date = new Date(item.data);
        return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
    }),
    datasets: [
        {
            label: 'Valor (R$)',
            data: props.data.map(item => item.total),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            yAxisID: 'y',
        },
        {
            label: 'Quantidade',
            data: props.data.map(item => item.quantidade),
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            yAxisID: 'y1',
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: 'index' as const,
        intersect: false,
    },
    scales: {
        x: {
            display: true,
            title: {
                display: true,
                text: 'Data'
            }
        },
        y: {
            type: 'linear' as const,
            display: true,
            position: 'left' as const,
            title: {
                display: true,
                text: 'Valor (R$)'
            },
            ticks: {
                callback: function(value: any) {
                    return new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                }
            }
        },
        y1: {
            type: 'linear' as const,
            display: true,
            position: 'right' as const,
            title: {
                display: true,
                text: 'Quantidade'
            },
            grid: {
                drawOnChartArea: false,
            },
        },
    },
    plugins: {
        legend: {
            position: 'top' as const,
        },
        title: {
            display: true,
            text: 'Vendas por Período'
        },
        tooltip: {
            callbacks: {
                label: function(context: any) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.datasetIndex === 0) {
                        label += new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }).format(context.parsed.y);
                    } else {
                        label += context.parsed.y;
                    }
                    return label;
                }
            }
        }
    }
};
</script>

<template>
    <div class="h-80 w-full">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>
