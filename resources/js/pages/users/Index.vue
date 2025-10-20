<script setup lang="ts">
import UserController from '@/actions/App/Http/Controllers/UserController';
import { index as usersIndex, create as usersCreate } from '@/routes/users';
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Search, Pencil, Trash2, Eye, User as UserIcon, Building2 } from 'lucide-vue-next';
import type { BreadcrumbItem, User, Empresa } from '@/types';

interface PaginatedUsers {
    data: User[];
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
    users: PaginatedUsers;
    empresas: Empresa[];
    filters: {
        search?: string;
        status?: string;
        empresa_id?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Usuários', href: usersIndex().url },
];

// Verificar flash messages do backend
onMounted(() => {
    const flash = page.props.flash as any;
    if (flash?.success) {
        toast.success(flash.success as string);
    }
});

// Filters
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');
const empresaId = ref(props.filters.empresa_id || '');

// Delete dialog
const showDeleteDialog = ref(false);
const userToDelete = ref<User | null>(null);

// Watch filters and update URL
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch([search, status, empresaId], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            usersIndex().url,
            {
                search: search.value || undefined,
                status: status.value !== 'all' ? status.value : undefined,
                empresa_id: empresaId.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, 300);
});

// Delete functions
const openDeleteDialog = (user: User) => {
    userToDelete.value = user;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    showDeleteDialog.value = false;
    userToDelete.value = null;
};

const deleteUser = () => {
    if (userToDelete.value) {
        router.delete(UserController.destroy(userToDelete.value).url, {
            onSuccess: () => {
                toast.success('Usuário excluído com sucesso!');
                closeDeleteDialog();
            },
            onError: () => {
                toast.error('Erro ao excluir usuário');
            },
        });
    }
};

// Helper functions
const formatDate = (date: string | null) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString('pt-BR');
};

const formatCpf = (cpf: string) => {
    if (!cpf) return '';
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
};

const getStatusBadgeVariant = (ativo: boolean) => {
    return ativo ? 'default' : 'secondary';
};

const getStatusText = (ativo: boolean) => {
    return ativo ? 'Ativo' : 'Inativo';
};
</script>

<template>
    <Head title="Usuários" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Usuários</h1>
                    <p class="text-muted-foreground">
                        Gerencie os usuários do sistema e suas permissões
                    </p>
                </div>
                <Link :href="usersCreate().url">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Novo Usuário
                    </Button>
                </Link>
            </div>

            <!-- Filters Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 md:grid-cols-3">
                        <!-- Search -->
                        <div class="space-y-2">
                            <label for="search" class="text-sm font-medium">Buscar</label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="search"
                                    v-model="search"
                                    placeholder="Nome ou email..."
                                    class="pl-10"
                                />
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="space-y-2">
                            <label for="status" class="text-sm font-medium">Status</label>
                            <select
                                id="status"
                                v-model="status"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="all">Todos</option>
                                <option value="ativo">Ativos</option>
                                <option value="inativo">Inativos</option>
                            </select>
                        </div>

                        <!-- Empresa Filter -->
                        <div class="space-y-2">
                            <label for="empresa" class="text-sm font-medium">Empresa</label>
                            <select
                                id="empresa"
                                v-model="empresaId"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Todas as empresas</option>
                                <option
                                    v-for="empresa in empresas"
                                    :key="empresa.id"
                                    :value="empresa.id.toString()"
                                >
                                    {{ empresa.razao_social }}
                                </option>
                            </select>
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
                                <TableHead>Email</TableHead>
                                <TableHead>CPF</TableHead>
                                <TableHead>Empresa</TableHead>
                                <TableHead>Tipo</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Cadastrado em</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="users.data.length === 0">
                                <TableCell colspan="8" class="text-center py-8 text-muted-foreground">
                                    Nenhum usuário encontrado
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="user in users.data" :key="user.id">
                                <TableCell class="font-medium">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                                            <UserIcon class="h-4 w-4 text-primary" />
                                        </div>
                                        {{ user.name }}
                                    </div>
                                </TableCell>
                                <TableCell>{{ user.email }}</TableCell>
                                <TableCell>
                                    <span v-if="user.cpf" class="text-sm">{{ formatCpf(user.cpf) }}</span>
                                    <span v-else class="text-muted-foreground">-</span>
                                </TableCell>
                                <TableCell>
                                    <div v-if="user.empresa" class="flex items-center gap-2">
                                        <Building2 class="h-4 w-4 text-muted-foreground" />
                                        {{ user.empresa.razao_social }}
                                    </div>
                                    <span v-else class="text-muted-foreground">-</span>
                                </TableCell>
                                <TableCell>
                                    <span v-if="user.tipo" class="text-sm">{{ user.tipo }}</span>
                                    <span v-else class="text-muted-foreground">-</span>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadgeVariant(user.ativo)">
                                        {{ getStatusText(user.ativo) }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ formatDate(user.created_at) }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="UserController.show(user).url">
                                            <Button variant="ghost" size="sm">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="UserController.edit(user).url">
                                            <Button variant="ghost" size="sm">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            @click="openDeleteDialog(user)"
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
            <div v-if="users.last_page > 1" class="flex items-center justify-between">
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ users.data.length }} de {{ users.total }} usuários
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        v-for="link in users.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-3 py-2 text-sm rounded-md',
                            link.active
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:text-foreground hover:bg-muted',
                            !link.url && 'pointer-events-none opacity-50'
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
                            Tem certeza que deseja excluir o usuário
                            <strong>{{ userToDelete?.name }}</strong>?
                            Esta ação não pode ser desfeita.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline">Cancelar</Button>
                        </DialogClose>
                        <Button variant="destructive" @click="deleteUser">
                            Excluir
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
