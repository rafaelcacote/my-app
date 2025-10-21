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

interface StatusEstoqueProps {
    data: {
        sem_estoque: number;
        estoque_baixo: number;
        estoque_medio: number;
        estoque_alto: number;
    };
}

const props = defineProps<StatusEstoqueProps>();

const chartData = computed(() => {
    const labels = ['Sem Estoque', 'Estoque Baixo', 'Estoque Médio', 'Estoque Alto'];
    const values = [
        props.data.sem_estoque,
        props.data.estoque_baixo,
        props.data.estoque_medio,
        props.data.estoque_alto
    ];
    
    return {
        labels,
        datasets: [
            {
                data: values,
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',   // Sem estoque - vermelho
                    'rgba(245, 158, 11, 0.8)',  // Estoque baixo - amarelo
                    'rgba(59, 130, 246, 0.8)',  // Estoque médio - azul
                    'rgba(16, 185, 129, 0.8)',  // Estoque alto - verde
                ],
                borderColor: [
                    'rgb(239, 68, 68)',
                    'rgb(245, 158, 11)',
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
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
            text: 'Distribuição do Estoque'
        },
        tooltip: {
            callbacks: {
                label: function(context: any) {
                    const total = context.dataset.data.reduce((sum: number, value: number) => sum + value, 0);
                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                    return `${context.label}: ${context.parsed} (${percentage}%)`;
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
