<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { TrendingUp, TrendingDown } from 'lucide-vue-next';

interface MetricCardProps {
    title: string;
    value: string | number;
    description?: string;
    icon: any; // Componente de ícone do Lucide
    trend?: {
        value: number;
        isPositive: boolean;
    };
    variant?: 'default' | 'success' | 'warning' | 'destructive';
}

const props = withDefaults(defineProps<MetricCardProps>(), {
    variant: 'default'
});

const getVariantClasses = () => {
    const variants = {
        default: '',
        success: 'text-green-600',
        warning: 'text-orange-600',
        destructive: 'text-red-600'
    };
    return variants[props.variant];
};
</script>

<template>
    <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">{{ title }}</CardTitle>
            <component :is="icon" class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
            <div class="text-2xl font-bold" :class="getVariantClasses()">
                {{ value }}
            </div>
            <div v-if="trend" class="flex items-center text-xs text-muted-foreground">
                <component 
                    :is="trend.isPositive ? TrendingUp : TrendingDown" 
                    class="mr-1 h-3 w-3"
                    :class="trend.isPositive ? 'text-green-500' : 'text-red-500'"
                />
                {{ trend.value >= 0 ? '+' : '' }}{{ trend.value.toFixed(1) }}% vs período anterior
            </div>
            <p v-else-if="description" class="text-xs text-muted-foreground">
                {{ description }}
            </p>
        </CardContent>
    </Card>
</template>
