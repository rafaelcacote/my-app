<script setup lang="ts">
import CategoriaController from '@/actions/App/Http/Controllers/CategoriaController';
import { index as categoriasIndex } from '@/routes/categorias';
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import CategoriaForm from '@/components/categorias/CategoriaForm.vue';
import FormActions from '@/components/categorias/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Categoria {
    id: number;
    nome: string;
    descricao: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    categoria: Categoria;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Categorias', href: categoriasIndex().url },
    { title: `Editar ${props.categoria.nome}`, href: '#' },
];
</script>

<template>
    <Head :title="`Editar ${categoria.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (ID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ categoria.id }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(categoria.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="CategoriaController.update.form(categoria)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Categoria atualizada com sucesso!')"
                @error="toast.error('Erro ao atualizar categoria')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <CategoriaForm 
                    :categoria="categoria"
                    :errors="errors"
                    :processing="processing"
                />
                
                <FormActions 
                    :processing="processing"
                    :recentlySuccessful="recentlySuccessful"
                />
            </Form>
        </div>
    </AppLayout>
</template>
