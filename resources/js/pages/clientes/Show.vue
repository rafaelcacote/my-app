<script setup lang="ts">
import ClienteController from '@/actions/App/Http/Controllers/ClienteController';
import { index as clientesIndex } from '@/routes/clientes';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pencil, ArrowLeft } from 'lucide-vue-next';
import { computed } from 'vue';

interface Venda {
    id: number;
    numero_venda: string;
    status: string;
    total: number;
    data_venda: string;
    status_formatado: string;
    total_formatado: string;
}

interface Cliente {
    id: number;
    uuid: string;
    nome: string;
    tipo: string;
    cpf_cnpj: string | null;
    email: string | null;
    telefone: string | null;
    data_nascimento: string | null;
    cep: string | null;
    logradouro: string | null;
    numero: string | null;
    complemento: string | null;
    bairro: string | null;
    cidade: string | null;
    estado: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
    tipo_formatado: string;
    cpf_cnpj_formatado: string | null;
    endereco_completo: string;
    vendas: Venda[];
}

interface Props {
    cliente: Cliente;
}

const props = defineProps<Props>();

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Clientes', href: clientesIndex().url },
    { label: props.cliente.nome, href: null },
]);

const formatDate = (date: string | null) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString('pt-BR');
};

const getStatusBadgeVariant = (ativo: boolean) => {
    return ativo ? 'default' : 'secondary';
};

const getTipoBadgeVariant = (tipo: string) => {
    return tipo === 'fisica' ? 'outline' : 'default';
};

const getVendaStatusBadgeVariant = (status: string) => {
    switch (status) {
        case 'concluida':
            return 'default';
        case 'pendente':
            return 'secondary';
        case 'cancelada':
            return 'destructive';
        default:
            return 'outline';
    }
};
</script>

<template>
    <Head :title="cliente.nome" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="clientesIndex().url">
                        <Button variant="outline" size="sm">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Voltar
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">{{ cliente.nome }}</h1>
                        <p class="text-muted-foreground">
                            Detalhes do cliente
                        </p>
                    </div>
                </div>
                <Link :href="ClienteController.edit(cliente).url">
                    <Button>
                        <Pencil class="mr-2 h-4 w-4" />
                        Editar
                    </Button>
                </Link>
            </div>

            <!-- Cliente Info Card -->
            <Card class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Informações do Cliente</CardTitle>
                    <CardDescription>
                        Dados cadastrais e informações de contato
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Dados Básicos -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Dados Básicos</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Nome:</span>
                                    <span class="text-foreground">{{ cliente.nome }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Tipo:</span>
                                    <Badge :variant="getTipoBadgeVariant(cliente.tipo)">
                                        {{ cliente.tipo_formatado }}
                                    </Badge>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">CPF/CNPJ:</span>
                                    <span class="text-foreground">{{ cliente.cpf_cnpj_formatado || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Status:</span>
                                    <Badge :variant="getStatusBadgeVariant(cliente.ativo)">
                                        {{ cliente.ativo ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </div>
                                <div v-if="cliente.data_nascimento" class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Data de Nascimento:</span>
                                    <span class="text-foreground">{{ formatDate(cliente.data_nascimento) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Contato -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Contato</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Email:</span>
                                    <span class="text-foreground">{{ cliente.email || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-muted-foreground">Telefone:</span>
                                    <span class="text-foreground">{{ cliente.telefone || '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div v-if="cliente.endereco_completo" class="space-y-4">
                        <h3 class="text-lg font-semibold">Endereço</h3>
                        <div class="text-sm">
                            <span class="font-medium text-muted-foreground">Endereço completo:</span>
                            <span class="ml-2 text-foreground">{{ cliente.endereco_completo }}</span>
                        </div>
                    </div>

                    <!-- Informações do Sistema -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Informações do Sistema</h3>
                        <div class="grid gap-2 text-sm md:grid-cols-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-muted-foreground">UUID:</span>
                                <span class="font-mono text-foreground">{{ cliente.uuid }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                                <span class="text-foreground">{{ formatDate(cliente.created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-muted-foreground">Última atualização:</span>
                                <span class="text-foreground">{{ formatDate(cliente.updated_at) }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Vendas do Cliente -->
            <Card v-if="cliente.vendas && cliente.vendas.length > 0" class="border-border shadow-sm">
                <CardHeader>
                    <CardTitle>Vendas do Cliente</CardTitle>
                    <CardDescription>
                        Histórico de vendas realizadas para este cliente
                    </CardDescription>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Número da Venda</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead>Data da Venda</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="venda in cliente.vendas" :key="venda.id">
                                <TableCell class="font-medium">{{ venda.numero_venda }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getVendaStatusBadgeVariant(venda.status)">
                                        {{ venda.status_formatado }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ venda.total_formatado }}</TableCell>
                                <TableCell>{{ formatDate(venda.data_venda) }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <!-- Mensagem quando não há vendas -->
            <Card v-else class="border-border shadow-sm">
                <CardContent class="py-8 text-center">
                    <p class="text-muted-foreground">Este cliente ainda não possui vendas cadastradas.</p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

