<template>
    <Head title="Fornecedores" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        Fornecedores
                    </h1>
                    <p class="text-muted-foreground">
                        Gerencie os fornecedores da sua empresa
                    </p>
                </div>
                <Link :href="FornecedorController.create().url">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Novo Fornecedor
                    </Button>
                </Link>
            </div>

            <!-- Filters Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Search -->
                        <div class="space-y-2">
                            <Label for="search">Buscar</Label>
                            <div class="relative">
                                <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                <Input
                                    id="search"
                                    v-model="search"
                                    placeholder="Nome, CPF/CNPJ ou email..."
                                    class="pl-10"
                                />
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="space-y-2">
                            <Label for="status">Status</Label>
                            <Select v-model="status">
                                <SelectTrigger>
                                    <SelectValue placeholder="Todos os status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="ativo">Ativos</SelectItem>
                                    <SelectItem value="inativo">Inativos</SelectItem>
                                </SelectContent>
                            </Select>
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
                                    <TableHead>Nome</TableHead>
                                    <TableHead>CPF/CNPJ</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Telefone</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="fornecedores.data.length === 0">
                                    <TableCell colspan="6" class="text-center py-8 text-muted-foreground">
                                        Nenhum fornecedor encontrado
                                    </TableCell>
                                </TableRow>
                                <TableRow v-else v-for="fornecedor in fornecedores.data" :key="fornecedor.id">
                                    <TableCell class="font-medium">
                                        {{ fornecedor.nome }}
                                    </TableCell>
                                    <TableCell>
                                        {{ fornecedor.cpf_cnpj || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        {{ fornecedor.email || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        {{ fornecedor.telefone || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        <Badge :variant="fornecedor.ativo ? 'default' : 'secondary'">
                                            {{ fornecedor.ativo ? 'Ativo' : 'Inativo' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="FornecedorController.show(fornecedor).url">
                                                <Button variant="ghost" size="sm">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="FornecedorController.edit(fornecedor).url">
                                                <Button variant="ghost" size="sm">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="openDeleteDialog(fornecedor)"
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
                v-if="fornecedores.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ fornecedores.data.length }} de {{ fornecedores.total }} fornecedores
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in fornecedores.links"
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
                            Tem certeza que deseja excluir o fornecedor
                            <strong>{{ empresaToDelete?.nome }}</strong>?
                            Esta ação não pode ser desfeita.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="showDeleteDialog = false">
                            Cancelar
                        </Button>
                        <Button variant="destructive" @click="deleteFornecedor" :disabled="isDeleting">
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
import FornecedorController from '@/actions/App/Http/Controllers/FornecedorController';
import { index as fornecedoresIndex } from '@/routes/fornecedores';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Search, Eye, Pencil, Trash2, Loader2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

interface Fornecedor {
    id: number;
    uuid: string;
    nome: string;
    cpf_cnpj: string | null;
    email: string | null;
    telefone: string | null;
    cep: string | null;
    logradouro: string | null;
    numero: string | null;
    complemento: string | null;
    bairro: string | null;
    cidade: string | null;
    estado: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface PaginatedFornecedores {
    data: Fornecedor[];
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
    fornecedores: PaginatedFornecedores;
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');
const showDeleteDialog = ref(false);
const empresaToDelete = ref<Fornecedor | null>(null);
const isDeleting = ref(false);

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Fornecedores', href: null },
];

// Watchers para filtros em tempo real
watch([search, status], () => {
    router.get(
        fornecedoresIndex().url,
        {
            search: search.value || undefined,
            status: status.value !== 'all' ? status.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, { debounce: 300 });

const openDeleteDialog = (fornecedor: Fornecedor) => {
    empresaToDelete.value = fornecedor;
    showDeleteDialog.value = true;
};

const deleteFornecedor = () => {
    if (!empresaToDelete.value) return;

    isDeleting.value = true;
    router.delete(FornecedorController.destroy(empresaToDelete.value).url, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            empresaToDelete.value = null;
            toast.success('Fornecedor excluído com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao excluir fornecedor');
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};
</script>
