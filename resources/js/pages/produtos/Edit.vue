<script setup lang="ts">
import ProdutoController from '@/actions/App/Http/Controllers/ProdutoController';
import { index as produtosIndex } from '@/routes/produtos';
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import ProdutoForm from '@/components/produtos/ProdutoForm.vue';
import FormActions from '@/components/produtos/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Categoria {
    id: number;
    nome: string;
}

interface Marca {
    id: number;
    nome: string;
}

interface Produto {
    id: number;
    nome: string;
    descricao: string | null;
    categoria_id: number | null;
    marca_id: number | null;
    preco_custo: number | null;
    preco_venda: number | null;
    codigo_barras: string | null;
    ativo: boolean;
    controla_estoque: boolean;
    estoque_minimo: number | null;
    categoria?: Categoria;
    marca?: Marca;
    created_at: string;
    updated_at: string;
}

interface Props {
    produto: Produto;
    categorias: Categoria[];
    marcas: Marca[];
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Produtos', href: produtosIndex().url },
    { title: `Editar ${props.produto.nome}`, href: '#' },
];
</script>

<template>
    <Head :title="`Editar ${produto.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (ID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ produto.id }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(produto.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="ProdutoController.update.form(produto)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Produto atualizado com sucesso!')"
                @error="toast.error('Erro ao atualizar produto')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <ProdutoForm 
                    :produto="produto"
                    :errors="errors"
                    :processing="processing"
                    :categorias="categorias"
                    :marcas="marcas"
                />
                
                <FormActions 
                    :processing="processing"
                    :recentlySuccessful="recentlySuccessful"
                />
            </Form>
        </div>
    </AppLayout>
</template>
