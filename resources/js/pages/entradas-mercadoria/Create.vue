<template>
    <Head title="Nova Entrada de Mercadoria" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <EntradaMercadoriaForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :lojas="lojas"
                    :fornecedores="fornecedores"
                    :produtoVariacoes="produtoVariacoes"
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

<script setup lang="ts">
import EntradaMercadoriaController from '@/actions/App/Http/Controllers/EntradaMercadoriaController';
import { index as entradasIndex } from '@/routes/entradas-mercadoria';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import EntradaMercadoriaForm from '@/components/entradas-mercadoria/EntradaMercadoriaForm.vue';
import FormActions from '@/components/entradas-mercadoria/FormActions.vue';

interface Loja {
    id: number;
    nome: string;
}

interface Fornecedor {
    id: number;
    nome: string;
}

interface ProdutoVariacao {
    id: number;
    produto?: {
        id: number;
        nome: string;
    };
    cor?: {
        id: number;
        nome: string;
    };
    tamanho?: {
        id: number;
        nome: string;
    };
}

interface Props {
    lojas: Loja[];
    fornecedores: Fornecedor[];
    produtoVariacoes: ProdutoVariacao[];
}

const props = defineProps<Props>();
const toast = useToast();

const form = useForm({
    loja_id: '',
    fornecedor_id: '',
    numero_nota: '',
    data_entrada: new Date().toISOString().split('T')[0],
    valor_total: 0,
    observacoes: '',
    itens: [] as Array<{
        produto_variacao_id: number;
        quantidade: number;
        preco_unitario: number;
        total: number;
    }>,
});

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Entradas de Mercadoria', href: entradasIndex().url },
    { label: 'Nova Entrada', href: null },
];

const handleSubmit = () => {
    form.post(EntradaMercadoriaController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Entrada de mercadoria cadastrada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar entrada de mercadoria', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>
