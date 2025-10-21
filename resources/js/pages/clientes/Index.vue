<script setup lang="ts">
import ClienteController from '@/actions/App/Http/Controllers/ClienteController';
import { index as clientesIndex, create as clientesCreate } from '@/routes/clientes';
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
import { Plus, Search, Pencil, Trash2, Loader2, Eye } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

// Interfaces TypeScript
interface Cliente {
    id: number;
    uuid: string;
    nome: string;
    tipo: string;
    cpf_cnpj: string | null;
    email: string | null;
    telefone: string | null;
    data_nascimento: string | null;
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
    tipo_formatado: string;
    cpf_cnpj_formatado: string | null;
    endereco_completo: string;
}

interface PaginatedClientes {
    data: Cliente[];
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
    clientes: PaginatedClientes;
    filters: {
        search?: string;
        status?: string;
        tipo?: string;
    };
}

// State
const props = defineProps<Props>();
const page = usePage();
const toast = useToast();
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');
const tipo = ref(props.filters.tipo || 'all');
const showDeleteDialog = ref(false);
const clienteToDelete = ref<Cliente | null>(null);

// Computed
const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Clientes', href: null },
]);

// Watchers para filtros em tempo real
watch([search, status, tipo], () => {
    router.get(
        clientesIndex().url,
        {
            search: search.value || undefined,
            status: status.value !== 'all' ? status.value : undefined,
            tipo: tipo.value !== 'all' ? tipo.value : undefined,
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

const getStatusBadgeVariant = (ativo: boolean) => {
    return ativo ? 'default' : 'secondary';
};

const getTipoBadgeVariant = (tipo: string) => {
    return tipo === 'fisica' ? 'outline' : 'default';
};

const openDeleteDialog = (cliente: Cliente) => {
    clienteToDelete.value = cliente;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    showDeleteDialog.value = false;
    clienteToDelete.value = null;
};

const deleteCliente = () => {
    if (!clienteToDelete.value) return;

    router.delete(ClienteController.destroy(clienteToDelete.value).url, {
        onSuccess: () => {
            toast.success('Cliente excluído com sucesso!');
            closeDeleteDialog();
        },
        onError: () => {
            toast.error('Erro ao excluir cliente');
        },
    });
};
</script>

<template>
    <Head title="Clientes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Clientes</h1>
                    <p class="text-muted-foreground">
                        Gerencie o cadastro de clientes do sistema
                    </p>
                </div>
                <Link :href="clientesCreate().url">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Novo Cliente
                    </Button>
                </Link>
            </div>

            <!-- Filters Card -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Filtros</CardTitle>
                    <CardDescription>
                        Use os filtros abaixo para encontrar clientes específicos
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
                                    placeholder="Nome, CPF/CNPJ ou email..."
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
                                    <SelectItem value="ativo">Ativos</SelectItem>
                                    <SelectItem value="inativo">Inativos</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Tipo -->
                        <div class="space-y-2">
                            <Label for="tipo">Tipo</Label>
                            <Select v-model="tipo">
                                <SelectTrigger>
                                    <SelectValue placeholder="Todos os tipos" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="fisica">Pessoa Física</SelectItem>
                                    <SelectItem value="juridica">Pessoa Jurídica</SelectItem>
                                </SelectContent>
                            </Select>
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
                                <TableHead>Nome</TableHead>
                                <TableHead>Tipo</TableHead>
                                <TableHead>CPF/CNPJ</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Telefone</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Cadastrado em</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="clientes.data.length === 0">
                                <TableCell colspan="8" class="text-center py-8 text-muted-foreground">
                                    Nenhum cliente encontrado
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="cliente in clientes.data" :key="cliente.id">
                                <TableCell class="font-medium">{{ cliente.nome }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getTipoBadgeVariant(cliente.tipo)">
                                        {{ cliente.tipo_formatado }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ cliente.cpf_cnpj_formatado || '-' }}</TableCell>
                                <TableCell>{{ cliente.email || '-' }}</TableCell>
                                <TableCell>{{ cliente.telefone || '-' }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadgeVariant(cliente.ativo)">
                                        {{ cliente.ativo ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ formatDate(cliente.created_at) }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="ClienteController.show(cliente).url">
                                            <Button variant="outline" size="sm">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="ClienteController.edit(cliente).url">
                                            <Button variant="outline" size="sm">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="openDeleteDialog(cliente)"
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
            <div v-if="clientes.last_page > 1" class="flex justify-center">
                <nav class="flex items-center space-x-2">
                    <Link
                        v-for="link in clientes.links"
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
                            Tem certeza que deseja excluir o cliente <strong>{{ clienteToDelete?.nome }}</strong>?
                            Esta ação não pode ser desfeita.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="closeDeleteDialog">
                            Cancelar
                        </Button>
                        <Button variant="destructive" @click="deleteCliente">
                            Excluir
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>

