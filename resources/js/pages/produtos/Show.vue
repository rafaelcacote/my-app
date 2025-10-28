<script setup lang="ts">
import ProdutoController from '@/actions/App/Http/Controllers/ProdutoController';
import { index as produtosIndex } from '@/routes/produtos';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, ArrowLeft, Package } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Categoria {
    id: number;
    nome: string;
}

interface Marca {
    id: number;
    nome: string;
}

interface Produto {
    id: number;
    nome: string;
    descricao: string | null;
    categoria_id: number | null;
    marca_id: number | null;
    preco_custo: number | null;
    preco_venda: number | null;
    codigo_barras: string | null;
    ativo: boolean;
    controla_estoque: boolean;
    estoque_minimo: number | null;
    categoria?: Categoria;
    marca?: Marca;
    created_at: string;
    updated_at: string;
}

interface Props {
    produto: Produto;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Produtos', href: produtosIndex().url },
    { title: props.produto.nome, href: '#' },
];

const formatCurrency = (value: number | null) => {
    if (!value) return 'Não informado';
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};
</script>

<template>
    <Head :title="`Produto: ${produto.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Package class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">{{ produto.nome }}</h1>
                        <Badge
                            :variant="produto.ativo ? 'success' : 'secondary'"
                            class="ml-2"
                        >
                            {{ produto.ativo ? 'Ativo' : 'Inativo' }}
                        </Badge>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Detalhes do produto
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="ProdutoController.edit(produto).url">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Informações Básicas -->
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Package class="h-5 w-5" />
                            Informações Básicas
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Nome:</span>
                            <p class="text-foreground">{{ produto.nome }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Descrição:</span>
                            <p class="text-foreground">
                                {{ produto.descricao || 'Sem descrição' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Código de Barras:</span>
                            <p class="font-mono text-foreground">
                                {{ produto.codigo_barras || 'Não informado' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Status:</span>
                            <Badge
                                :variant="produto.ativo ? 'success' : 'secondary'"
                                class="ml-2"
                            >
                                {{ produto.ativo ? 'Ativo' : 'Inativo' }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Categoria e Marca -->
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle>Classificação</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Categoria:</span>
                            <p class="text-foreground">
                                {{ produto.categoria?.nome || 'Não definida' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Marca:</span>
                            <p class="text-foreground">
                                {{ produto.marca?.nome || 'Não definida' }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Preços e Estoque -->
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle>Preços e Estoque</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Preço de Custo:</span>
                            <p class="text-foreground">{{ formatCurrency(produto.preco_custo) }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Preço de Venda:</span>
                            <p class="text-foreground">{{ formatCurrency(produto.preco_venda) }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Controla Estoque:</span>
                            <Badge :variant="produto.controla_estoque ? 'default' : 'secondary'" class="ml-2">
                                {{ produto.controla_estoque ? 'Sim' : 'Não' }}
                            </Badge>
                        </div>
                        <div v-if="produto.controla_estoque">
                            <span class="text-sm font-medium text-muted-foreground">Estoque Mínimo:</span>
                            <p class="text-foreground">{{ produto.estoque_minimo || 'Não definido' }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Informações do Sistema -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Informações do Sistema</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 text-sm sm:grid-cols-3">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <p class="font-mono text-foreground">{{ produto.id }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-muted-foreground">Criado em:</span>
                            <p class="text-foreground">
                                {{ new Date(produto.created_at).toLocaleDateString('pt-BR') }} às 
                                {{ new Date(produto.created_at).toLocaleTimeString('pt-BR') }}
                            </p>
                        </div>
                        <div>
                            <span class="font-medium text-muted-foreground">Atualizado em:</span>
                            <p class="text-foreground">
                                {{ new Date(produto.updated_at).toLocaleDateString('pt-BR') }} às 
                                {{ new Date(produto.updated_at).toLocaleTimeString('pt-BR') }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Back Button -->
            <div class="flex justify-start">
                <Link :href="produtosIndex().url">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Voltar para Produtos
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
