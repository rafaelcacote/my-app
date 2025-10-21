<template>
    <Head title="Entrada de Mercadoria" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        Entrada de Mercadoria
                    </h1>
                    <p class="text-muted-foreground">
                        {{ entrada.numero_nota || 'Sem número de nota' }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="EntradaMercadoriaController.edit(entrada).url">
                        <Button variant="outline">
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Link :href="EntradaMercadoriaController.index().url">
                        <Button variant="outline">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Voltar
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Informações Básicas -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Informações da Entrada</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Data de Entrada</Label>
                            <p class="text-sm">{{ formatDate(entrada.data_entrada) }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Número da Nota</Label>
                            <p class="text-sm">{{ entrada.numero_nota || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Loja</Label>
                            <p class="text-sm">{{ entrada.loja?.nome || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Fornecedor</Label>
                            <p class="text-sm">{{ entrada.fornecedor?.nome || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Valor Total</Label>
                            <p class="text-sm font-semibold">{{ formatCurrency(entrada.valor_total) }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Usuário</Label>
                            <p class="text-sm">{{ entrada.usuario?.name || '-' }}</p>
                        </div>
                    </div>
                    <div v-if="entrada.observacoes">
                        <Label class="text-sm font-medium text-muted-foreground">Observações</Label>
                        <p class="text-sm mt-1">{{ entrada.observacoes }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Itens da Entrada -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Itens da Entrada</CardTitle>
                    <CardDescription>
                        Produtos recebidos nesta entrada
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="entrada.itens?.length === 0" class="text-center py-8 text-muted-foreground">
                        Nenhum item encontrado
                    </div>
                    <div v-else class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Produto</TableHead>
                                    <TableHead>Variação</TableHead>
                                    <TableHead>Quantidade</TableHead>
                                    <TableHead>Preço Unitário</TableHead>
                                    <TableHead>Total</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="item in entrada.itens" :key="item.id">
                                    <TableCell class="font-medium">
                                        {{ item.produto_variacao?.produto?.nome || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        <div v-if="item.produto_variacao">
                                            <span v-if="item.produto_variacao.cor">
                                                {{ item.produto_variacao.cor.nome }}
                                            </span>
                                            <span v-if="item.produto_variacao.cor && item.produto_variacao.tamanho"> - </span>
                                            <span v-if="item.produto_variacao.tamanho">
                                                {{ item.produto_variacao.tamanho.nome }}
                                            </span>
                                        </div>
                                        <span v-else>-</span>
                                    </TableCell>
                                    <TableCell>{{ item.quantidade }}</TableCell>
                                    <TableCell>{{ formatCurrency(item.preco_unitario) }}</TableCell>
                                    <TableCell class="font-medium">{{ formatCurrency(item.total) }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <!-- Informações do Sistema -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Informações do Sistema</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Cadastrado em</Label>
                            <p class="text-sm">{{ formatDate(entrada.created_at) }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Última atualização</Label>
                            <p class="text-sm">{{ formatDate(entrada.updated_at) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import EntradaMercadoriaController from '@/actions/App/Http/Controllers/EntradaMercadoriaController';
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, ArrowLeft } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface Produto {
    id: number;
    nome: string;
}

interface Cor {
    id: number;
    nome: string;
}

interface Tamanho {
    id: number;
    nome: string;
}

interface ProdutoVariacao {
    id: number;
    produto?: Produto;
    cor?: Cor;
    tamanho?: Tamanho;
}

interface EntradaMercadoriaItem {
    id: number;
    quantidade: number;
    preco_unitario: number;
    total: number;
    produto_variacao?: ProdutoVariacao;
}

interface Loja {
    id: number;
    nome: string;
}

interface Fornecedor {
    id: number;
    nome: string;
}

interface Usuario {
    id: number;
    name: string;
}

interface EntradaMercadoria {
    id: number;
    numero_nota: string | null;
    data_entrada: string;
    valor_total: number;
    observacoes: string | null;
    loja?: Loja;
    fornecedor?: Fornecedor;
    usuario?: Usuario;
    itens?: EntradaMercadoriaItem[];
    created_at: string;
    updated_at: string;
}

interface Props {
    entrada: EntradaMercadoria;
}

const props = defineProps<Props>();

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Entradas de Mercadoria', href: EntradaMercadoriaController.index().url },
    { label: props.entrada.numero_nota || 'Entrada', href: null },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};
</script>
