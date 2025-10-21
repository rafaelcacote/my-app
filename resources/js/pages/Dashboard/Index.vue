<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { Skeleton } from '@/components/ui/skeleton';
import VendasPorPeriodoChart from '@/components/charts/VendasPorPeriodoChart.vue';
import ProdutosMaisVendidosChart from '@/components/charts/ProdutosMaisVendidosChart.vue';
import StatusEstoqueChart from '@/components/charts/StatusEstoqueChart.vue';
import VendasPorCategoriaChart from '@/components/charts/VendasPorCategoriaChart.vue';
import PeriodoFilter from '@/components/dashboard/PeriodoFilter.vue';
import MetricCard from '@/components/dashboard/MetricCard.vue';
import { 
    TrendingUp, 
    TrendingDown, 
    DollarSign, 
    ShoppingCart, 
    Users, 
    Package, 
    AlertTriangle,
    BarChart3,
    PieChart,
    Calendar,
    Store,
    CreditCard,
    Target
} from 'lucide-vue-next';

interface DashboardProps {
    metricas: {
        totalVendas: number;
        quantidadeVendas: number;
        ticketMedio: number;
        clientesAtivos: number;
        produtosEstoqueBaixo: number;
        totalProdutos: number;
        margemLucroMedia: number;
    };
    vendasPorPeriodo: Array<{
        data: string;
        total: number;
        quantidade: number;
    }>;
    produtosMaisVendidos: Array<{
        nome: string;
        quantidade_vendida: number;
        total_vendido: number;
    }>;
    vendasPorCategoria: Array<{
        nome: string;
        total_vendido: number;
        vendas: number;
    }>;
    vendasPorFormaPagamento: Array<{
        forma_pagamento: string;
        total: number;
        quantidade: number;
    }>;
    statusEstoque: {
        sem_estoque: number;
        estoque_baixo: number;
        estoque_medio: number;
        estoque_alto: number;
    };
    clientesAtivos: Array<{
        nome: string;
        quantidade_vendas: number;
        total_gasto: number;
    }>;
    vendasPorLoja: Array<{
        nome: string;
        total: number;
        quantidade: number;
    }>;
    tendencias: {
        variacao_vendas: number;
        variacao_quantidade: number;
        vendas_atual: number;
        vendas_anterior: number;
        quantidade_atual: number;
        quantidade_anterior: number;
    };
    periodo: {
        inicio: string;
        fim: string;
    };
}

const props = defineProps<DashboardProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Formatação de valores monetários
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};

// Formatação de percentual
const formatPercentage = (value: number) => {
    return `${value >= 0 ? '+' : ''}${value.toFixed(1)}%`;
};

// Formatação de data
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-BR');
};

// Cores para badges de status
const getStatusColor = (status: string) => {
    const colors = {
        'sem_estoque': 'destructive',
        'estoque_baixo': 'destructive',
        'estoque_medio': 'secondary',
        'estoque_alto': 'default'
    };
    return colors[status as keyof typeof colors] || 'default';
};

// Labels para status de estoque
const getStatusLabel = (status: string) => {
    const labels = {
        'sem_estoque': 'Sem Estoque',
        'estoque_baixo': 'Estoque Baixo',
        'estoque_medio': 'Estoque Médio',
        'estoque_alto': 'Estoque Alto'
    };
    return labels[status as keyof typeof labels] || status;
};

// Labels para formas de pagamento
const getFormaPagamentoLabel = (forma: string) => {
    const labels = {
        'dinheiro': 'Dinheiro',
        'cartao_debito': 'Cartão de Débito',
        'cartao_credito': 'Cartão de Crédito',
        'pix': 'PIX',
        'boleto': 'Boleto',
        'transferencia': 'Transferência'
    };
    return labels[forma as keyof typeof labels] || forma;
};

// Computed para calcular totais de estoque
const totalProdutosEstoque = computed(() => {
    return Object.values(props.statusEstoque).reduce((sum, count) => sum + count, 0);
});

