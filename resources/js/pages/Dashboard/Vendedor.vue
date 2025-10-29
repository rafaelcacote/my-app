<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Progress } from '@/components/ui/progress';
import { 
    TrendingUp, 
    DollarSign, 
    ShoppingCart, 
    Target,
    Calendar,
    Award,
    Package,
    TrendingDown
} from 'lucide-vue-next';

interface DashboardVendedorProps {
    metricas: {
        minhasVendas: number;
        quantidadeVendas: number;
        ticketMedio: number;
        comissaoEstimada: number;
        percentualComissao: number;
        metaMensal: number;
        percentualMeta: number;
    };
    vendasRecentes: Array<{
        id: number;
        numero_venda: string;
        cliente_nome: string;
        loja_nome: string;
        total: number;
        data_venda: string;
        forma_pagamento: string;
    }>;
    produtosMaisVendidos: Array<{
        produto_nome: string;
        quantidade_vendida: number;
        total_vendido: number;
    }>;
    vendasPorDia: Array<{
        data: string;
        total: number;
        quantidade: number;
    }>;
    periodo: {
        inicio: string;
        fim: string;
    };
}

const props = defineProps<DashboardVendedorProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Meu Dashboard',
        href: '#',
    },
];

// Formatação de valores monetários
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};

// Formatação de data
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-BR');
};

// Labels para formas de pagamento
const getFormaPagamentoLabel = (forma: string) => {
    const labels: Record<string, string> = {
        'dinheiro': 'Dinheiro',
        'cartao_debito': 'Cartão de Débito',
        'cartao_credito': 'Cartão de Crédito',
        'pix': 'PIX',
        'boleto': 'Boleto',
        'transferencia': 'Transferência'
    };
    return labels[forma] || forma;
};

// Cores para badges de forma de pagamento
const getFormaPagamentoBadge = (forma: string) => {
    const variants: Record<string, 'default' | 'secondary' | 'outline'> = {
        'pix': 'default',
        'dinheiro': 'secondary',
        'cartao_credito': 'outline',
        'cartao_debito': 'outline',
    };
    return variants[forma] || 'outline';
};

// Status da meta
const statusMeta = computed(() => {
    if (props.metricas.percentualMeta >= 100) return { color: 'text-green-600', text: 'Meta Atingida!' };
    if (props.metricas.percentualMeta >= 80) return { color: 'text-blue-600', text: 'Quase lá!' };
    if (props.metricas.percentualMeta >= 50) return { color: 'text-yellow-600', text: 'No caminho!' };
    return { color: 'text-orange-600', text: 'Continue trabalhando!' };
});
</script>

