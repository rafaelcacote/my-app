<template>
    <Head :title="`Editar ${fornecedor.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (UUID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">UUID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ fornecedor.uuid }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(fornecedor.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="FornecedorController.update.form(fornecedor)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Fornecedor atualizado com sucesso!')"
                @error="toast.error('Erro ao atualizar fornecedor')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <FornecedorForm 
                    :fornecedor="fornecedor"
                    :errors="errors"
                    :processing="processing"
                />
                
                <EnderecoForm 
                    :form="form"
                    :errors="errors"
                    :processing="processing"
                />
                
                <FormActions 
                    :processing="processing"
                    :recentlySuccessful="recentlySuccessful"
                />
            </Form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import FornecedorController from '@/actions/App/Http/Controllers/FornecedorController';
import { index as fornecedoresIndex } from '@/routes/fornecedores';
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import FornecedorForm from '@/components/fornecedores/FornecedorForm.vue';
import EnderecoForm from '@/components/endereco/EnderecoForm.vue';
import FormActions from '@/components/fornecedores/FormActions.vue';

interface Fornecedor {
    id: number;
    uuid: string;
    nome: string;
    cpf_cnpj: string | null;
    email: string | null;
    telefone: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    fornecedor: Fornecedor;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Fornecedores', href: fornecedoresIndex().url },
    { label: props.fornecedor.nome, href: null },
];
</script>
