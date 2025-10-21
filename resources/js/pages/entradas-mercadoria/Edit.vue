<template>
    <Head :title="`Editar Entrada - ${entrada.numero_nota || 'Sem número'}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">Data de Entrada:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(entrada.data_entrada).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(entrada.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="EntradaMercadoriaController.update.form(entrada)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Entrada de mercadoria atualizada com sucesso!')"
                @error="toast.error('Erro ao atualizar entrada de mercadoria')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <EntradaMercadoriaForm 
                    :entrada="entrada"
                    :errors="errors"
                    :processing="processing"
                    :lojas="lojas"
                    :fornecedores="fornecedores"
                    :produtoVariacoes="produtoVariacoes"
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
import EntradaMercadoriaController from '@/actions/App/Http/Controllers/EntradaMercadoriaController';
import { index as entradasIndex } from '@/routes/entradas-mercadoria';
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import EntradaMercadoriaForm from '@/components/entradas-mercadoria/EntradaMercadoriaForm.vue';
import FormActions from '@/components/entradas-mercadoria/FormActions.vue';

interface Loja {
    id: number;
    nome: string;
}

interface Fornecedor {
    id: number;
    nome: string;
}

interface ProdutoVariacao {
    id: number;
    produto?: {
        id: number;
        nome: string;
    };
    cor?: {
        id: number;
        nome: string;
    };
    tamanho?: {
        id: number;
        nome: string;
    };
}

interface EntradaMercadoria {
    id: number;
    numero_nota: string | null;
    data_entrada: string;
    valor_total: number;
    observacoes: string | null;
    loja_id: number;
    fornecedor_id: number | null;
    itens?: Array<{
        id: number;
        produto_variacao_id: number;
        quantidade: number;
        preco_unitario: number;
        total: number;
        produto_variacao?: ProdutoVariacao;
    }>;
    created_at: string;
    updated_at: string;
}

interface Props {
    entrada: EntradaMercadoria;
    lojas: Loja[];
    fornecedores: Fornecedor[];
    produtoVariacoes: ProdutoVariacao[];
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Entradas de Mercadoria', href: entradasIndex().url },
    { label: props.entrada.numero_nota || 'Entrada', href: null },
];
</script>
