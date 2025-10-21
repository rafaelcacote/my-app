<script setup lang="ts">
import PagamentoController from '@/actions/App/Http/Controllers/PagamentoController';
import { index as pagamentosIndex, create as pagamentosCreate } from '@/routes/pagamentos';
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, Search, Pencil, Trash2, Loader2, Eye, CheckCircle, XCircle } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

// Interfaces TypeScript
interface Cliente {
    id: number;
    nome: string;
}

interface Loja {
    id: number;
    nome: string;
}

interface Venda {
    id: number;
    numero_venda: string;
    cliente: Cliente | null;
    loja: Loja;
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

interface PaginatedPagamentos {
    data: Pagamento[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

interface Props {
    pagamentos: PaginatedPagamentos;
    filters: {
        search?: string;
        status?: string;
        forma_pagamento?: string;
        data_inicio?: string;
        data_fim?: string;
    };
}

// State
const props = defineProps<Props>();
const page = usePage();
const toast = useToast();
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');
const formaPagamento = ref(props.filters.forma_pagamento || '');
const dataInicio = ref(props.filters.data_inicio || '');
const dataFim = ref(props.filters.data_fim || '');
const showDeleteDialog = ref(false);
const pagamentoToDelete = ref<Pagamento | null>(null);

// Computed
const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Pagamentos', href: null },
]);

// Watchers para filtros em tempo real
watch([search, status, formaPagamento, dataInicio, dataFim], () => {
    router.get(
        pagamentosIndex().url,
        {
            search: search.value || undefined,
            status: status.value !== 'all' ? status.value : undefined,
            forma_pagamento: formaPagamento.value || undefined,
            data_inicio: dataInicio.value || undefined,
            data_fim: dataFim.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, { debounce: 300 });

// Métodos auxiliares
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

const openDeleteDialog = (pagamento: Pagamento) => {
    pagamentoToDelete.value = pagamento;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    showDeleteDialog.value = false;
    pagamentoToDelete.value = null;
};

const deletePagamento = () => {
    if (!pagamentoToDelete.value) return;

    router.delete(PagamentoController.destroy(pagamentoToDelete.value).url, {
        onSuccess: () => {
            toast.success('Pagamento excluído com sucesso!');
            closeDeleteDialog();
        },
        onError: () => {
            toast.error('Erro ao excluir pagamento');
        },
    });
};

const marcarComoPago = (pagamento: Pagamento) => {
    router.post(PagamentoController.marcarComoPago(pagamento).url, {
        onSuccess: () => {
            toast.success('Pagamento marcado como pago com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao marcar pagamento como pago');
        },
    });
};

const cancelarPagamento = (pagamento: Pagamento) => {
    router.post(PagamentoController.cancelar(pagamento).url, {
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
    <Head title="Pagamentos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Pagamentos</h1>
                    <p class="text-muted-foreground">
                        Gerencie os pagamentos das vendas
                    </p>
                </div>
                <Link :href="pagamentosCreate().url">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Novo Pagamento
                    </Button>
                </Link>
            </div>

            <!-- Filters Card -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Filtros</CardTitle>
                    <CardDescription>
                        Use os filtros abaixo para encontrar pagamentos específicos
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-3">
                        <!-- Busca -->
                        <div class="space-y-2">
                            <Label for="search">Buscar</Label>
                            <div class="relative">
                                <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                <Input
                                    id="search"
                                    v-model="search"
                                    placeholder="Número da venda ou cliente..."
                                    class="pl-10"
                                />
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <Label for="status">Status</Label>
                            <Select v-model="status">
                                <SelectTrigger>
                                    <SelectValue placeholder="Todos os status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="pendente">Pendentes</SelectItem>
                                    <SelectItem value="pago">Pagos</SelectItem>
                                    <SelectItem value="cancelado">Cancelados</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Forma de Pagamento -->
                        <div class="space-y-2">
                            <Label for="forma_pagamento">Forma de Pagamento</Label>
                            <Select v-model="formaPagamento">
                                <SelectTrigger>
                                    <SelectValue placeholder="Todas as formas" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">Todas</SelectItem>
                                    <SelectItem value="dinheiro">Dinheiro</SelectItem>
                                    <SelectItem value="cartao_debito">Cartão de Débito</SelectItem>
                                    <SelectItem value="cartao_credito">Cartão de Crédito</SelectItem>
                                    <SelectItem value="pix">PIX</SelectItem>
                                    <SelectItem value="boleto">Boleto</SelectItem>
                                    <SelectItem value="transferencia">Transferência</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <!-- Período -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="data_inicio">Data Início</Label>
                            <Input
                                id="data_inicio"
                                v-model="dataInicio"
                                type="date"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="data_fim">Data Fim</Label>
                            <Input
                                id="data_fim"
                                v-model="dataFim"
                                type="date"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Table Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Venda</TableHead>
                                <TableHead>Cliente</TableHead>
                                <TableHead>Loja</TableHead>
                                <TableHead>Forma Pagamento</TableHead>
                                <TableHead>Valor</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Data Pagamento</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="pagamentos.data.length === 0">
                                <TableCell colspan="8" class="text-center py-8 text-muted-foreground">
                                    Nenhum pagamento encontrado
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="pagamento in pagamentos.data" :key="pagamento.id">
                                <TableCell class="font-medium">{{ pagamento.venda.numero_venda }}</TableCell>
                                <TableCell>{{ pagamento.venda.cliente?.nome || 'Cliente não informado' }}</TableCell>
                                <TableCell>{{ pagamento.venda.loja.nome }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getFormaPagamentoBadgeVariant(pagamento.forma_pagamento)">
                                        {{ pagamento.forma_pagamento_formatada }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="font-medium">{{ pagamento.valor_formatado }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadgeVariant(pagamento.status)">
                                        {{ pagamento.status_formatado }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ formatDate(pagamento.data_pagamento) }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="PagamentoController.show(pagamento).url">
                                            <Button variant="outline" size="sm">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link v-if="pagamento.status === 'pendente'" :href="PagamentoController.edit(pagamento).url">
                                            <Button variant="outline" size="sm">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="pagamento.status === 'pendente'"
                                            variant="outline"
                                            size="sm"
                                            @click="marcarComoPago(pagamento)"
                                        >
                                            <CheckCircle class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            v-if="pagamento.status === 'pendente'"
                                            variant="outline"
                                            size="sm"
                                            @click="cancelarPagamento(pagamento)"
                                        >
                                            <XCircle class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            v-if="pagamento.status === 'pendente'"
                                            variant="outline"
                                            size="sm"
                                            @click="openDeleteDialog(pagamento)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div v-if="pagamentos.last_page > 1" class="flex justify-center">
                <nav class="flex items-center space-x-2">
                    <Link
                        v-for="link in pagamentos.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-3 py-2 text-sm font-medium rounded-md',
                            link.active
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:text-foreground hover:bg-muted',
                            !link.url ? 'pointer-events-none opacity-50' : ''
                        ]"
                        v-html="link.label"
                    />
                </nav>
            </div>

            <!-- Delete Dialog -->
            <Dialog v-model:open="showDeleteDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Confirmar Exclusão</DialogTitle>
                        <DialogDescription>
                            Tem certeza que deseja excluir este pagamento da venda <strong>{{ pagamentoToDelete?.venda.numero_venda }}</strong>?
                            Esta ação não pode ser desfeita.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="closeDeleteDialog">
                            Cancelar
                        </Button>
                        <Button variant="destructive" @click="deletePagamento">
                            Excluir
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>

