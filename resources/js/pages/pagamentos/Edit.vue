<script setup lang="ts">
import PagamentoController from '@/actions/App/Http/Controllers/PagamentoController';
import { index as pagamentosIndex } from '@/routes/pagamentos';
import { Form, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import PagamentoForm from '@/components/pagamentos/PagamentoForm.vue';
import FormActions from '@/components/pagamentos/FormActions.vue';
import { computed } from 'vue';

interface Cliente {
    id: number;
    nome: string;
}

interface Loja {
    id: number;
    nome: string;
}

interface Venda {
    id: number;
    numero_venda: string;
    cliente: Cliente | null;
    loja: Loja;
}

interface Pagamento {
    id: number;
    venda_id: number;
    forma_pagamento: string;
    valor: number;
    status: string;
    data_pagamento: string | null;
    observacoes: string | null;
    venda: Venda;
}

interface Props {
    pagamento: Pagamento;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Pagamentos', href: pagamentosIndex().url },
    { label: `Editar Pagamento - ${props.pagamento.venda.numero_venda}`, href: null },
]);
</script>

<template>
    <Head :title="`Editar Pagamento - ${pagamento.venda.numero_venda}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (UUID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">Venda:</span>
                            <span class="ml-2 font-mono text-foreground">{{ pagamento.venda.numero_venda }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cliente:</span>
                            <span class="ml-2 text-foreground">
                                {{ pagamento.venda.cliente?.nome || 'Cliente não informado' }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="PagamentoController.update.form(pagamento)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Pagamento atualizado com sucesso!')"
                @error="toast.error('Erro ao atualizar pagamento')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <PagamentoForm 
                    :pagamento="pagamento"
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

