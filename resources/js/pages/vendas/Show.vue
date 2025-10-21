<script setup lang="ts">
import VendaController from '@/actions/App/Http/Controllers/VendaController';
import { index as vendasIndex } from '@/routes/vendas';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pencil, ArrowLeft, CheckCircle, XCircle } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';
import { computed } from 'vue';

interface Cliente {
    id: number;
    nome: string;
    tipo: string;
}

interface Loja {
    id: number;
    nome: string;
}

interface Usuario {
    id: number;
    name: string;
}

interface Cor {
    id: number;
    nome: string;
}

interface Tamanho {
    id: number;
    nome: string;
}

interface Produto {
    id: number;
    nome: string;
}

interface ProdutoVariacao {
    id: number;
    produto: Produto;
    cor: Cor;
    tamanho: Tamanho;
}

interface VendaItem {
    id: number;
    quantidade: number;
    preco_unitario: number;
    desconto: number;
    total: number;
    produto_variacao: ProdutoVariacao;
    preco_unitario_formatado: string;
    desconto_formatado: string;
    total_formatado: string;
    subtotal_formatado: string;
}

interface Pagamento {
    id: number;
    forma_pagamento: string;
    valor: number;
    status: string;
    data_pagamento: string | null;
    observacoes: string | null;
    forma_pagamento_formatada: string;
    status_formatado: string;
    valor_formatado: string;
}

interface Venda {
    id: number;
    uuid: string;
    numero_venda: string;
    status: string;
    subtotal: number;
    desconto: number;
    total: number;
    forma_pagamento: string;
    observacoes: string | null;
    data_venda: string;
    cliente: Cliente | null;
    loja: Loja;
    usuario: Usuario;
    itens: VendaItem[];
    pagamentos: Pagamento[];
    status_formatado: string;
    forma_pagamento_formatada: string;
    total_formatado: string;
    subtotal_formatado: string;
    desconto_formatado: string;
}

interface Props {
    venda: Venda;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Vendas', href: vendasIndex().url },
    { label: props.venda.numero_venda, href: null },
]);

const formatDate = (date: string | null) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString('pt-BR');
};

const getStatusBadgeVariant = (status: string) => {
    switch (status) {
        case 'concluida':
            return 'default';
        case 'pendente':
            return 'secondary';
        case 'cancelada':
            return 'destructive';
        default:
            return 'outline';
    }
};

const getFormaPagamentoBadgeVariant = (forma: string) => {
    switch (forma) {
        case 'dinheiro':
            return 'default';
        case 'cartao_debito':
        case 'cartao_credito':
            return 'secondary';
        case 'pix':
            return 'outline';
        case 'boleto':
            return 'destructive';
        default:
            return 'outline';
    }
};

const getPagamentoStatusBadgeVariant = (status: string) => {
    switch (status) {
        case 'pago':
            return 'default';
        case 'pendente':
            return 'secondary';
        case 'cancelado':
            return 'destructive';
        default:
            return 'outline';
    }
};

const concluirVenda = () => {
    router.post(VendaController.concluir(props.venda).url, {
        onSuccess: () => {
            toast.success('Venda concluída com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao concluir venda');
        },
    });
};

const cancelarVenda = () => {
    router.post(VendaController.cancelar(props.venda).url, {
        onSuccess: () => {
            toast.success('Venda cancelada com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao cancelar venda');
        },
    });
};
</script>

