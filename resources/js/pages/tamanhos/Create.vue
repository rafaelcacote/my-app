<script setup lang="ts">
import TamanhoController from '@/actions/App/Http/Controllers/TamanhoController';
import { index as tamanhosIndex } from '@/routes/tamanhos';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import TamanhoForm from '@/components/tamanhos/TamanhoForm.vue';
import FormActions from '@/components/tamanhos/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tamanhos', href: tamanhosIndex().url },
    { title: 'Novo Tamanho', href: '#' },
];

const form = useForm({
    nome: '',
    tipo: '',
});

const handleSubmit = () => {
    form.post(TamanhoController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Tamanho cadastrado com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar tamanho', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Novo Tamanho" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <TamanhoForm 
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
