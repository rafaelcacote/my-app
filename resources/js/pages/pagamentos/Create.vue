<script setup lang="ts">
import PagamentoController from '@/actions/App/Http/Controllers/PagamentoController';
import { index as pagamentosIndex } from '@/routes/pagamentos';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
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

interface Props {
    venda?: Venda;
}

const props = defineProps<Props>();
const toast = useToast();

const form = useForm({
    venda_id: props.venda?.id || '',
    forma_pagamento: '',
    valor: props.venda ? 0 : 0,
    status: 'pendente',
    data_pagamento: '',
    observacoes: '',
});

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Pagamentos', href: pagamentosIndex().url },
    { label: 'Novo Pagamento', href: null },
]);

const handleSubmit = () => {
    form.post(PagamentoController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Pagamento cadastrado com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar pagamento', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Novo Pagamento" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <PagamentoForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :venda="venda"
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

