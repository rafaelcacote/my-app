<script setup lang="ts">
import MovimentacaoEstoqueController from '@/actions/App/Http/Controllers/MovimentacaoEstoqueController';
import { index as movimentacoesEstoqueIndex } from '@/routes/movimentacoes-estoque';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import MovimentacaoEstoqueForm from '@/components/movimentacoes-estoque/MovimentacaoEstoqueForm.vue';
import FormActions from '@/components/movimentacoes-estoque/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Props {
    produtoVariacoes: Array<{ id: number; produto: { nome: string }; cor?: { nome: string }; tamanho?: { nome: string }; }>;
    lojas: Array<{ id: number; nome: string; }>;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Movimentações de Estoque', href: movimentacoesEstoqueIndex().url },
    { title: 'Nova Movimentação', href: '#' },
];

const form = useForm({
    produto_variacao_id: null as number | null,
    loja_id: null as number | null,
    tipo: '',
    quantidade: '',
    observacoes: '',
});

const handleSubmit = () => {
    form.post(MovimentacaoEstoqueController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Movimentação registrada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao registrar movimentação', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Nova Movimentação" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <MovimentacaoEstoqueForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :isCreate="true"
                    :produtoVariacoes="produtoVariacoes"
                    :lojas="lojas"
                />
                
                <FormActions 
                    :processing="form.processing"
                    :isCreate="true"
                />
            </form>
        </div>
    </AppLayout>
</template>
