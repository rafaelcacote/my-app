<template>
    <Head title="Novo Fornecedor" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <FornecedorForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :isCreate="true"
                />
                
                <EnderecoForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
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
import FornecedorController from '@/actions/App/Http/Controllers/FornecedorController';
import { index as fornecedoresIndex } from '@/routes/fornecedores';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import FornecedorForm from '@/components/fornecedores/FornecedorForm.vue';
import EnderecoForm from '@/components/endereco/EnderecoForm.vue';
import FormActions from '@/components/fornecedores/FormActions.vue';

const toast = useToast();


const form = useForm({
    nome: '',
    cpf_cnpj: '',
    email: '',
    telefone: '',
    ativo: true,
    endereco: {
        endereco: '',
        numero: '',
        complemento: '',
        bairro: '',
        municipio_id: null as number | null,
        cep: '',
        referencia: '',
    },
});

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Fornecedores', href: fornecedoresIndex().url },
    { label: 'Novo Fornecedor', href: null },
];

const handleSubmit = () => {
    form.post(FornecedorController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Fornecedor cadastrado com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar fornecedor', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>
