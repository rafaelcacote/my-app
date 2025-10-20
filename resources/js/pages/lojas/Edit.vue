<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import LojaForm from '@/components/lojas/LojaForm.vue';
import EnderecoForm from '@/components/endereco/EnderecoForm.vue';
import FormActions from '@/components/lojas/FormActions.vue';
import { Store } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Endereco {
    id: number;
    endereco: string | null;
    numero: string | null;
    complemento: string | null;
    bairro: string | null;
    municipio_id: number | null;
    cep: string | null;
    referencia: string | null;
}

interface Loja {
    id: number;
    empresa_id: number;
    nome: string;
    cnpj: string | null;
    telefone: string | null;
    email: string | null;
    ativo: boolean;
    endereco_id: number | null;
    endereco: Endereco | null;
}

interface Empresa {
    id: number;
    razao_social: string;
    nome_fantasia: string;
}

interface Props {
    empresa: Empresa;
    loja: Loja;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Empresas', href: '/empresas' },
    { title: props.empresa.nome_fantasia, href: `/empresas/${props.empresa.id}` },
    { title: 'Lojas', href: `/empresas/${props.empresa.id}/lojas` },
    { title: 'Editar Loja', href: `/empresas/${props.empresa.id}/lojas/${props.loja.id}/edit` },
];

const form = useForm({
    nome: props.loja.nome || '',
    cnpj: props.loja.cnpj || '',
    email: props.loja.email || '',
    telefone: props.loja.telefone || '',
    ativo: props.loja.ativo ? 1 : 0,
    endereco: {
        endereco: props.loja.endereco?.endereco || '',
        numero: props.loja.endereco?.numero || '',
        complemento: props.loja.endereco?.complemento || '',
        bairro: props.loja.endereco?.bairro || '',
        municipio_id: props.loja.endereco?.municipio_id || null,
        cep: props.loja.endereco?.cep || '',
        referencia: props.loja.endereco?.referencia || '',
    },
});

const handleSubmit = () => {
    form.put(`/empresas/${props.empresa.id}/lojas/${props.loja.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Loja atualizada com sucesso!');
        },
        onError: (errors) => {
            console.log('Errors:', errors);
            toast.error('Erro ao atualizar loja', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head :title="`Editar ${loja.nome} - ${empresa.nome_fantasia}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <Store class="h-8 w-8 text-primary" />
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">
                            Editar Loja
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            {{ empresa.nome_fantasia }}
                        </p>
                    </div>
                </div>
                <p class="text-base text-muted-foreground">
                    Atualize as informações da loja {{ loja.nome }}.
                </p>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <LojaForm 
                    :loja="loja"
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :isCreate="false"
                />
                
                <EnderecoForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :isCreate="false"
                />
                
                <FormActions 
                    :processing="form.processing"
                    :isCreate="false"
                    :empresaId="empresa.id"
                />
            </form>
        </div>
    </AppLayout>
</template>

