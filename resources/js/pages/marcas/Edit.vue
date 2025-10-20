<script setup lang="ts">
import MarcaController from '@/actions/App/Http/Controllers/MarcaController';
import { index as marcasIndex } from '@/routes/marcas';
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import MarcaForm from '@/components/marcas/MarcaForm.vue';
import FormActions from '@/components/marcas/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Marca {
    id: number;
    nome: string;
    descricao: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    marca: Marca;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Marcas', href: marcasIndex().url },
    { title: `Editar ${props.marca.nome}`, href: '#' },
];
</script>

<template>
    <Head :title="`Editar ${marca.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (ID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ marca.id }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(marca.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="MarcaController.update.form(marca)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Marca atualizada com sucesso!')"
                @error="toast.error('Erro ao atualizar marca')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <MarcaForm 
                    :marca="marca"
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
