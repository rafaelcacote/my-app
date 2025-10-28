<script setup lang="ts">
import VendaController from '@/actions/App/Http/Controllers/VendaController';
import { index as vendasIndex, create as vendasCreate } from '@/routes/vendas';
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

interface Usuario {
    id: number;
    name: string;
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
    data_venda: string;
    cliente: Cliente | null;
    loja: Loja;
    usuario: Usuario;
    status_formatado: string;
    forma_pagamento_formatada: string;
    total_formatado: string;
}

interface PaginatedVendas {
    data: Venda[];
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

interface Loja {
    id: number;
    nome: string;
}

interface Props {
    vendas: PaginatedVendas;
    lojas: Loja[];
    filters: {
        search?: string;
        status?: string;
        loja_id?: string;
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
const lojaId = ref(props.filters.loja_id || '');
const dataInicio = ref(props.filters.data_inicio || '');
const dataFim = ref(props.filters.data_fim || '');
const showDeleteDialog = ref(false);
const vendaToDelete = ref<Venda | null>(null);

// Computed
const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Vendas', href: null },
]);

// Watchers para filtros em tempo real
watch([search, status, lojaId, dataInicio, dataFim], () => {
    router.get(
        vendasIndex().url,
        {
            search: search.value || undefined,
            status: status.value !== 'all' ? status.value : undefined,
            loja_id: lojaId.value || undefined,
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

const openDeleteDialog = (venda: Venda) => {
    vendaToDelete.value = venda;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    showDeleteDialog.value = false;
    vendaToDelete.value = null;
};

const deleteVenda = () => {
    if (!vendaToDelete.value) return;

    router.delete(VendaController.destroy(vendaToDelete.value).url, {
        onSuccess: () => {
            toast.success('Venda excluída com sucesso!');
            closeDeleteDialog();
        },
        onError: () => {
            toast.error('Erro ao excluir venda');
        },
    });
};

const concluirVenda = (venda: Venda) => {
    router.post(VendaController.concluir(venda).url, {
        onSuccess: () => {
            toast.success('Venda concluída com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao concluir venda');
        },
    });
};

const cancelarVenda = (venda: Venda) => {
    router.post(VendaController.cancelar(venda).url, {
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
    <Head title="Vendas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Vendas</h1>
                    <p class="text-muted-foreground">
                        Gerencie as vendas realizadas no sistema
                    </p>
                </div>
                <Link :href="vendasCreate().url">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Venda
                    </Button>
                </Link>
            </div>

            <!-- Filters Card -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Filtros</CardTitle>
                    <CardDescription>
                        Use os filtros abaixo para encontrar vendas específicas
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
                                    <SelectItem value="concluida">Concluídas</SelectItem>
                                    <SelectItem value="cancelada">Canceladas</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Loja -->
                        <div class="space-y-2">
                            <Label for="loja_id">Loja</Label>
                            <Select v-model="lojaId">
                                <SelectTrigger>
                                    <SelectValue placeholder="Todas as lojas" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">Todas</SelectItem>
                                    <SelectItem v-for="loja in lojas" :key="loja.id" :value="loja.id.toString()">
                                        {{ loja.nome }}
                                    </SelectItem>
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
                                <TableHead>Número</TableHead>
                                <TableHead>Cliente</TableHead>
                                <TableHead>Loja</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead>Forma Pagamento</TableHead>
                                <TableHead>Data</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="vendas.data.length === 0">
                                <TableCell colspan="8" class="text-center py-8 text-muted-foreground">
                                    Nenhuma venda encontrada
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="venda in vendas.data" :key="venda.id">
                                <TableCell class="font-medium">{{ venda.numero_venda }}</TableCell>
                                <TableCell>{{ venda.cliente?.nome || 'Cliente não informado' }}</TableCell>
                                <TableCell>{{ venda.loja.nome }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadgeVariant(venda.status)">
                                        {{ venda.status_formatado }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="font-medium">{{ venda.total_formatado }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getFormaPagamentoBadgeVariant(venda.forma_pagamento)">
                                        {{ venda.forma_pagamento_formatada }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ formatDate(venda.data_venda) }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="VendaController.show(venda).url">
                                            <Button variant="ghost" size="sm" title="Visualizar">
                                                <Eye class="h-4 w-4 text-blue-600 hover:text-blue-700" />
                                                <span class="sr-only">Visualizar</span>
                                            </Button>
                                        </Link>
                                        <Link v-if="venda.status === 'pendente'" :href="VendaController.edit(venda).url">
                                            <Button variant="ghost" size="sm" title="Editar">
                                                <Pencil class="h-4 w-4 text-orange-600 hover:text-orange-700" />
                                                <span class="sr-only">Editar</span>
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="venda.status === 'pendente'"
                                            variant="ghost"
                                            size="sm"
                                            title="Concluir"
                                            @click="concluirVenda(venda)"
                                        >
                                            <CheckCircle class="h-4 w-4 text-green-600 hover:text-green-700" />
                                            <span class="sr-only">Concluir</span>
                                        </Button>
                                        <Button
                                            v-if="venda.status === 'pendente'"
                                            variant="ghost"
                                            size="sm"
                                            title="Cancelar"
                                            @click="cancelarVenda(venda)"
                                        >
                                            <XCircle class="h-4 w-4 text-amber-600 hover:text-amber-700" />
                                            <span class="sr-only">Cancelar</span>
                                        </Button>
                                        <Button
                                            v-if="venda.status === 'pendente'"
                                            variant="ghost"
                                            size="sm"
                                            title="Excluir"
                                            @click="openDeleteDialog(venda)"
                                        >
                                            <Trash2 class="h-4 w-4 text-red-600 hover:text-red-700" />
                                            <span class="sr-only">Excluir</span>
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div v-if="vendas.last_page > 1" class="flex justify-center">
                <nav class="flex items-center space-x-2">
                    <Link
                        v-for="link in vendas.links"
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
                            Tem certeza que deseja excluir a venda <strong>{{ vendaToDelete?.numero_venda }}</strong>?
                            Esta ação não pode ser desfeita.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="closeDeleteDialog">
                            Cancelar
                        </Button>
                        <Button variant="destructive" @click="deleteVenda">
                            Excluir
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>

