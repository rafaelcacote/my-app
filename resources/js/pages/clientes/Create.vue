<script setup lang="ts">
import ClienteController from '@/actions/App/Http/Controllers/ClienteController';
import { index as clientesIndex } from '@/routes/clientes';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import ClienteForm from '@/components/clientes/ClienteForm.vue';
import EnderecoForm from '@/components/endereco/EnderecoForm.vue';
import FormActions from '@/components/clientes/FormActions.vue';
import { computed } from 'vue';

const toast = useToast();

const form = useForm({
    nome: '',
    tipo: 'fisica',
    cpf_cnpj: '',
    email: '',
    telefone: '',
    data_nascimento: '',
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

const breadcrumbs = computed(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clientes', href: clientesIndex().url },
    { title: 'Novo Cliente', href: '' },
]);

const handleSubmit = () => {
    form.post(ClienteController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Cliente cadastrado com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar cliente', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Novo Cliente" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <ClienteForm 
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

