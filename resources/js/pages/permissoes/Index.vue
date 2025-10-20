<script setup lang="ts">
import PermissionController from '@/actions/App/Http/Controllers/PermissionController';
import { index as permissoesIndex, create as permissoesCreate } from '@/routes/permissoes';
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
import { Plus, Search, Pencil, Trash2, Key } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Permission {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
}

interface PaginatedPermissions {
    data: Permission[];
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
    permissoes: PaginatedPermissions;
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Permissões', href: permissoesIndex().url },
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

// Delete dialog
const showDeleteDialog = ref(false);
const permissionToDelete = ref<Permission | null>(null);

// Watch filters and update URL
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch(search, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        router.get(
            permissoesIndex().url,
            {
                search: search.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, 300);
});

const openDeleteDialog = (permission: Permission) => {
    permissionToDelete.value = permission;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    permissionToDelete.value = null;
    showDeleteDialog.value = false;
};
</script>

<template>
    <Head title="Permissões" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Key class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Permissões</h1>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Gerencie as permissões de acesso do sistema
                    </p>
                </div>
                <Link :href="permissoesCreate().url">
                    <Button class="w-full sm:w-auto">
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Permissão
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
                                placeholder="Buscar por nome da permissão..."
                                class="pl-10"
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
                            <TableHead>Nome</TableHead>
                            <TableHead>Guard</TableHead>
                            <TableHead>Criado em</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="permissoes.data.length === 0"
                            class="hover:bg-transparent"
                        >
                            <TableCell colspan="4" class="text-center">
                                <div class="py-8 text-muted-foreground">
                                    Nenhuma permissão encontrada
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="permission in permissoes.data" :key="permission.id">
                            <TableCell class="font-medium">
                                {{ permission.name }}
                            </TableCell>
                            <TableCell>{{ permission.guard_name }}</TableCell>
                            <TableCell>
                                {{ new Date(permission.created_at).toLocaleDateString('pt-BR') }}
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="PermissionController.edit(permission).url">
                                        <Button variant="ghost" size="sm" title="Editar">
                                            <Pencil class="h-4 w-4 text-orange-600 hover:text-orange-700" />
                                            <span class="sr-only">Editar</span>
                                        </Button>
                                    </Link>
                                    
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        title="Excluir"
                                        @click="openDeleteDialog(permission)"
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
                v-if="permissoes.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ permissoes.data.length }} de {{ permissoes.total }} permissões
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in permissoes.links"
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
            <DialogContent v-if="permissionToDelete" class="sm:max-w-md">
                <Form
                    v-bind="PermissionController.destroy.form(permissionToDelete)"
                    :options="{
                        preserveScroll: true,
                    }"
                    @success="() => {
                        toast.success('Permissão excluída com sucesso!');
                        closeDeleteDialog();
                    }"
                    @error="toast.error('Erro ao excluir permissão', 'Tente novamente mais tarde.')"
                    class="space-y-6"
                    v-slot="{ processing }"
                >
                    <DialogHeader class="space-y-2">
                        <DialogTitle class="text-xl font-semibold text-red-600 flex items-center gap-2">
                            <Trash2 class="w-6 h-6" />
                            Excluir Permissão
                        </DialogTitle>
                        <DialogDescription class="text-gray-600 pt-2">
                            Você está prestes a excluir a permissão
                            <strong class="font-medium text-gray-800">{{ permissionToDelete.name }}</strong>.
                            
                            <span class="text-sm text-red-500 font-medium">
                                Esta ação é **irreversível** e todos os dados associados serão perdidos.
                            </span>
                        </DialogDescription>
                    </DialogHeader>

                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <p class="font-semibold">Atenção:</p>
                        <p>Confirme se esta é a permissão correta antes de prosseguir.</p>
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


