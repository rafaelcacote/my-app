<script setup lang="ts">
import TamanhoController from '@/actions/App/Http/Controllers/TamanhoController';
import { index as tamanhosIndex, create as tamanhosCreate } from '@/routes/tamanhos';
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
import { Plus, Search, Pencil, Trash2, Eye, Ruler } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Tamanho {
    id: number;
    nome: string;
    tipo: string;
    ordem: number;
    created_at: string;
    updated_at: string;
}

interface PaginatedTamanhos {
    data: Tamanho[];
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
    tamanhos: PaginatedTamanhos;
    filters: {
        search?: string;
        tipo?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tamanhos', href: tamanhosIndex().url },
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

// Delete dialog
const showDeleteDialog = ref(false);
const tamanhoToDelete = ref<Tamanho | null>(null);

// Watch filters and update URL
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch([search, tipo], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            tamanhosIndex().url,
            {
                search: search.value || undefined,
                tipo: tipo.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, 300);
});

const openDeleteDialog = (tamanho: Tamanho) => {
    tamanhoToDelete.value = tamanho;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    tamanhoToDelete.value = null;
    showDeleteDialog.value = false;
};

const getTipoLabel = (tipo: string) => {
    const tipos = {
        'alfanumerico': 'Alfanumérico',
        'numerico': 'Numérico',
        'texto': 'Texto'
    };
    return tipos[tipo as keyof typeof tipos] || tipo;
};
</script>

<template>
    <Head title="Tamanhos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Ruler class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Tamanhos</h1>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Gerencie os tamanhos dos produtos
                    </p>
                </div>
                <Link :href="tamanhosCreate().url">
                    <Button class="w-full sm:w-auto">
                        <Plus class="mr-2 h-4 w-4" />
                        Novo Tamanho
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
                                placeholder="Buscar por nome..."
                                class="pl-10"
                            />
                        </div>

                        <select
                            v-model="tipo"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[180px]"
                        >
                            <option value="">Todos os Tipos</option>
                            <option value="alfanumerico">Alfanumérico</option>
                            <option value="numerico">Numérico</option>
                            <option value="texto">Texto</option>
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
                            <TableHead>Ordem</TableHead>
                            <TableHead>Nome</TableHead>
                            <TableHead>Tipo</TableHead>
                            <TableHead>Criado em</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="tamanhos.data.length === 0"
                            class="hover:bg-transparent"
                        >
                            <TableCell colspan="5" class="text-center">
                                <div class="py-8 text-muted-foreground">
                                    Nenhum tamanho encontrado
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="tamanho in tamanhos.data" :key="tamanho.id">
                            <TableCell class="font-mono text-sm">
                                {{ tamanho.ordem }}
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ tamanho.nome }}
                            </TableCell>
                            <TableCell>
                                <Badge variant="outline">
                                    {{ getTipoLabel(tamanho.tipo) }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{ new Date(tamanho.created_at).toLocaleDateString('pt-BR') }}
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="TamanhoController.show(tamanho).url">
                                        <Button variant="ghost" size="sm" title="Visualizar">
                                            <Eye class="h-4 w-4 text-blue-600 hover:text-blue-700" />
                                            <span class="sr-only">Visualizar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Link :href="TamanhoController.edit(tamanho).url">
                                        <Button variant="ghost" size="sm" title="Editar">
                                            <Pencil class="h-4 w-4 text-orange-600 hover:text-orange-700" />
                                            <span class="sr-only">Editar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        title="Excluir"
                                        @click="openDeleteDialog(tamanho)"
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
                v-if="tamanhos.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ tamanhos.data.length }} de {{ tamanhos.total }} tamanhos
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in tamanhos.links"
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
            <DialogContent v-if="tamanhoToDelete" class="sm:max-w-md">
                <Form
                    v-bind="TamanhoController.destroy.form(tamanhoToDelete)"
                    :options="{
                        preserveScroll: true,
                    }"
                    @success="() => {
                        toast.success('Tamanho excluído com sucesso!');
                        closeDeleteDialog();
                    }"
                    @error="toast.error('Erro ao excluir tamanho', 'Tente novamente mais tarde.')"
                    class="space-y-6"
                    v-slot="{ processing }"
                >
                    <DialogHeader class="space-y-2">
                        <DialogTitle class="text-xl font-semibold text-red-600 flex items-center gap-2">
                            <Trash2 class="w-6 h-6" />
                            Excluir Tamanho
                        </DialogTitle>
                        <DialogDescription class="text-gray-600 pt-2">
                            Você está prestes a excluir o tamanho
                            <strong class="font-medium text-gray-800">{{ tamanhoToDelete.nome }}</strong>.
                            
                            <span class="text-sm text-red-500 font-medium">
                                Esta ação é **irreversível** e todos os dados associados serão perdidos.
                            </span>
                        </DialogDescription>
                    </DialogHeader>

                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <p class="font-semibold">Atenção:</p>
                        <p>Confirme se este é o tamanho correto antes de prosseguir.</p>
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
