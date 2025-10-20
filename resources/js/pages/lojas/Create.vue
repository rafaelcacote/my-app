<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import LojaForm from '@/components/lojas/LojaForm.vue';
import EnderecoForm from '@/components/endereco/EnderecoForm.vue';
import FormActions from '@/components/lojas/FormActions.vue';
import { Store } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Empresa {
    id: number;
    razao_social: string;
    nome_fantasia: string;
}

interface Props {
    empresa: Empresa;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Empresas', href: '/empresas' },
    { title: props.empresa.nome_fantasia, href: `/empresas/${props.empresa.id}` },
    { title: 'Lojas', href: `/empresas/${props.empresa.id}/lojas` },
    { title: 'Nova Loja', href: `/empresas/${props.empresa.id}/lojas/create` },
];

const form = useForm({
    nome: '',
    cnpj: '',
    email: '',
    telefone: '',
    ativo: 1,
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
    form.post(`/empresas/${props.empresa.id}/lojas`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Loja cadastrada com sucesso!');
        },
        onError: (errors) => {
            console.log('Errors:', errors);
            toast.error('Erro ao cadastrar loja', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head :title="`Nova Loja - ${empresa.nome_fantasia}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <Store class="h-8 w-8 text-primary" />
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">
                            Nova Loja
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            {{ empresa.nome_fantasia }}
                        </p>
                    </div>
                </div>
                <p class="text-base text-muted-foreground">
                    Preencha as informações abaixo para cadastrar uma nova loja.
                </p>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <LojaForm 
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
                    :empresaId="empresa.id"
                />
            </form>
        </div>
    </AppLayout>
</template>