<template>
    <Head :title="venda.numero_venda" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="vendasIndex().url">
                        <Button variant="outline" size="sm">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Voltar
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">{{ venda.numero_venda }}</h1>
                        <p class="text-muted-foreground">
                            Detalhes da venda
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link v-if="venda.status === 'pendente'" :href="VendaController.edit(venda).url">
                        <Button variant="outline">
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Button v-if="venda.status === 'pendente'" @click="concluirVenda">
                        <CheckCircle class="mr-2 h-4 w-4" />
                        Concluir
                    </Button>
                    <Button v-if="venda.status === 'pendente'" variant="destructive" @click="cancelarVenda">
                        <XCircle class="mr-2 h-4 w-4" />
                        Cancelar
                    </Button>
                </div>
            </div>

            <!-- Venda Info Card -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Informações da Venda</CardTitle>
                    <CardDescription>
                        Dados da venda e informações do cliente
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Dados da Venda -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Dados da Venda</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Número:</span>
                                    <span class="text-foreground">{{ venda.numero_venda }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Status:</span>
                                    <Badge :variant="getStatusBadgeVariant(venda.status)">
                                        {{ venda.status_formatado }}
                                    </Badge>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Loja:</span>
                                    <span class="text-foreground">{{ venda.loja.nome }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Vendedor:</span>
                                    <span class="text-foreground">{{ venda.usuario.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Data:</span>
                                    <span class="text-foreground">{{ formatDate(venda.data_venda) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cliente -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Cliente</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Nome:</span>
                                    <span class="text-foreground">{{ venda.cliente?.nome || 'Cliente não informado' }}</span>
                                </div>
                                <div v-if="venda.cliente" class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Tipo:</span>
                                    <span class="text-foreground">{{ venda.cliente.tipo === 'fisica' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Valores -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Valores</h3>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Subtotal:</span>
                                    <span class="text-foreground">{{ venda.subtotal_formatado }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Desconto:</span>
                                    <span class="text-foreground">{{ venda.desconto_formatado }}</span>
                                </div>
                                <div class="flex justify-between font-semibold">
                                    <span class="text-muted-foreground">Total:</span>
                                    <span class="text-foreground">{{ venda.total_formatado }}</span>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Forma de Pagamento:</span>
                                    <Badge :variant="getFormaPagamentoBadgeVariant(venda.forma_pagamento)">
                                        {{ venda.forma_pagamento_formatada }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div v-if="venda.observacoes" class="space-y-4">
                        <h3 class="text-lg font-semibold">Observações</h3>
                        <p class="text-sm text-foreground">{{ venda.observacoes }}</p>
                    </div>

                    <!-- Informações do Sistema -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Informações do Sistema</h3>
                        <div class="grid gap-2 text-sm md:grid-cols-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-muted-foreground">UUID:</span>
                                <span class="font-mono text-foreground">{{ venda.uuid }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Itens da Venda -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Itens da Venda</CardTitle>
                    <CardDescription>
                        Produtos vendidos nesta venda
                    </CardDescription>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Produto</TableHead>
                                <TableHead>Cor</TableHead>
                                <TableHead>Tamanho</TableHead>
                                <TableHead>Quantidade</TableHead>
                                <TableHead>Preço Unitário</TableHead>
                                <TableHead>Desconto</TableHead>
                                <TableHead>Total</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in venda.itens" :key="item.id">
                                <TableCell class="font-medium">{{ item.produto_variacao.produto.nome }}</TableCell>
                                <TableCell>{{ item.produto_variacao.cor.nome }}</TableCell>
                                <TableCell>{{ item.produto_variacao.tamanho.nome }}</TableCell>
                                <TableCell>{{ item.quantidade }}</TableCell>
                                <TableCell>{{ item.preco_unitario_formatado }}</TableCell>
                                <TableCell>{{ item.desconto_formatado }}</TableCell>
                                <TableCell class="font-medium">{{ item.total_formatado }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Pagamentos -->
            <Card v-if="venda.pagamentos && venda.pagamentos.length > 0" class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Pagamentos</CardTitle>
                    <CardDescription>
                        Pagamentos realizados para esta venda
                    </CardDescription>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Forma de Pagamento</TableHead>
                                <TableHead>Valor</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Data do Pagamento</TableHead>
                                <TableHead>Observações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="pagamento in venda.pagamentos" :key="pagamento.id">
                                <TableCell>
                                    <Badge :variant="getFormaPagamentoBadgeVariant(pagamento.forma_pagamento)">
                                        {{ pagamento.forma_pagamento_formatada }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="font-medium">{{ pagamento.valor_formatado }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getPagamentoStatusBadgeVariant(pagamento.status)">
                                        {{ pagamento.status_formatado }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ formatDate(pagamento.data_pagamento) }}</TableCell>
                                <TableCell>{{ pagamento.observacoes || '-' }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Mensagem quando não há pagamentos -->
            <Card v-else class="border-border shadow-sm">
                <CardContent class="py-8 text-center">
                    <p class="text-muted-foreground">Esta venda ainda não possui pagamentos cadastrados.</p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

