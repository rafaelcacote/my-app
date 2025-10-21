<script setup lang="ts">
import VendaController from '@/actions/App/Http/Controllers/VendaController';
import { index as vendasIndex } from '@/routes/vendas';
import { Form, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import VendaForm from '@/components/vendas/VendaForm.vue';
import FormActions from '@/components/vendas/FormActions.vue';
import { computed } from 'vue';

interface Cliente {
    id: number;
    nome: string;
    tipo: string;
}

interface Loja {
    id: number;
    nome: string;
}

interface Cor {
    id: number;
    nome: string;
}

interface Tamanho {
    id: number;
    nome: string;
}

interface Produto {
    id: number;
    nome: string;
}

interface ProdutoVariacao {
    id: number;
    produto: Produto;
    cor: Cor;
    tamanho: Tamanho;
}

interface VendaItem {
    id: number;
    produto_variacao_id: number;
    quantidade: number;
    preco_unitario: number;
    desconto: number;
    produto_variacao: ProdutoVariacao;
}

interface Venda {
    id: number;
    uuid: string;
    numero_venda: string;
    status: string;
    subtotal: number;
    desconto: number;
    total: number;
    forma_pagamento: string;
    observacoes: string | null;
    data_venda: string;
    loja_id: number;
    cliente_id: number | null;
    itens: VendaItem[];
}

interface Props {
    venda: Venda;
    clientes: Cliente[];
    lojas: Loja[];
    produtos: ProdutoVariacao[];
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Vendas', href: vendasIndex().url },
    { label: `Editar ${props.venda.numero_venda}`, href: null },
]);
</script>

<template>
    <Head :title="`Editar ${venda.numero_venda}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (UUID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">UUID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ venda.uuid }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(venda.data_venda).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="VendaController.update.form(venda)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Venda atualizada com sucesso!')"
                @error="toast.error('Erro ao atualizar venda')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <VendaForm 
                    :venda="venda"
                    :errors="errors"
                    :processing="processing"
                    :clientes="clientes"
                    :lojas="lojas"
                    :produtos="produtos"
                />
                
                <FormActions 
                    :processing="processing"
                    :recentlySuccessful="recentlySuccessful"
                />
            </Form>
        </div>
    </AppLayout>
</template>

