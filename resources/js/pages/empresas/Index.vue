<script setup lang="ts">
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { index as empresasIndex, create as empresasCreate } from '@/routes/empresas';
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
import { Plus, Search, Pencil, Trash2, Eye, Building2, Store } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Empresa {
    id: number;
    uuid: string;
    razao_social: string;
    nome_fantasia: string;
    cnpj: string | null;
    email: string;
    logo_path: string | null;
    logo_url: string | null;
    telefone: string | null;
    ativo: boolean;
    data_adesao: string;
    data_expiracao: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginatedEmpresas {
    data: Empresa[];
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
    empresas: PaginatedEmpresas;
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Empresas', href: empresasIndex().url },
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
const empresaToDelete = ref<Empresa | null>(null);

// Watch filters and update URL
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch([search, status], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            empresasIndex().url,
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

const openDeleteDialog = (empresa: Empresa) => {
    empresaToDelete.value = empresa;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    empresaToDelete.value = null;
    showDeleteDialog.value = false;
};

const formatDate = (date: string | null) => {
    if (!date) {
        return null; // Retorna null para indicar que não há data
    }
    return new Date(date).toLocaleDateString('pt-BR');
};

// Verifica o status da data de expiração
const getExpirationStatus = (date: string | null) => {
    if (!date) return 'none';
    
    const expirationDate = new Date(date);
    const today = new Date();
    
    // Calcular a diferença em dias
    const diffTime = expirationDate.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    // Retorna o status baseado nos dias restantes
    if (diffDays < 0) {
        return 'expired'; // Já expirou
    } else if (diffDays <= 30) {
        return 'near'; // Próximo da expiração (30 dias ou menos)
    } else {
        return 'ok'; // Ainda está longe de expirar
    }
};

const formatCNPJ = (cnpj: string | null) => {
    if (!cnpj) return '-';
    return cnpj;
};
</script>

<template>
    <Head title="Empresas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Building2 class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Empresas</h1>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Gerencie as empresas cadastradas no sistema
                    </p>
                </div>
                <Link :href="empresasCreate().url">
                    <Button class="w-full sm:w-auto">
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Empresa
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
                                placeholder="Buscar por razão social, nome fantasia ou CNPJ..."
                                class="pl-10"
                            />
                        </div>

                        <select
                            v-model="status"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[200px]"
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
                            <TableHead>Logo</TableHead>
                            <TableHead>Razão Social</TableHead>
                            <TableHead>Nome Fantasia</TableHead>
                            <TableHead>CNPJ</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Data Adesão</TableHead>
                            <TableHead>Data Expiração</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="empresas.data.length === 0"
                            class="hover:bg-transparent"
                        >
                            <TableCell colspan="9" class="text-center">
                                <div class="py-8 text-muted-foreground">
                                    Nenhuma empresa encontrada
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="empresa in empresas.data" :key="empresa.id">
                            <TableCell>
                                <div class="w-10 h-10 rounded overflow-hidden flex items-center justify-center bg-gray-50 border">
                                    <img 
                                        v-if="empresa.logo_url" 
                                        :src="empresa.logo_url" 
                                        :alt="`Logo ${empresa.nome_fantasia}`"
                                        class="max-w-full max-h-full object-contain"
                                        @error="() => {}"
                                    />
                                    <span v-else class="text-xs text-muted-foreground">Sem logo</span>
                                </div>
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ empresa.razao_social }}
                            </TableCell>
                            <TableCell>{{ empresa.nome_fantasia }}</TableCell>
                            <TableCell>{{ formatCNPJ(empresa.cnpj) }}</TableCell>
                            <TableCell>{{ empresa.email }}</TableCell>
                            <TableCell>
                                <Badge
                                    :variant="empresa.ativo ? 'default' : 'secondary'"
                                >
                                    {{ empresa.ativo ? 'Ativo' : 'Inativo' }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <span v-if="formatDate(empresa.data_adesao)">
                                    {{ formatDate(empresa.data_adesao) }}
                                </span>
                                <span v-else class="text-muted-foreground italic">
                                    Não definido
                                </span>
                            </TableCell>
                            <TableCell>
                                <span 
                                    v-if="formatDate(empresa.data_expiracao)"
                                    :class="{
                                        'text-red-600 font-medium': getExpirationStatus(empresa.data_expiracao) === 'expired',
                                        'text-amber-600 font-medium': getExpirationStatus(empresa.data_expiracao) === 'near'
                                    }"
                                >
                                    {{ formatDate(empresa.data_expiracao) }}
                                </span>
                                <span v-else class="text-muted-foreground italic">
                                    Não definido
                                </span>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/empresas/${empresa.id}/lojas`">
                                        <Button variant="ghost" size="sm" title="Lojas">
                                            <Store class="h-4 w-4 text-blue-600 hover:text-blue-700" />
                                            <span class="sr-only">Lojas</span>
                                        </Button>
                                    </Link>
                                    
                                    <Link :href="EmpresaController.show(empresa).url">
                                        <Button variant="ghost" size="sm" title="Visualizar">
                                            <Eye class="h-4 w-4 text-green-600 hover:text-green-700" />
                                            <span class="sr-only">Visualizar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Link :href="EmpresaController.edit(empresa).url">
                                        <Button variant="ghost" size="sm" title="Editar">
                                            <Pencil class="h-4 w-4 text-orange-600 hover:text-orange-700" />
                                            <span class="sr-only">Editar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        title="Excluir"
                                        @click="openDeleteDialog(empresa)"
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
                v-if="empresas.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ empresas.data.length }} de {{ empresas.total }} empresas
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in empresas.links"
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
            <DialogContent v-if="empresaToDelete" class="sm:max-w-md">
                <Form
                    v-bind="EmpresaController.destroy.form(empresaToDelete)"
                    :options="{
                        preserveScroll: true,
                    }"
                    @success="() => {
                        toast.success('Empresa excluída com sucesso!');
                        closeDeleteDialog();
                    }"
                    @error="toast.error('Erro ao excluir empresa', 'Tente novamente mais tarde.')"
                    class="space-y-6"
                    v-slot="{ processing }"
                >
                    <DialogHeader class="space-y-2">
                        <DialogTitle class="text-xl font-semibold text-red-600 flex items-center gap-2">
                            <Trash2 class="w-6 h-6" />
                            Excluir Empresa
                        </DialogTitle>
                        <DialogDescription class="text-gray-600 pt-2">
                            Você está prestes a excluir a empresa
                            <strong class="font-medium text-gray-800">{{ empresaToDelete.razao_social }}</strong>.
                            

                            <span class="text-sm text-red-500 font-medium">
                                Esta ação é **irreversível** e todos os dados associados serão perdidos.
                            </span>
                        </DialogDescription>
                    </DialogHeader>

                    <!-- Conteúdo opcional pode ser adicionado aqui para mais elegância, como um aviso visual -->
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <p class="font-semibold">Atenção:</p>
                        <p>Confirme se esta é a empresa correta antes de prosseguir.</p>
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

