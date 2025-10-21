<template>
    <Head title="Entradas de Mercadoria" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        Entradas de Mercadoria
                    </h1>
                    <p class="text-muted-foreground">
                        Gerencie as entradas de mercadoria nas suas lojas
                    </p>
                </div>
                <Link :href="EntradaMercadoriaController.create().url">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Entrada
                    </Button>
                </Link>
            </div>

            <!-- Filters Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                        <!-- Search -->
                        <div class="space-y-2">
                            <Label for="search">Buscar</Label>
                            <div class="relative">
                                <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                <Input
                                    id="search"
                                    v-model="search"
                                    placeholder="Número da nota ou observações..."
                                    class="pl-10"
                                />
                            </div>
                        </div>

                        <!-- Loja Filter -->
                        <div class="space-y-2">
                            <Label for="loja">Loja</Label>
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

                        <!-- Fornecedor Filter -->
                        <div class="space-y-2">
                            <Label for="fornecedor">Fornecedor</Label>
                            <Select v-model="fornecedorId">
                                <SelectTrigger>
                                    <SelectValue placeholder="Todos os fornecedores" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="">Todos</SelectItem>
                                    <SelectItem v-for="fornecedor in fornecedores" :key="fornecedor.id" :value="fornecedor.id.toString()">
                                        {{ fornecedor.nome }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Data Início -->
                        <div class="space-y-2">
                            <Label for="data_inicio">Data Início</Label>
                            <Input
                                id="data_inicio"
                                v-model="dataInicio"
                                type="date"
                            />
                        </div>

                        <!-- Data Fim -->
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
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Data</TableHead>
                                    <TableHead>Número da Nota</TableHead>
                                    <TableHead>Loja</TableHead>
                                    <TableHead>Fornecedor</TableHead>
                                    <TableHead>Valor Total</TableHead>
                                    <TableHead>Usuário</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="entradas.data.length === 0">
                                    <TableCell colspan="7" class="text-center py-8 text-muted-foreground">
                                        Nenhuma entrada encontrada
                                    </TableCell>
                                </TableRow>
                                <TableRow v-else v-for="entrada in entradas.data" :key="entrada.id">
                                    <TableCell class="font-medium">
                                        {{ formatDate(entrada.data_entrada) }}
                                    </TableCell>
                                    <TableCell>
                                        {{ entrada.numero_nota || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        {{ entrada.loja?.nome || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        {{ entrada.fornecedor?.nome || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        {{ formatCurrency(entrada.valor_total) }}
                                    </TableCell>
                                    <TableCell>
                                        {{ entrada.usuario?.name || '-' }}
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="EntradaMercadoriaController.show(entrada).url">
                                                <Button variant="ghost" size="sm">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="EntradaMercadoriaController.edit(entrada).url">
                                                <Button variant="ghost" size="sm">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="openDeleteDialog(entrada)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div
                v-if="entradas.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ entradas.data.length }} de {{ entradas.total }} entradas
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in entradas.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-3 py-1 rounded border text-sm',
                            link.active
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-background hover:bg-muted',
                            !link.url && 'opacity-50 cursor-not-allowed',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>

            <!-- Delete Dialog -->
            <Dialog v-model:open="showDeleteDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Confirmar Exclusão</DialogTitle>
                        <DialogDescription>
                            Tem certeza que deseja excluir esta entrada de mercadoria?
                            Esta ação não pode ser desfeita e removerá todos os itens associados.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="showDeleteDialog = false">
                            Cancelar
                        </Button>
                        <Button variant="destructive" @click="deleteEntrada" :disabled="isDeleting">
                            <Loader2 v-if="isDeleting" class="mr-2 h-4 w-4 animate-spin" />
                            {{ isDeleting ? 'Excluindo...' : 'Excluir' }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import EntradaMercadoriaController from '@/actions/App/Http/Controllers/EntradaMercadoriaController';
import { index as entradasIndex } from '@/routes/entradas-mercadoria';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Search, Eye, Pencil, Trash2, Loader2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

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
    created_at: string;
    updated_at: string;
}

interface PaginatedEntradas {
    data: EntradaMercadoria[];
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
    entradas: PaginatedEntradas;
    lojas: Loja[];
    fornecedores: Fornecedor[];
    filters: {
        search?: string;
        loja_id?: string;
        fornecedor_id?: string;
        data_inicio?: string;
        data_fim?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const search = ref(props.filters.search || '');
const lojaId = ref(props.filters.loja_id || '');
const fornecedorId = ref(props.filters.fornecedor_id || '');
const dataInicio = ref(props.filters.data_inicio || '');
const dataFim = ref(props.filters.data_fim || '');
const showDeleteDialog = ref(false);
const entradaToDelete = ref<EntradaMercadoria | null>(null);
const isDeleting = ref(false);

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Entradas de Mercadoria', href: null },
];

// Watchers para filtros em tempo real
watch([search, lojaId, fornecedorId, dataInicio, dataFim], () => {
    router.get(
        entradasIndex().url,
        {
            search: search.value || undefined,
            loja_id: lojaId.value || undefined,
            fornecedor_id: fornecedorId.value || undefined,
            data_inicio: dataInicio.value || undefined,
            data_fim: dataFim.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, { debounce: 300 });

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};

const openDeleteDialog = (entrada: EntradaMercadoria) => {
    entradaToDelete.value = entrada;
    showDeleteDialog.value = true;
};

const deleteEntrada = () => {
    if (!entradaToDelete.value) return;

    isDeleting.value = true;
    router.delete(EntradaMercadoriaController.destroy(entradaToDelete.value).url, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            entradaToDelete.value = null;
            toast.success('Entrada de mercadoria excluída com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao excluir entrada de mercadoria');
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};
</script>
