<script setup lang="ts">
import CorController from '@/actions/App/Http/Controllers/CorController';
import { index as coresIndex } from '@/routes/cores';
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import CorForm from '@/components/cores/CorForm.vue';
import FormActions from '@/components/cores/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Cor {
    id: number;
    nome: string;
    codigo_hex: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    cor: Cor;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Cores', href: coresIndex().url },
    { title: `Editar ${props.cor.nome}`, href: '#' },
];
</script>

<template>
    <Head :title="`Editar ${cor.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (ID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ cor.id }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(cor.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="CorController.update.form(cor)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Cor atualizada com sucesso!')"
                @error="toast.error('Erro ao atualizar cor')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <CorForm 
                    :cor="cor"
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
