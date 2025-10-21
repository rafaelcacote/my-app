<script setup lang="ts">
import { computed } from 'vue';
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend,
} from 'chart.js';
import { Doughnut } from 'vue-chartjs';

ChartJS.register(
    ArcElement,
    Tooltip,
    Legend
);

interface VendasPorCategoriaProps {
    data: Array<{
        nome: string;
        total_vendido: number;
        vendas: number;
    }>;
}

const props = defineProps<VendasPorCategoriaProps>();

const chartData = computed(() => {
    const labels = props.data.map(item => item.nome);
    const values = props.data.map(item => item.total_vendido);
    
    return {
        labels,
        datasets: [
            {
                data: values,
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
                borderWidth: 2,
            }
        ]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
        },
        title: {
            display: true,
            text: 'Vendas por Categoria'
        },
        tooltip: {
            callbacks: {
                label: function(context: any) {
                    const index = context.dataIndex;
                    const categoria = props.data[index];
                    const total = props.data.reduce((sum, item) => sum + item.total_vendido, 0);
                    const percentage = ((categoria.total_vendido / total) * 100).toFixed(1);
                    
                    return [
                        `${context.label}: ${new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }).format(context.parsed)}`,
                        `(${percentage}% do total)`,
                        `${categoria.vendas} vendas`
                    ];
                }
            }
        }
    }
};
</script>

<template>
    <div class="h-80 w-full">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
</template>
