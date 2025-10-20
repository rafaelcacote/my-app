<script setup lang="ts">
import ProdutoController from '@/actions/App/Http/Controllers/ProdutoController';
import { index as produtosIndex, create as produtosCreate } from '@/routes/produtos';
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
import { Plus, Search, Pencil, Trash2, Eye, Package, DollarSign } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Categoria {
    id: number;
    nome: string;
}

interface Marca {
    id: number;
    nome: string;
}

interface Produto {
    id: number;
    uuid: string;
    sku: string;
    nome: string;
    descricao: string | null;
    categoria_id: number;
    marca_id: number;
    preco_custo: number;
    preco_venda: number;
    margem_lucro: number | null;
    ativo: boolean;
    controle_estoque: boolean;
    created_at: string;
    updated_at: string;
    categoria: Categoria;
    marca: Marca;
}

interface PaginatedProdutos {
    data: Produto[];
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
    produtos: PaginatedProdutos;
    categorias: Categoria[];
    marcas: Marca[];
    filters: {
        search?: string;
        status?: string;
        controle_estoque?: string;
        categoria_id?: string;
        marca_id?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Produtos', href: produtosIndex().url },
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
const controleEstoque = ref(props.filters.controle_estoque || 'all');
const categoriaId = ref(props.filters.categoria_id || '');
const marcaId = ref(props.filters.marca_id || '');

// Delete dialog
const showDeleteDialog = ref(false);
const produtoToDelete = ref<Produto | null>(null);

// Watch filters and update URL
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch([search, status, controleEstoque, categoriaId, marcaId], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            produtosIndex().url,
            {
                search: search.value || undefined,
                status: status.value !== 'all' ? status.value : undefined,
                controle_estoque: controleEstoque.value !== 'all' ? controleEstoque.value : undefined,
                categoria_id: categoriaId.value || undefined,
                marca_id: marcaId.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, 300);
});

const openDeleteDialog = (produto: Produto) => {
    produtoToDelete.value = produto;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    produtoToDelete.value = null;
    showDeleteDialog.value = false;
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(price);
};
</script>

<template>
    <Head title="Produtos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Package class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Produtos</h1>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Gerencie o catálogo de produtos
                    </p>
                </div>
                <Link :href="produtosCreate().url">
                    <Button class="w-full sm:w-auto">
                        <Plus class="mr-2 h-4 w-4" />
                        Novo Produto
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
                                placeholder="Buscar por nome, SKU ou descrição..."
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

                        <select
                            v-model="controleEstoque"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[180px]"
                        >
                            <option value="all">Controle Estoque</option>
                            <option value="1">Com Controle</option>
                            <option value="0">Sem Controle</option>
                        </select>

                        <select
                            v-model="categoriaId"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[150px]"
                        >
                            <option value="">Todas Categorias</option>
                            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                                {{ categoria.nome }}
                            </option>
                        </select>

                        <select
                            v-model="marcaId"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[150px]"
                        >
                            <option value="">Todas Marcas</option>
                            <option v-for="marca in marcas" :key="marca.id" :value="marca.id">
                                {{ marca.nome }}
                            </option>
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
                            <TableHead>SKU</TableHead>
                            <TableHead>Nome</TableHead>
                            <TableHead>Categoria</TableHead>
                            <TableHead>Marca</TableHead>
                            <TableHead>Preço Custo</TableHead>
                            <TableHead>Preço Venda</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Controle Estoque</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="produtos.data.length === 0"
                            class="hover:bg-transparent"
                        >
                            <TableCell colspan="9" class="text-center">
                                <div class="py-8 text-muted-foreground">
                                    Nenhum produto encontrado
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="produto in produtos.data" :key="produto.id">
                            <TableCell class="font-mono text-sm">
                                {{ produto.sku }}
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ produto.nome }}
                            </TableCell>
                            <TableCell>{{ produto.categoria.nome }}</TableCell>
                            <TableCell>{{ produto.marca.nome }}</TableCell>
                            <TableCell class="text-right">
                                {{ formatPrice(produto.preco_custo) }}
                            </TableCell>
                            <TableCell class="text-right font-medium">
                                {{ formatPrice(produto.preco_venda) }}
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="produto.ativo ? 'default' : 'secondary'"
                                >
                                    {{ produto.ativo ? 'Ativo' : 'Inativo' }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="produto.controle_estoque ? 'default' : 'outline'"
                                >
                                    {{ produto.controle_estoque ? 'Sim' : 'Não' }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="ProdutoController.show(produto).url">
                                        <Button variant="ghost" size="sm" title="Visualizar">
                                            <Eye class="h-4 w-4 text-green-600 hover:text-green-700" />
                                            <span class="sr-only">Visualizar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Link :href="ProdutoController.edit(produto).url">
                                        <Button variant="ghost" size="sm" title="Editar">
                                            <Pencil class="h-4 w-4 text-orange-600 hover:text-orange-700" />
                                            <span class="sr-only">Editar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        title="Excluir"
                                        @click="openDeleteDialog(produto)"
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
                v-if="produtos.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ produtos.data.length }} de {{ produtos.total }} produtos
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in produtos.links"
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
            <DialogContent v-if="produtoToDelete" class="sm:max-w-md">
                <Form
                    v-bind="ProdutoController.destroy.form(produtoToDelete)"
                    :options="{
                        preserveScroll: true,
                    }"
                    @success="() => {
                        toast.success('Produto excluído com sucesso!');
                        closeDeleteDialog();
                    }"
                    @error="toast.error('Erro ao excluir produto', 'Tente novamente mais tarde.')"
                    class="space-y-6"
                    v-slot="{ processing }"
                >
                    <DialogHeader class="space-y-2">
                        <DialogTitle class="text-xl font-semibold text-red-600 flex items-center gap-2">
                            <Trash2 class="w-6 h-6" />
                            Excluir Produto
                        </DialogTitle>
                        <DialogDescription class="text-gray-600 pt-2">
                            Você está prestes a excluir o produto
                            <strong class="font-medium text-gray-800">{{ produtoToDelete.nome }}</strong>.
                            
                            <span class="text-sm text-red-500 font-medium">
                                Esta ação é **irreversível** e todos os dados associados serão perdidos.
                            </span>
                        </DialogDescription>
                    </DialogHeader>

                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <p class="font-semibold">Atenção:</p>
                        <p>Confirme se este é o produto correto antes de prosseguir.</p>
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
