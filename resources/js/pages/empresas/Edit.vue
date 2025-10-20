<script setup lang="ts">
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { index as empresasIndex } from '@/routes/empresas';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import DeleteEmpresa from '@/components/DeleteEmpresa.vue';
import { useToast } from '@/composables/useToast';
import EmpresaForm from '@/components/empresas/EmpresaForm.vue';
import EnderecoForm from '@/components/endereco/EnderecoForm.vue';
import FormActions from '@/components/empresas/FormActions.vue';
import { Building2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Endereco {
    id?: number;
    endereco?: string;
    numero?: string | null;
    complemento?: string | null;
    bairro?: string | null;
    municipio_id?: number;
    cep?: string | null;
    referencia?: string | null;
}

interface Empresa {
    id: number;
    uuid: string;
    razao_social: string;
    nome_fantasia: string;
    cnpj: string | null;
    email: string;
    telefone: string | null;
    ativo: boolean;
    data_adesao: string;
    data_expiracao: string | null;
    endereco?: Endereco | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    empresa: Empresa;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Empresas', href: empresasIndex().url },
    { title: props.empresa.razao_social, href: EmpresaController.edit(props.empresa).url },
];

// Inicializa o form com os dados da empresa
const form = useForm({
    razao_social: props.empresa.razao_social,
    nome_fantasia: props.empresa.nome_fantasia,
    cnpj: props.empresa.cnpj || '',
    email: props.empresa.email,
    logo: null as File | null,
    telefone: props.empresa.telefone || '',
    ativo: props.empresa.ativo ? 1 : 0,
    // Garante que as datas estão no formato YYYY-MM-DD
    data_adesao: props.empresa.data_adesao?.split('T')[0] || props.empresa.data_adesao,
    data_expiracao: props.empresa.data_expiracao?.split('T')[0] || '',
    endereco: {
        endereco: props.empresa.endereco?.endereco || '',
        numero: props.empresa.endereco?.numero || '',
        complemento: props.empresa.endereco?.complemento || '',
        bairro: props.empresa.endereco?.bairro || '',
        municipio_id: props.empresa.endereco?.municipio_id || null,
        cep: props.empresa.endereco?.cep || '',
        referencia: props.empresa.endereco?.referencia || '',
    },
});

const handleSubmit = () => {
    // Log para debug
    console.log('Form data before submit:', {
        razao_social: form.razao_social,
        nome_fantasia: form.nome_fantasia,
        cnpj: form.cnpj,
        email: form.email,
        telefone: form.telefone,
        ativo: form.ativo,
        data_adesao: form.data_adesao,
        data_expiracao: form.data_expiracao,
        endereco: form.endereco,
        logo: form.logo,
    });
    
    const options = {
        preserveScroll: true,
        forceFormData: !!form.logo, // Força FormData quando há logo
        onSuccess: () => {
            toast.success('Empresa atualizada com sucesso!');
        },
        onError: (errors: Record<string, string>) => {
            console.log('Validation errors:', errors);
            toast.error('Erro ao atualizar empresa', 'Verifique os campos e tente novamente.');
        },
    };
    
    // Quando há logo, usa POST com _method=PUT no payload (method spoofing)
    // Quando não há logo, usa PUT normal
    if (form.logo) {
        // Transforma os dados para incluir _method
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(EmpresaController.update(props.empresa).url, options);
    } else {
        form.put(EmpresaController.update(props.empresa).url, options);
    }
};
</script>

<template>
    <Head :title="`Editar ${empresa.razao_social}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <Building2 class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Editar Empresa
                    </h1>
                </div>
                <p class="text-base text-muted-foreground">
                    Atualize as informações da empresa {{ empresa.nome_fantasia }}.
                </p>
            </div>

            <!-- Info Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">UUID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ empresa.uuid }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(empresa.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <EmpresaForm 
                    :form="form"
                    :empresa="empresa"
                    :errors="form.errors"
                    :processing="form.processing"
                />
                
                <EnderecoForm 
                    :form="form"
                    :endereco="empresa.endereco ?? undefined"
                    :errors="form.errors"
                    :processing="form.processing"
                />
                
                <FormActions 
                    :processing="form.processing"
                    :recentlySuccessful="form.recentlySuccessful"
                />
            </form>
        </div>
    </AppLayout>
</template>


