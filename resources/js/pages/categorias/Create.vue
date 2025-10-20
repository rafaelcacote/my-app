<script setup lang="ts">
import CategoriaController from '@/actions/App/Http/Controllers/CategoriaController';
import { index as categoriasIndex } from '@/routes/categorias';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import CategoriaForm from '@/components/categorias/CategoriaForm.vue';
import FormActions from '@/components/categorias/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Categorias', href: categoriasIndex().url },
    { title: 'Nova Categoria', href: '#' },
];

const form = useForm({
    nome: '',
    descricao: '',
    ativo: true,
});

const handleSubmit = () => {
    form.post(CategoriaController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Categoria cadastrada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar categoria', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Nova Categoria" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <CategoriaForm 
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
