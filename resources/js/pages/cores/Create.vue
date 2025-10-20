<script setup lang="ts">
import CorController from '@/actions/App/Http/Controllers/CorController';
import { index as coresIndex } from '@/routes/cores';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import CorForm from '@/components/cores/CorForm.vue';
import FormActions from '@/components/cores/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Cores', href: coresIndex().url },
    { title: 'Nova Cor', href: '#' },
];

const form = useForm({
    nome: '',
    codigo_hex: '',
});

const handleSubmit = () => {
    form.post(CorController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Cor cadastrada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar cor', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Nova Cor" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <CorForm 
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
