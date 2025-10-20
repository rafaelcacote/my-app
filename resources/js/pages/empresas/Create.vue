<script setup lang="ts">
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { index as empresasIndex } from '@/routes/empresas';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import EmpresaForm from '@/components/empresas/EmpresaForm.vue';
import EnderecoForm from '@/components/endereco/EnderecoForm.vue';
import FormActions from '@/components/empresas/FormActions.vue';
import { Building2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Empresas', href: empresasIndex().url },
    { title: 'Nova Empresa', href: EmpresaController.create().url },
];

const form = useForm({
    razao_social: '',
    nome_fantasia: '',
    cnpj: '',
    email: '',
    logo: null as File | null,
    telefone: '',
    ativo: 1,
    data_adesao: new Date().toISOString().split('T')[0],
    data_expiracao: '',
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

const handleSubmit = () => {
    console.log('Form data:', form);
    console.log('Logo:', form.logo);
    
    form.post(EmpresaController.store().url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Empresa cadastrada com sucesso!');
        },
        onError: (errors) => {
            console.log('Errors:', errors);
            toast.error('Erro ao cadastrar empresa', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>


<template>
    <Head title="Nova Empresa" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <Building2 class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Nova Empresa
                    </h1>
                </div>
                <p class="text-base text-muted-foreground">
                    Preencha as informações abaixo para cadastrar uma nova empresa no sistema.
                </p>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <EmpresaForm 
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




