<script setup lang="ts">
import PagamentoController from '@/actions/App/Http/Controllers/PagamentoController';
import { index as pagamentosIndex } from '@/routes/pagamentos';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
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

interface Venda {
    id: number;
    numero_venda: string;
    status: string;
    subtotal: number;
    desconto: number;
    total: number;
    forma_pagamento: string;
    data_venda: string;
    cliente: Cliente | null;
    loja: Loja;
    usuario: Usuario;
    status_formatado: string;
    forma_pagamento_formatada: string;
    total_formatado: string;
}

interface Pagamento {
    id: number;
    forma_pagamento: string;
    valor: number;
    status: string;
    data_pagamento: string | null;
    observacoes: string | null;
    venda: Venda;
    forma_pagamento_formatada: string;
    status_formatado: string;
    valor_formatado: string;
}

interface Props {
    pagamento: Pagamento;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Pagamentos', href: pagamentosIndex().url },
    { label: `Pagamento - ${props.pagamento.venda.numero_venda}`, href: null },
]);

const formatDate = (date: string | null) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString('pt-BR');
};

const getStatusBadgeVariant = (status: string) => {
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

const getVendaStatusBadgeVariant = (status: string) => {
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

const marcarComoPago = () => {
    router.post(PagamentoController.marcarComoPago(props.pagamento).url, {
        onSuccess: () => {
            toast.success('Pagamento marcado como pago com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao marcar pagamento como pago');
        },
    });
};

const cancelarPagamento = () => {
    router.post(PagamentoController.cancelar(props.pagamento).url, {
        onSuccess: () => {
            toast.success('Pagamento cancelado com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao cancelar pagamento');
        },
    });
};
</script>

<template>
    <Head :title="`Pagamento - ${pagamento.venda.numero_venda}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="pagamentosIndex().url">
                        <Button variant="outline" size="sm">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Voltar
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">
                            Pagamento - {{ pagamento.venda.numero_venda }}
                        </h1>
                        <p class="text-muted-foreground">
                            Detalhes do pagamento
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link v-if="pagamento.status === 'pendente'" :href="PagamentoController.edit(pagamento).url">
                        <Button variant="outline">
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Button v-if="pagamento.status === 'pendente'" @click="marcarComoPago">
                        <CheckCircle class="mr-2 h-4 w-4" />
                        Marcar como Pago
                    </Button>
                    <Button v-if="pagamento.status === 'pendente'" variant="destructive" @click="cancelarPagamento">
                        <XCircle class="mr-2 h-4 w-4" />
                        Cancelar
                    </Button>
                </div>
            </div>

            <!-- Pagamento Info Card -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Informações do Pagamento</CardTitle>
                    <CardDescription>
                        Dados do pagamento e informações da venda
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Dados do Pagamento -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Dados do Pagamento</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Forma de Pagamento:</span>
                                    <Badge :variant="getFormaPagamentoBadgeVariant(pagamento.forma_pagamento)">
                                        {{ pagamento.forma_pagamento_formatada }}
                                    </Badge>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Valor:</span>
                                    <span class="text-foreground font-semibold">{{ pagamento.valor_formatado }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Status:</span>
                                    <Badge :variant="getStatusBadgeVariant(pagamento.status)">
                                        {{ pagamento.status_formatado }}
                                    </Badge>
                                </div>
                                <div v-if="pagamento.data_pagamento" class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Data do Pagamento:</span>
                                    <span class="text-foreground">{{ formatDate(pagamento.data_pagamento) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dados da Venda -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Dados da Venda</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Número da Venda:</span>
                                    <span class="text-foreground">{{ pagamento.venda.numero_venda }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Status da Venda:</span>
                                    <Badge :variant="getVendaStatusBadgeVariant(pagamento.venda.status)">
                                        {{ pagamento.venda.status_formatado }}
                                    </Badge>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Total da Venda:</span>
                                    <span class="text-foreground">{{ pagamento.venda.total_formatado }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Data da Venda:</span>
                                    <span class="text-foreground">{{ formatDate(pagamento.venda.data_venda) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cliente e Loja -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Cliente -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Cliente</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Nome:</span>
                                    <span class="text-foreground">{{ pagamento.venda.cliente?.nome || 'Cliente não informado' }}</span>
                                </div>
                                <div v-if="pagamento.venda.cliente" class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Tipo:</span>
                                    <span class="text-foreground">{{ pagamento.venda.cliente.tipo === 'fisica' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Loja -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Loja</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Nome:</span>
                                    <span class="text-foreground">{{ pagamento.venda.loja.nome }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div v-if="pagamento.observacoes" class="space-y-4">
                        <h3 class="text-lg font-semibold">Observações</h3>
                        <p class="text-sm text-foreground">{{ pagamento.observacoes }}</p>
                    </div>

                    <!-- Informações do Sistema -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Informações do Sistema</h3>
                        <div class="grid gap-2 text-sm md:grid-cols-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-muted-foreground">ID do Pagamento:</span>
                                <span class="font-mono text-foreground">{{ pagamento.id }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

