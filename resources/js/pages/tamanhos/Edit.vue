<script setup lang="ts">
import TamanhoController from '@/actions/App/Http/Controllers/TamanhoController';
import { index as tamanhosIndex } from '@/routes/tamanhos';
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import TamanhoForm from '@/components/tamanhos/TamanhoForm.vue';
import FormActions from '@/components/tamanhos/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Tamanho {
    id: number;
    nome: string;
    tipo: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    tamanho: Tamanho;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tamanhos', href: tamanhosIndex().url },
    { title: `Editar ${props.tamanho.nome}`, href: '#' },
];
</script>

<template>
    <Head :title="`Editar ${tamanho.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (ID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ tamanho.id }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(tamanho.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="TamanhoController.update.form(tamanho)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Tamanho atualizado com sucesso!')"
                @error="toast.error('Erro ao atualizar tamanho')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <TamanhoForm 
                    :tamanho="tamanho"
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
