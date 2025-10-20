<script setup lang="ts">
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
import { Plus, Search, Pencil, Trash2, Store, ArrowLeft, Loader2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Empresa {
    id: number;
    razao_social: string;
    nome_fantasia: string;
}

interface Loja {
    id: number;
    empresa_id: number;
    nome: string;
    cnpj: string | null;
    telefone: string | null;
    email: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface PaginatedLojas {
    data: Loja[];
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
    empresa: Empresa;
    lojas: PaginatedLojas;
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Empresas', href: '/empresas' },
    { title: props.empresa.nome_fantasia, href: `/empresas/${props.empresa.id}` },
    { title: 'Lojas', href: `/empresas/${props.empresa.id}/lojas` },
];

onMounted(() => {
    const flash = page.props.flash as any;
    if (flash?.success) {
        toast.success(flash.success as string);
    }
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');

const showDeleteDialog = ref(false);
const lojaToDelete = ref<Loja | null>(null);

let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch([search, status], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            `/empresas/${props.empresa.id}/lojas`,
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

const openDeleteDialog = (loja: Loja) => {
    lojaToDelete.value = loja;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    lojaToDelete.value = null;
    showDeleteDialog.value = false;
};

const deleteLoja = () => {
    if (!lojaToDelete.value) return;
    
    router.delete(`/empresas/${props.empresa.id}/lojas/${lojaToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Loja excluída com sucesso!');
            closeDeleteDialog();
        },
        onError: () => {
            toast.error('Erro ao excluir loja', 'Tente novamente mais tarde.');
        },
    });
};

const formatCNPJ = (cnpj: string | null) => {
    if (!cnpj) return '-';
    return cnpj;
};

const formatPhone = (phone: string | null) => {
    if (!phone) return '-';
    return phone;
};
</script>

<template>
    <Head :title="`Lojas - ${empresa.nome_fantasia}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Store class="h-8 w-8 text-primary" />
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-foreground">Lojas</h1>
                            <p class="text-sm text-muted-foreground">
                                {{ empresa.nome_fantasia }}
                            </p>
                        </div>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Gerencie as lojas da empresa
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="'/empresas'">
                        <Button variant="outline" class="w-full sm:w-auto">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Voltar
                        </Button>
                    </Link>
                    <Link :href="`/empresas/${empresa.id}/lojas/create`">
                        <Button class="w-full sm:w-auto">
                            <Plus class="mr-2 h-4 w-4" />
                            Nova Loja
                        </Button>
                    </Link>
                </div>
            </div>

            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center">
                        <div class="relative flex-1">
                            <Search
                                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                v-model="search"
                                placeholder="Buscar por nome, CNPJ ou email..."
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

            <Card class="border-border shadow-sm">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nome</TableHead>
                                <TableHead>CNPJ</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Telefone</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-if="lojas.data.length === 0"
                                class="hover:bg-transparent"
                            >
                                <TableCell colspan="6" class="text-center">
                                    <div class="py-8 text-muted-foreground">
                                        Nenhuma loja encontrada
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="loja in lojas.data" :key="loja.id">
                                <TableCell class="font-medium">
                                    {{ loja.nome }}
                                </TableCell>
                                <TableCell>{{ formatCNPJ(loja.cnpj) }}</TableCell>
                                <TableCell>{{ loja.email || '-' }}</TableCell>
                                <TableCell>{{ formatPhone(loja.telefone) }}</TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="loja.ativo ? 'default' : 'secondary'"
                                    >
                                        {{ loja.ativo ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="`/empresas/${empresa.id}/lojas/${loja.id}/edit`">
                                            <Button variant="ghost" size="sm" title="Editar">
                                                <Pencil class="h-4 w-4" />
                                                <span class="sr-only">Editar</span>
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            title="Excluir"
                                            @click="openDeleteDialog(loja)"
                                        >
                                            <Trash2 class="h-4 w-4 text-red-500" />
                                            <span class="sr-only">Excluir</span>
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </Card>

            <div
                v-if="lojas.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ lojas.data.length }} de {{ lojas.total }} lojas
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in lojas.links"
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

        <Dialog v-model:open="showDeleteDialog">
            <DialogContent v-if="lojaToDelete" class="sm:max-w-md">
                <DialogHeader class="space-y-2">
                    <DialogTitle class="text-xl font-semibold text-red-600 flex items-center gap-2">
                        <Trash2 class="w-6 h-6" />
                        Excluir Loja
                    </DialogTitle>
                    <DialogDescription class="text-gray-600 pt-2">
                        Você está prestes a excluir a loja
                        <strong class="font-medium text-gray-800">{{ lojaToDelete.nome }}</strong>.
                        
                        <span class="text-sm text-red-500 font-medium">
                            Esta ação é irreversível.
                        </span>
                    </DialogDescription>
                </DialogHeader>

                <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    <p class="font-semibold">Atenção:</p>
                    <p>Confirme se esta é a loja correta antes de prosseguir.</p>
                </div>

                <DialogFooter class="flex justify-end gap-3 pt-4">
                    <DialogClose as-child>
                        <Button
                            variant="outline"
                            @click="closeDeleteDialog"
                        >
                            Cancelar
                        </Button>
                    </DialogClose>

                    <Button
                        variant="destructive"
                        @click="deleteLoja"
                    >
                        <Trash2 class="w-4 h-4 mr-2" />
                        Excluir Definitivamente
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
