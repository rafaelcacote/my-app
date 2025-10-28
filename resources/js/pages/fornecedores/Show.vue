<template>
    <Head :title="fornecedor.nome" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        {{ fornecedor.nome }}
                    </h1>
                    <p class="text-muted-foreground">
                        Detalhes do fornecedor
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="FornecedorController.edit(fornecedor).url">
                        <Button variant="outline">
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Link :href="FornecedorController.index().url">
                        <Button variant="outline">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Voltar
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="flex items-center gap-2">
                <Badge :variant="fornecedor.ativo ? 'success' : 'secondary'">
                    {{ fornecedor.ativo ? 'Ativo' : 'Inativo' }}
                </Badge>
            </div>

            <!-- Informações Básicas -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Informações Básicas</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Nome</Label>
                            <p class="text-sm">{{ fornecedor.nome }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">CPF/CNPJ</Label>
                            <p class="text-sm">{{ fornecedor.cpf_cnpj || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Email</Label>
                            <p class="text-sm">{{ fornecedor.email || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Telefone</Label>
                            <p class="text-sm">{{ fornecedor.telefone || '-' }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Endereço -->
            <Card v-if="fornecedor.endereco" class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Endereço</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">CEP</Label>
                            <p class="text-sm">{{ fornecedor.endereco.cep || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Logradouro</Label>
                            <p class="text-sm">{{ fornecedor.endereco.logradouro || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Número</Label>
                            <p class="text-sm">{{ fornecedor.endereco.numero || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Complemento</Label>
                            <p class="text-sm">{{ fornecedor.endereco.complemento || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Bairro</Label>
                            <p class="text-sm">{{ fornecedor.endereco.bairro || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Cidade</Label>
                            <p class="text-sm">{{ fornecedor.endereco.cidade || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Estado</Label>
                            <p class="text-sm">{{ fornecedor.endereco.estado || '-' }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Informações do Sistema -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Informações do Sistema</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">UUID</Label>
                            <p class="text-sm font-mono">{{ fornecedor.uuid }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Cadastrado em</Label>
                            <p class="text-sm">{{ formatDate(fornecedor.created_at) }}</p>
                        </div>
                        <div>
                            <Label class="text-sm font-medium text-muted-foreground">Última atualização</Label>
                            <p class="text-sm">{{ formatDate(fornecedor.updated_at) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Entradas de Mercadoria -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Entradas de Mercadoria</CardTitle>
                    <CardDescription>
                        Histórico de entradas de mercadoria deste fornecedor
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="fornecedor.entradas_mercadoria?.length === 0" class="text-center py-8 text-muted-foreground">
                        Nenhuma entrada de mercadoria encontrada
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="entrada in fornecedor.entradas_mercadoria" :key="entrada.id" class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium">{{ entrada.numero_nota || 'Sem número' }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ formatDate(entrada.data_entrada) }} - {{ entrada.loja?.nome }}
                                    </p>
                                </div>
                                <Badge variant="outline">
                                    {{ formatCurrency(entrada.valor_total) }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import FornecedorController from '@/actions/App/Http/Controllers/FornecedorController';
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, ArrowLeft } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';

interface Loja {
    id: number;
    nome: string;
}

interface EntradaMercadoria {
    id: number;
    numero_nota: string | null;
    data_entrada: string;
    valor_total: number;
    loja?: Loja;
}

interface Endereco {
    id: number;
    logradouro: string;
    numero: string;
    complemento?: string;
    bairro: string;
    cidade: string;
    estado: string;
    cep: string;
}

interface Fornecedor {
    id: number;
    uuid: string;
    nome: string;
    cpf_cnpj: string | null;
    email: string | null;
    telefone: string | null;
    endereco_id: number | null;
    endereco?: Endereco;
    ativo: boolean;
    entradas_mercadoria?: EntradaMercadoria[];
    created_at: string;
    updated_at: string;
}

interface Props {
    fornecedor: Fornecedor;
}

const props = defineProps<Props>();

const breadcrumbs = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Fornecedores', href: FornecedorController.index().url },
    { label: props.fornecedor.nome, href: null },
];


const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};
</script>