<template>
    <Head title="Meu Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Meu Dashboard</h1>
                    <p class="text-muted-foreground">
                        Período: {{ formatDate(periodo.inicio) }} - {{ formatDate(periodo.fim) }}
                    </p>
                </div>
                <Button variant="outline" size="sm">
                    <Calendar class="mr-2 h-4 w-4" />
                    Alterar Período
                </Button>
            </div>

            <!-- Métricas Principais -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total de Vendas -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Minhas Vendas</CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(metricas.minhasVendas) }}</div>
                        <p class="text-xs text-muted-foreground">
                            No período selecionado
                        </p>
                    </CardContent>
                </Card>

                <!-- Quantidade de Vendas -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Vendas Realizadas</CardTitle>
                        <ShoppingCart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metricas.quantidadeVendas }}</div>
                        <p class="text-xs text-muted-foreground">
                            Vendas concluídas
                        </p>
                    </CardContent>
                </Card>

                <!-- Ticket Médio -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Ticket Médio</CardTitle>
                        <Target class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(metricas.ticketMedio) }}</div>
                        <p class="text-xs text-muted-foreground">
                            Por venda realizada
                        </p>
                    </CardContent>
                </Card>

                <!-- Comissão Estimada -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Comissão Estimada</CardTitle>
                        <Award class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ formatCurrency(metricas.comissaoEstimada) }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ metricas.percentualComissao }}% sobre vendas
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Meta Mensal -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <Target class="h-5 w-5" />
                                Meta Mensal
                            </CardTitle>
                            <CardDescription>
                                Acompanhe seu progresso em relação à meta do mês
                            </CardDescription>
                        </div>
                        <Badge :variant="metricas.percentualMeta >= 100 ? 'default' : 'secondary'" class="text-base px-3 py-1">
                            {{ metricas.percentualMeta.toFixed(1) }}%
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <Progress :model-value="metricas.percentualMeta" class="h-3" />
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <span class="font-medium">Vendido:</span>
                                <span class="ml-2">{{ formatCurrency(metricas.minhasVendas) }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Meta:</span>
                                <span class="ml-2">{{ formatCurrency(metricas.metaMensal) }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Falta:</span>
                                <span class="ml-2" :class="metricas.percentualMeta >= 100 ? 'text-green-600' : 'text-orange-600'">
                                    {{ formatCurrency(Math.max(0, metricas.metaMensal - metricas.minhasVendas)) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center">
                            <component 
                                :is="metricas.percentualMeta >= 100 ? TrendingUp : TrendingDown" 
                                class="mr-2 h-4 w-4"
                                :class="statusMeta.color"
                            />
                            <span class="text-sm font-medium" :class="statusMeta.color">
                                {{ statusMeta.text }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Conteúdo Principal -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Vendas Recentes -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <ShoppingCart class="h-5 w-5" />
                            Minhas Vendas Recentes
                        </CardTitle>
                        <CardDescription>
                            Últimas 10 vendas realizadas
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nº Venda</TableHead>
                                    <TableHead>Cliente</TableHead>
                                    <TableHead>Loja</TableHead>
                                    <TableHead>Pagamento</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                    <TableHead class="text-right">Data</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="vendasRecentes.length === 0">
                                    <TableCell colspan="6" class="text-center text-muted-foreground">
                                        Nenhuma venda registrada
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="venda in vendasRecentes" :key="venda.id">
                                    <TableCell class="font-medium">
                                        {{ venda.numero_venda }}
                                    </TableCell>
                                    <TableCell>{{ venda.cliente_nome }}</TableCell>
                                    <TableCell>{{ venda.loja_nome }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="getFormaPagamentoBadge(venda.forma_pagamento)">
                                            {{ getFormaPagamentoLabel(venda.forma_pagamento) }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right font-medium">
                                        {{ formatCurrency(venda.total) }}
                                    </TableCell>
                                    <TableCell class="text-right text-muted-foreground">
                                        {{ venda.data_venda }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Produtos Mais Vendidos -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Package class="h-5 w-5" />
                            Meus Produtos Mais Vendidos
                        </CardTitle>
                        <CardDescription>
                            Top 5 produtos que você mais vendeu
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-if="produtosMaisVendidos.length === 0" class="text-center text-muted-foreground py-4">
                                Nenhum produto vendido no período
                            </div>
                            <div v-for="(produto, index) in produtosMaisVendidos" :key="produto.produto_nome" class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Badge variant="outline" class="w-6 h-6 flex items-center justify-center text-xs">
                                        {{ index + 1 }}
                                    </Badge>
                                    <span class="text-sm font-medium">{{ produto.produto_nome }}</span>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-sm font-medium">{{ formatCurrency(produto.total_vendido) }}</span>
                                    <span class="text-xs text-muted-foreground">
                                        {{ produto.quantidade_vendida }} unidades
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Vendas por Dia -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Vendas por Dia
                        </CardTitle>
                        <CardDescription>
                            Desempenho diário no período
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3 max-h-[300px] overflow-y-auto">
                            <div v-if="vendasPorDia.length === 0" class="text-center text-muted-foreground py-4">
                                Nenhuma venda no período
                            </div>
                            <div v-for="dia in vendasPorDia" :key="dia.data" class="flex items-center justify-between border-b pb-2">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium">{{ formatDate(dia.data) }}</span>
                                    <span class="text-xs text-muted-foreground">{{ dia.quantidade }} vendas</span>
                                </div>
                                <div class="text-sm font-bold">
                                    {{ formatCurrency(dia.total) }}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

