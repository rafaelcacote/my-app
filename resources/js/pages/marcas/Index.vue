<script setup lang="ts">
import MarcaController from '@/actions/App/Http/Controllers/MarcaController';
import { index as marcasIndex, create as marcasCreate } from '@/routes/marcas';
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
import { Plus, Search, Pencil, Trash2, Eye, Tag } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Marca {
    id: number;
    nome: string;
    descricao: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface PaginatedMarcas {
    data: Marca[];
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
    marcas: PaginatedMarcas;
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Marcas', href: marcasIndex().url },
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

// Delete dialog
const showDeleteDialog = ref(false);
const marcaToDelete = ref<Marca | null>(null);

// Watch filters and update URL
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch([search, status], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            marcasIndex().url,
            {
                search: search.value || undefined,
                status: status.value !== 'all' ? status.value : undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, 300);
});

const openDeleteDialog = (marca: Marca) => {
    marcaToDelete.value = marca;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    marcaToDelete.value = null;
    showDeleteDialog.value = false;
};
</script>

<template>
    <Head title="Marcas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Tag class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Marcas</h1>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Gerencie as marcas dos produtos
                    </p>
                </div>
                <Link :href="marcasCreate().url">
                    <Button class="w-full sm:w-auto">
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Marca
                    </Button>
                </Link>
            </div>

            <!-- Filters Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center">
                        <div class="relative flex-1">
                            <Search
                                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                v-model="search"
                                placeholder="Buscar por nome ou descrição..."
                                class="pl-10"
                            />
                        </div>

                        <select
                            v-model="status"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[150px]"
                        >
                            <option value="all">Todos</option>
                            <option value="ativo">Ativos</option>
                            <option value="inativo">Inativos</option>
                        </select>
                    </div>
                </CardContent>
            </Card>

            <!-- Table Card -->
            <Card class="border-border shadow-sm">
                <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome</TableHead>
                            <TableHead>Descrição</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Criado em</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="marcas.data.length === 0"
                            class="hover:bg-transparent"
                        >
                            <TableCell colspan="5" class="text-center">
                                <div class="py-8 text-muted-foreground">
                                    Nenhuma marca encontrada
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="marca in marcas.data" :key="marca.id">
                            <TableCell class="font-medium">
                                {{ marca.nome }}
                            </TableCell>
                            <TableCell>
                                <span v-if="marca.descricao" class="text-sm text-muted-foreground">
                                    {{ marca.descricao }}
                                </span>
                                <span v-else class="text-sm text-muted-foreground italic">
                                    Sem descrição
                                </span>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="marca.ativo ? 'default' : 'secondary'"
                                >
                                    {{ marca.ativo ? 'Ativo' : 'Inativo' }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ new Date(marca.created_at).toLocaleDateString('pt-BR') }}
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="MarcaController.show(marca).url">
                                        <Button variant="ghost" size="sm" title="Visualizar">
                                            <Eye class="h-4 w-4 text-green-600 hover:text-green-700" />
                                            <span class="sr-only">Visualizar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Link :href="MarcaController.edit(marca).url">
                                        <Button variant="ghost" size="sm" title="Editar">
                                            <Pencil class="h-4 w-4 text-orange-600 hover:text-orange-700" />
                                            <span class="sr-only">Editar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        title="Excluir"
                                        @click="openDeleteDialog(marca)"
                                    >
                                        <Trash2 class="h-4 w-4 text-red-600 hover:text-red-700" />
                                        <span class="sr-only">Excluir</span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                </div>
            </Card>

            <!-- Pagination -->
            <div
                v-if="marcas.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ marcas.data.length }} de {{ marcas.total }} marcas
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in marcas.links"
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
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent v-if="marcaToDelete" class="sm:max-w-md">
                <Form
                    v-bind="MarcaController.destroy.form(marcaToDelete)"
                    :options="{
                        preserveScroll: true,
                    }"
                    @success="() => {
                        toast.success('Marca excluída com sucesso!');
                        closeDeleteDialog();
                    }"
                    @error="toast.error('Erro ao excluir marca', 'Tente novamente mais tarde.')"
                    class="space-y-6"
                    v-slot="{ processing }"
                >
                    <DialogHeader class="space-y-2">
                        <DialogTitle class="text-xl font-semibold text-red-600 flex items-center gap-2">
                            <Trash2 class="w-6 h-6" />
                            Excluir Marca
                        </DialogTitle>
                        <DialogDescription class="text-gray-600 pt-2">
                            Você está prestes a excluir a marca
                            <strong class="font-medium text-gray-800">{{ marcaToDelete.nome }}</strong>.
                            
                            <span class="text-sm text-red-500 font-medium">
                                Esta ação é **irreversível** e todos os dados associados serão perdidos.
                            </span>
                        </DialogDescription>
                    </DialogHeader>

                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <p class="font-semibold">Atenção:</p>
                        <p>Confirme se esta é a marca correta antes de prosseguir.</p>
                    </div>

                    <DialogFooter class="flex justify-end gap-3 pt-4">
                        <DialogClose as-child>
                            <Button
                                variant="outline"
                                @click="closeDeleteDialog"
                                :disabled="processing"
                                class="flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </Button>
                        </DialogClose>

                        <Button
                            type="submit"
                            variant="destructive"
                            :disabled="processing"
                            class="flex items-center gap-1"
                        >
                            <template v-if="processing">
                                <Loader2 class="w-4 h-4 animate-spin" />
                                Excluindo...
                            </template>
                            <template v-else>
                                <Trash2 class="w-4 h-4" />
                                Excluir Definitivamente
                            </template>
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
