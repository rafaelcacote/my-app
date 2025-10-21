<script setup lang="ts">
import VendaController from '@/actions/App/Http/Controllers/VendaController';
import { index as vendasIndex } from '@/routes/vendas';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import VendaForm from '@/components/vendas/VendaForm.vue';
import FormActions from '@/components/vendas/FormActions.vue';
import { computed, ref } from 'vue';

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

interface Props {
    clientes: Cliente[];
    lojas: Loja[];
    produtos: ProdutoVariacao[];
}

const props = defineProps<Props>();
const toast = useToast();

const form = useForm({
    loja_id: '',
    cliente_id: '',
    numero_venda: '',
    status: 'pendente',
    subtotal: 0,
    desconto: 0,
    total: 0,
    forma_pagamento: '',
    observacoes: '',
    data_venda: new Date().toISOString().split('T')[0],
    itens: [] as Array<{
        produto_variacao_id: number;
        quantidade: number;
        preco_unitario: number;
        desconto: number;
    }>,
    pagamento: false,
});

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Vendas', href: vendasIndex().url },
    { label: 'Nova Venda', href: null },
]);

const handleSubmit = () => {
    form.post(VendaController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Venda cadastrada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar venda', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Nova Venda" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <VendaForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :clientes="clientes"
                    :lojas="lojas"
                    :produtos="produtos"
                    :isCreate="true"
                />
                
                <FormActions 
                    :processing="form.processing"
                    :isCreate="true"
                />
            </form>
        </div>
    </AppLayout>
</template>

