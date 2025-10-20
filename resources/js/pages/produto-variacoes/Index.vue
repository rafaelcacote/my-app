<script setup lang="ts">
import ProdutoVariacaoController from '@/actions/App/Http/Controllers/ProdutoVariacaoController';
import { index as produtoVariacoesIndex, create as produtoVariacoesCreate } from '@/routes/produto-variacoes';
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
import { Plus, Search, Pencil, Trash2, Eye, Layers, DollarSign } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Produto {
    id: number;
    nome: string;
}

interface Tamanho {
    id: number;
    nome: string;
}

interface Cor {
    id: number;
    nome: string;
    codigo_hex: string | null;
}

interface ProdutoVariacao {
    id: number;
    produto_id: number;
    tamanho_id: number;
    cor_id: number;
    sku_variacao: string | null;
    preco_adicional: number;
    ativo: boolean;
    created_at: string;
    updated_at: string;
    produto: Produto;
    tamanho: Tamanho;
    cor: Cor;
}

interface PaginatedProdutoVariacoes {
    data: ProdutoVariacao[];
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
    produtoVariacoes: PaginatedProdutoVariacoes;
    produtos: Produto[];
    tamanhos: Tamanho[];
    cores: Cor[];
    filters: {
        search?: string;
        status?: string;
        produto_id?: string;
        tamanho_id?: string;
        cor_id?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Variações de Produto', href: produtoVariacoesIndex().url },
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
const produtoId = ref(props.filters.produto_id || '');
const tamanhoId = ref(props.filters.tamanho_id || '');
const corId = ref(props.filters.cor_id || '');

// Delete dialog
const showDeleteDialog = ref(false);
const produtoVariacaoToDelete = ref<ProdutoVariacao | null>(null);

// Watch filters and update URL
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch([search, status, produtoId, tamanhoId, corId], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            produtoVariacoesIndex().url,
            {
                search: search.value || undefined,
                status: status.value !== 'all' ? status.value : undefined,
                produto_id: produtoId.value || undefined,
                tamanho_id: tamanhoId.value || undefined,
                cor_id: corId.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, 300);
});

const openDeleteDialog = (produtoVariacao: ProdutoVariacao) => {
    produtoVariacaoToDelete.value = produtoVariacao;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    produtoVariacaoToDelete.value = null;
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
    <Head title="Variações de Produto" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Layers class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Variações de Produto</h1>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Gerencie as variações de produtos (tamanho e cor)
                    </p>
                </div>
                <Link :href="produtoVariacoesCreate().url">
                    <Button class="w-full sm:w-auto">
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Variação
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
                                placeholder="Buscar por SKU variação..."
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
                            v-model="produtoId"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[150px]"
                        >
                            <option value="">Todos Produtos</option>
                            <option v-for="produto in produtos" :key="produto.id" :value="produto.id">
                                {{ produto.nome }}
                            </option>
                        </select>

                        <select
                            v-model="tamanhoId"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[150px]"
                        >
                            <option value="">Todos Tamanhos</option>
                            <option v-for="tamanho in tamanhos" :key="tamanho.id" :value="tamanho.id">
                                {{ tamanho.nome }}
                            </option>
                        </select>

                        <select
                            v-model="corId"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[150px]"
                        >
                            <option value="">Todas Cores</option>
                            <option v-for="cor in cores" :key="cor.id" :value="cor.id">
                                {{ cor.nome }}
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
                            <TableHead>SKU Variação</TableHead>
                            <TableHead>Produto</TableHead>
                            <TableHead>Tamanho</TableHead>
                            <TableHead>Cor</TableHead>
                            <TableHead>Preço Adicional</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="produtoVariacoes.data.length === 0"
                            class="hover:bg-transparent"
                        >
                            <TableCell colspan="7" class="text-center">
                                <div class="py-8 text-muted-foreground">
                                    Nenhuma variação encontrada
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="variacao in produtoVariacoes.data" :key="variacao.id">
                            <TableCell class="font-mono text-sm">
                                {{ variacao.sku_variacao || '-' }}
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ variacao.produto.nome }}
                            </TableCell>
                            <TableCell>{{ variacao.tamanho.nome }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <div 
                                        v-if="variacao.cor.codigo_hex"
                                        class="w-4 h-4 rounded border border-gray-300"
                                        :style="{ backgroundColor: variacao.cor.codigo_hex }"
                                    ></div>
                                    <span>{{ variacao.cor.nome }}</span>
                                </div>
                            </TableCell>
                            <TableCell class="text-right">
                                {{ formatPrice(variacao.preco_adicional) }}
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="variacao.ativo ? 'default' : 'secondary'"
                                >
                                    {{ variacao.ativo ? 'Ativo' : 'Inativo' }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="ProdutoVariacaoController.show(variacao).url">
                                        <Button variant="ghost" size="sm" title="Visualizar">
                                            <Eye class="h-4 w-4 text-green-600 hover:text-green-700" />
                                            <span class="sr-only">Visualizar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Link :href="ProdutoVariacaoController.edit(variacao).url">
                                        <Button variant="ghost" size="sm" title="Editar">
                                            <Pencil class="h-4 w-4 text-orange-600 hover:text-orange-700" />
                                            <span class="sr-only">Editar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        title="Excluir"
                                        @click="openDeleteDialog(variacao)"
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
                v-if="produtoVariacoes.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ produtoVariacoes.data.length }} de {{ produtoVariacoes.total }} variações
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in produtoVariacoes.links"
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
            <DialogContent v-if="produtoVariacaoToDelete" class="sm:max-w-md">
                <Form
                    v-bind="ProdutoVariacaoController.destroy.form(produtoVariacaoToDelete)"
                    :options="{
                        preserveScroll: true,
                    }"
                    @success="() => {
                        toast.success('Variação de produto excluída com sucesso!');
                        closeDeleteDialog();
                    }"
                    @error="toast.error('Erro ao excluir variação', 'Tente novamente mais tarde.')"
                    class="space-y-6"
                    v-slot="{ processing }"
                >
                    <DialogHeader class="space-y-2">
                        <DialogTitle class="text-xl font-semibold text-red-600 flex items-center gap-2">
                            <Trash2 class="w-6 h-6" />
                            Excluir Variação
                        </DialogTitle>
                        <DialogDescription class="text-gray-600 pt-2">
                            Você está prestes a excluir a variação do produto
                            <strong class="font-medium text-gray-800">{{ produtoVariacaoToDelete.produto.nome }}</strong>.
                            
                            <span class="text-sm text-red-500 font-medium">
                                Esta ação é **irreversível** e todos os dados associados serão perdidos.
                            </span>
                        </DialogDescription>
                    </DialogHeader>

                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <p class="font-semibold">Atenção:</p>
                        <p>Confirme se esta é a variação correta antes de prosseguir.</p>
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
