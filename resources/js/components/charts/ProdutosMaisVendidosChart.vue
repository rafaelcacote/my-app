<script setup lang="ts">
import { computed } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';
import { Bar } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

interface ProdutosMaisVendidosProps {
    data: Array<{
        nome: string;
        quantidade_vendida: number;
        total_vendido: number;
    }>;
}

const props = defineProps<ProdutosMaisVendidosProps>();

const chartData = computed(() => ({
    labels: props.data.map(item => {
        // Trunca nomes muito longos
        return item.nome.length > 20 ? item.nome.substring(0, 20) + '...' : item.nome;
    }),
    datasets: [
        {
            label: 'Quantidade Vendida',
            data: props.data.map(item => item.quantidade_vendida),
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)',
                'rgba(14, 165, 233, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(251, 146, 60, 0.8)',
                'rgba(220, 38, 127, 0.8)',
            ],
            borderColor: [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)',
                'rgb(139, 92, 246)',
                'rgb(236, 72, 153)',
                'rgb(14, 165, 233)',
                'rgb(34, 197, 94)',
                'rgb(251, 146, 60)',
                'rgb(220, 38, 127)',
            ],
            borderWidth: 1,
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Quantidade Vendida'
            }
        },
        x: {
            title: {
                display: true,
                text: 'Produtos'
            }
        }
    },
    plugins: {
        legend: {
            position: 'top' as const,
        },
        title: {
            display: true,
            text: 'Top 10 Produtos Mais Vendidos'
        },
        tooltip: {
            callbacks: {
                afterLabel: function(context: any) {
                    const index = context.dataIndex;
                    const produto = props.data[index];
                    return [
                        `Total: ${new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }).format(produto.total_vendido)}`
                    ];
                }
            }
        }
    }
};
</script>

<template>
    <div class="h-80 w-full">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>