// Computed para calcular percentual de cada status de estoque
const getEstoquePercentage = (count: number) => {
    return totalProdutosEstoque.value > 0 ? (count / totalProdutosEstoque.value) * 100 : 0;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="dashboard-container flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
            <!-- Header com período -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                    <p class="text-muted-foreground">
                        Período: {{ formatDate(periodo.inicio) }} - {{ formatDate(periodo.fim) }}
                    </p>
                </div>
                <PeriodoFilter 
                    :periodo-inicio="periodo.inicio" 
                    :periodo-fim="periodo.fim" 
                />
            </div>

            <!-- Métricas Principais -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total de Vendas -->
                <div class="dashboard-metric">
                    <MetricCard
                        title="Total de Vendas"
                        :value="formatCurrency(metricas.totalVendas)"
                        :icon="DollarSign"
                        :trend="{
                            value: tendencias.variacao_vendas,
                            isPositive: tendencias.variacao_vendas >= 0
                        }"
                    />
                </div>

                <!-- Quantidade de Vendas -->
                <div class="dashboard-metric">
                    <MetricCard
                        title="Vendas Realizadas"
                        :value="metricas.quantidadeVendas"
                        :icon="ShoppingCart"
                        :trend="{
                            value: tendencias.variacao_quantidade,
                            isPositive: tendencias.variacao_quantidade >= 0
                        }"
                    />
                </div>

                <!-- Ticket Médio -->
                <div class="dashboard-metric">
                    <MetricCard
                        title="Ticket Médio"
                        :value="formatCurrency(metricas.ticketMedio)"
                        :icon="Target"
                        description="Por venda realizada"
                    />
                </div>

                <!-- Clientes Ativos -->
                <div class="dashboard-metric">
                    <MetricCard
                        title="Clientes Ativos"
                        :value="metricas.clientesAtivos"
                        :icon="Users"
                        description="Cadastrados no sistema"
                    />
                </div>
            </div>

            <!-- Segunda linha de métricas -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Produtos com Estoque Baixo -->
                <MetricCard
                    title="Produtos com Estoque Baixo"
                    :value="metricas.produtosEstoqueBaixo"
                    :icon="AlertTriangle"
                    variant="warning"
                    description="Necessitam reposição"
                />

                <!-- Total de Produtos -->
                <MetricCard
                    title="Total de Produtos"
                    :value="metricas.totalProdutos"
                    :icon="Package"
                    description="Cadastrados no sistema"
                />

                <!-- Margem de Lucro Média -->
                <MetricCard
                    title="Margem de Lucro Média"
                    :value="formatPercentage(metricas.margemLucroMedia)"
                    :icon="BarChart3"
                    description="Sobre produtos ativos"
                />
            </div>

            <!-- Gráficos Principais -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Gráfico de Vendas por Período -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <BarChart3 class="h-5 w-5" />
                            Vendas por Período
                        </CardTitle>
                        <CardDescription>
                            Evolução das vendas ao longo do tempo
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="chart-container">
                            <VendasPorPeriodoChart :data="vendasPorPeriodo" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Gráfico de Status do Estoque -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Package class="h-5 w-5" />
                            Status do Estoque
                        </CardTitle>
                        <CardDescription>
                            Distribuição dos produtos por nível de estoque
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="chart-container">
                            <StatusEstoqueChart :data="statusEstoque" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Gráficos Secundários -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Gráfico de Produtos Mais Vendidos -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <BarChart3 class="h-5 w-5" />
                            Produtos Mais Vendidos
                        </CardTitle>
                        <CardDescription>
                            Top 10 produtos com maior quantidade vendida
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="chart-container">
                            <ProdutosMaisVendidosChart :data="produtosMaisVendidos" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Gráfico de Vendas por Categoria -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <PieChart class="h-5 w-5" />
                            Vendas por Categoria
                        </CardTitle>
                        <CardDescription>
                            Distribuição das vendas por categoria de produto
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="chart-container">
                            <VendasPorCategoriaChart :data="vendasPorCategoria" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Informações Detalhadas -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Vendas por Forma de Pagamento -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <CreditCard class="h-5 w-5" />
                            Vendas por Forma de Pagamento
                        </CardTitle>
                        <CardDescription>
                            Distribuição das vendas por método de pagamento
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="forma in vendasPorFormaPagamento" :key="forma.forma_pagamento" class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">{{ getFormaPagamentoLabel(forma.forma_pagamento) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">{{ formatCurrency(forma.total) }}</span>
                                    <span class="text-xs text-muted-foreground">
                                        ({{ forma.quantidade }} vendas)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Status do Estoque Detalhado -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Package class="h-5 w-5" />
                            Status do Estoque Detalhado
                        </CardTitle>
                        <CardDescription>
                            Distribuição dos produtos por nível de estoque
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="(count, status) in statusEstoque" :key="status" class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Badge :variant="getStatusColor(status)" class="status-badge">
                                        {{ getStatusLabel(status) }}
                                    </Badge>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">{{ count }}</span>
                                    <span class="text-xs text-muted-foreground">
                                        ({{ getEstoquePercentage(count).toFixed(1) }}%)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Tabelas de Dados -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Produtos Mais Vendidos -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <BarChart3 class="h-5 w-5" />
                            Produtos Mais Vendidos
                        </CardTitle>
                        <CardDescription>
                            Top 10 produtos com maior quantidade vendida
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Produto</TableHead>
                                    <TableHead class="text-right">Qtd. Vendida</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(produto, index) in produtosMaisVendidos" :key="produto.nome" class="table-row">
                                    <TableCell>
                                        <div class="flex items-center gap-2">
                                            <Badge variant="outline" class="w-6 h-6 flex items-center justify-center text-xs">
                                                {{ index + 1 }}
                                            </Badge>
                                            <span class="font-medium">{{ produto.nome }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right font-medium">
                                        {{ produto.quantidade_vendida }}
                                    </TableCell>
                                    <TableCell class="text-right font-medium">
                                        {{ formatCurrency(produto.total_vendido) }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Clientes Mais Ativos -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Clientes Mais Ativos
                        </CardTitle>
                        <CardDescription>
                            Top 10 clientes com maior volume de compras
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Cliente</TableHead>
                                    <TableHead class="text-right">Vendas</TableHead>
                                    <TableHead class="text-right">Total Gasto</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(cliente, index) in clientesAtivos" :key="cliente.nome" class="table-row">
                                    <TableCell>
                                        <div class="flex items-center gap-2">
                                            <Avatar class="h-6 w-6">
                                                <AvatarFallback class="text-xs">
                                                    {{ cliente.nome.charAt(0).toUpperCase() }}
                                                </AvatarFallback>
                                            </Avatar>
                                            <span class="font-medium">{{ cliente.nome }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right font-medium">
                                        {{ cliente.quantidade_vendas }}
                                    </TableCell>
                                    <TableCell class="text-right font-medium">
                                        {{ formatCurrency(cliente.total_gasto) }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>

            <!-- Vendas por Loja -->
            <div class="grid gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Store class="h-5 w-5" />
                            Vendas por Loja
                        </CardTitle>
                        <CardDescription>
                            Performance de vendas por unidade
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="loja in vendasPorLoja" :key="loja.nome" class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">{{ loja.nome }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">{{ formatCurrency(loja.total) }}</span>
                                    <span class="text-xs text-muted-foreground">
                                        ({{ loja.quantidade }} vendas)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>