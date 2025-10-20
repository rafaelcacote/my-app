<script setup lang="ts">
import MarcaController from '@/actions/App/Http/Controllers/MarcaController';
import { index as marcasIndex } from '@/routes/marcas';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import MarcaForm from '@/components/marcas/MarcaForm.vue';
import FormActions from '@/components/marcas/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Marcas', href: marcasIndex().url },
    { title: 'Nova Marca', href: '#' },
];

const form = useForm({
    nome: '',
    descricao: '',
    ativo: true,
});

const handleSubmit = () => {
    form.post(MarcaController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Marca cadastrada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar marca', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Nova Marca" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <MarcaForm 
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
