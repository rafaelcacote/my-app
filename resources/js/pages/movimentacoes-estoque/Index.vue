<script setup lang="ts">
import MovimentacaoEstoqueController from '@/actions/App/Http/Controllers/MovimentacaoEstoqueController';
import { index as movimentacoesEstoqueIndex, create as movimentacoesEstoqueCreate } from '@/routes/movimentacoes-estoque';
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
import { Plus, Search, Pencil, Trash2, Eye, BarChart3, ArrowUp, ArrowDown, RotateCcw, ArrowRightLeft } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Loja {
    id: number;
    nome: string;
}

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
}

interface ProdutoVariacao {
    id: number;
    produto: Produto;
    tamanho: Tamanho;
    cor: Cor;
}

interface Usuario {
    id: number;
    name: string;
}

interface MovimentacaoEstoque {
    id: number;
    loja_id: number;
    produto_variacao_id: number;
    tipo: string;
    quantidade: number;
    quantidade_anterior: number;
    quantidade_atual: number;
    motivo: string | null;
    observacao: string | null;
    usuario_id: number;
    created_at: string;
    loja: Loja;
    produto_variacao: ProdutoVariacao;
    usuario: Usuario;
}

interface PaginatedMovimentacoesEstoque {
    data: MovimentacaoEstoque[];
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
    movimentacoesEstoque: PaginatedMovimentacoesEstoque;
    lojas: Loja[];
    produtoVariacoes: ProdutoVariacao[];
    filters: {
        search?: string;
        tipo?: string;
        loja_id?: string;
        produto_variacao_id?: string;
        usuario_id?: string;
        data_inicio?: string;
        data_fim?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Movimentações de Estoque', href: movimentacoesEstoqueIndex().url },
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
const tipo = ref(props.filters.tipo || '');
const lojaId = ref(props.filters.loja_id || '');
const produtoVariacaoId = ref(props.filters.produto_variacao_id || '');
const dataInicio = ref(props.filters.data_inicio || '');
const dataFim = ref(props.filters.data_fim || '');

// Delete dialog
const showDeleteDialog = ref(false);
const movimentacaoEstoqueToDelete = ref<MovimentacaoEstoque | null>(null);

// Watch filters and update URL
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch([search, tipo, lojaId, produtoVariacaoId, dataInicio, dataFim], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            movimentacoesEstoqueIndex().url,
            {
                search: search.value || undefined,
                tipo: tipo.value || undefined,
                loja_id: lojaId.value || undefined,
                produto_variacao_id: produtoVariacaoId.value || undefined,
                data_inicio: dataInicio.value || undefined,
                data_fim: dataFim.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, 300);
});

const openDeleteDialog = (movimentacaoEstoque: MovimentacaoEstoque) => {
    movimentacaoEstoqueToDelete.value = movimentacaoEstoque;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    movimentacaoEstoqueToDelete.value = null;
    showDeleteDialog.value = false;
};

const getTipoIcon = (tipo: string) => {
    const icons = {
        'entrada': ArrowUp,
        'saida': ArrowDown,
        'ajuste': RotateCcw,
        'transferencia': ArrowRightLeft
    };
    return icons[tipo as keyof typeof icons] || BarChart3;
};

const getTipoLabel = (tipo: string) => {
    const labels = {
        'entrada': 'Entrada',
        'saida': 'Saída',
        'ajuste': 'Ajuste',
        'transferencia': 'Transferência'
    };
    return labels[tipo as keyof typeof labels] || tipo;
};

const getTipoVariant = (tipo: string) => {
    const variants = {
        'entrada': 'default',
        'saida': 'destructive',
        'ajuste': 'secondary',
        'transferencia': 'outline'
    };
    return variants[tipo as keyof typeof variants] || 'default';
};
</script>

<template>
    <Head title="Movimentações de Estoque" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <BarChart3 class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Movimentações de Estoque</h1>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Gerencie as movimentações de estoque
                    </p>
                </div>
                <Link :href="movimentacoesEstoqueCreate().url">
                    <Button class="w-full sm:w-auto">
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Movimentação
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
                                placeholder="Buscar por motivo ou observação..."
                                class="pl-10"
                            />
                        </div>

                        <select
                            v-model="tipo"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[150px]"
                        >
                            <option value="">Todos os Tipos</option>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída Jarvis</option>
                            <option value="ajuste">Ajuste</option>
                            <option value="transferencia">Transferência</option>
                        </select>

