<script setup lang="ts">
import ProdutoController from '@/actions/App/Http/Controllers/ProdutoController';
import { index as produtosIndex } from '@/routes/produtos';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import ProdutoForm from '@/components/produtos/ProdutoForm.vue';
import FormActions from '@/components/produtos/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Props {
    categorias: Array<{ id: number; nome: string; }>;
    marcas: Array<{ id: number; nome: string; }>;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Produtos', href: produtosIndex().url },
    { title: 'Novo Produto', href: '#' },
];

const form = useForm({
    sku: '',
    nome: '',
    descricao: '',
    categoria_id: null as number | null,
    marca_id: null as number | null,
    preco_custo: '',
    preco_venda: '',
    codigo_barras: '',
    ativo: true,
    controla_estoque: true,
    estoque_minimo: '',
});

const handleSubmit = () => {
    form.post(ProdutoController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Produto cadastrado com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar produto', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Novo Produto" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <ProdutoForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :isCreate="true"
                    :categorias="categorias"
                    :marcas="marcas"
                />
                
                <FormActions 
                    :processing="form.processing"
                    :isCreate="true"
                />
            </form>
        </div>
    </AppLayout>
</template>
