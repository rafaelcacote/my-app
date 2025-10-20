<script setup lang="ts">
import ProdutoVariacaoController from '@/actions/App/Http/Controllers/ProdutoVariacaoController';
import { index as produtoVariacoesIndex } from '@/routes/produto-variacoes';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import ProdutoVariacaoForm from '@/components/produto-variacoes/ProdutoVariacaoForm.vue';
import FormActions from '@/components/produto-variacoes/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Props {
    produtos: Array<{ id: number; nome: string; }>;
    cores: Array<{ id: number; nome: string; }>;
    tamanhos: Array<{ id: number; nome: string; }>;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Variações de Produto', href: produtoVariacoesIndex().url },
    { title: 'Nova Variação', href: '#' },
];

const form = useForm({
    produto_id: null as number | null,
    cor_id: null as number | null,
    tamanho_id: null as number | null,
    preco_custo: '',
    preco_venda: '',
    codigo_barras: '',
    ativo: true,
});

const handleSubmit = () => {
    form.post(ProdutoVariacaoController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Variação cadastrada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar variação', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Nova Variação" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <ProdutoVariacaoForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :isCreate="true"
                    :produtos="produtos"
                    :cores="cores"
                    :tamanhos="tamanhos"
                />
                
                <FormActions 
                    :processing="form.processing"
                    :isCreate="true"
                />
            </form>
        </div>
    </AppLayout>
</template>