                        <select
                            v-model="lojaId"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[150px]"
                        >
                            <option value="">Todas Lojas</option>
                            <option v-for="loja in lojas" :key="loja.id" :value="loja.id">
                                {{ loja.nome }}
                            </option>
                        </select>

                        <div class="flex gap-2">
                            <Input
                                v-model="dataInicio"
                                type="date"
                                placeholder="Data início"
                                class="md:w-[150px]"
                            />
                            <Input
                                v-model="dataFim"
                                type="date"
                                placeholder="Data fim"
                                class="md:w-[150px]"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Table Card -->
            <Card class="border-border shadow-sm">
                <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Tipo</TableHead>
                            <TableHead>Produto</TableHead>
                            <TableHead>Tamanho</TableHead>
                            <TableHead>Cor</TableHead>
                            <TableHead>Loja</TableHead>
                            <TableHead>Quantidade</TableHead>
                            <TableHead>Usuário</TableHead>
                            <TableHead>Data</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="movimentacoesEstoque.data.length === 0"
                            class="hover:bg-transparent"
                        >
                            <TableCell colspan="9" class="text-center">
                                <div class="py-8 text-muted-foreground">
                                    Nenhuma movimentação encontrada
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="movimentacao in movimentacoesEstoque.data" :key="movimentacao.id">
                            <TableCell>
                                <Badge :variant="getTipoVariant(movimentacao.tipo)" class="flex items-center gap-1 w-fit">
                                    <component :is="getTipoIcon(movimentacao.tipo)" class="w-3 h-3" />
                                    {{ getTipoLabel(movimentacao.tipo) }}
                                </Badge>
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ movimentacao.produto_variacao.produto.nome }}
                            </TableCell>
                            <TableCell>{{ movimentacao.produto_variacao.tamanho.nome }}</TableCell>
                            <TableCell>{{ movimentacao.produto_variacao.cor.nome }}</TableCell>
                            <TableCell>{{ movimentacao.loja.nome }}</TableCell>
                            <TableCell class="text-right font-mono">
                                {{ movimentacao.quantidade }}
                            </TableCell>
                            <TableCell class="text-sm">
                                {{ movimentacao.usuario.name }}
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ new Date(movimentacao.created_at).toLocaleDateString('pt-BR') }}
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="MovimentacaoEstoqueController.show(movimentacao).url">
                                        <Button variant="ghost" size="sm" title="Visualizar">
                                            <Eye class="h-4 w-4 text-green-600 hover:text-green-700" />
                                            <span class="sr-only">Visualizar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Link :href="MovimentacaoEstoqueController.edit(movimentacao).url">
                                        <Button variant="ghost" size="sm" title="Editar">
                                            <Pencil class="h-4 w-4 text-orange-600 hover:text-orange-700" />
                                            <span class="sr-only">Editar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        title="Excluir"
                                        @click="openDeleteDialog(movimentacao)"
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
                v-if="movimentacoesEstoque.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ movimentacoesEstoque.data.length }} de {{ movimentacoesEstoque.total }} movimentações
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in movimentacoesEstoque.links"
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
            <DialogContent v-if="movimentacaoEstoqueToDelete" class="sm:max-w-md">
                <Form
                    v-bind="MovimentacaoEstoqueController.destroy.form(movimentacaoEstoqueToDelete)"
                    :options="{
                        preserveScroll: true,
                    }"
                    @success="() => {
                        toast.success('Movimentação excluída com sucesso!');
                        closeDeleteDialog();
                    }"
                    @error="toast.error('Erro ao excluir movimentação', 'Tente novamente mais tarde.')"
                    class="space-y-6"
                    v-slot="{ processing }"
                >
                    <DialogHeader class="space-y-2">
                        <DialogTitle class="text-xl font-semibold text-red-600 flex items-center gap-2">
                            <Trash2 class="w-6 h-6" />
                            Excluir Movimentação
                        </DialogTitle>
                        <DialogDescription class="text-gray-600 pt-2">
                            Você está prestes a excluir a movimentação de estoque do produto
                            <strong class="font-medium text-gray-800">{{ movimentacaoEstoqueToDelete.produto_variacao.produto.nome }}</strong>.
                            
                            <span class="text-sm text-red-500 font-medium">
                                Esta ação é **irreversível** e todos os dados associados serão perdidos.
                            </span>
                        </DialogDescription>
                    </DialogHeader>

                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <p class="font-semibold">Atenção:</p>
                        <p>Confirme se esta é a movimentação correta antes de prosseguir.</p>
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
