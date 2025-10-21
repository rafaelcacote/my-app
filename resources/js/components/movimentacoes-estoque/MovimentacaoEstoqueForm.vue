<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { useForm } from '@inertiajs/vue3';

interface ProdutoVariacao {
    id: number;
    produto: {
        nome: string;
    };
    cor?: {
        nome: string;
    };
    tamanho?: {
        nome: string;
    };
}

interface Loja {
    id: number;
    nome: string;
}

interface MovimentacaoEstoque {
    id: number;
    produto_variacao_id: number | null;
    loja_id: number | null;
    tipo: string;
    quantidade: number;
    observacoes: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    form: any;
    movimentacaoEstoque?: MovimentacaoEstoque;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
    produtoVariacoes: ProdutoVariacao[];
    lojas: Loja[];
}

const props = defineProps<Props>();

const tiposMovimentacao = [
    { value: 'entrada', label: 'Entrada' },
    { value: 'saida', label: 'Saída' },
    { value: 'ajuste', label: 'Ajuste' },
    { value: 'transferencia', label: 'Transferência' },
];
</script>

<template>
    <div class="space-y-6">
        <!-- Seleção de Produto e Loja -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Produto e Loja</CardTitle>
                <CardDescription>
                    Selecione o produto e a loja para a movimentação
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Produto Variação -->
                    <div class="space-y-2">
                        <Label for="produto_variacao_id">Produto *</Label>
                        <Select
                            v-model="form.produto_variacao_id"
                            :disabled="processing"
                        >
                            <SelectTrigger :class="{ 'border-red-500': errors.produto_variacao_id }">
                                <SelectValue placeholder="Selecione um produto" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem 
                                    v-for="variacao in produtoVariacoes" 
                                    :key="variacao.id" 
                                    :value="variacao.id.toString()"
                                >
                                    {{ variacao.produto.nome }}
                                    <template v-if="variacao.cor || variacao.tamanho">
                                        ({{ [variacao.cor?.nome, variacao.tamanho?.nome].filter(Boolean).join(', ') }})
                                    </template>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.produto_variacao_id" />
                    </div>

                    <!-- Loja -->
                    <div class="space-y-2">
                        <Label for="loja_id">Loja *</Label>
                        <Select
                            v-model="form.loja_id"
                            :disabled="processing"
                        >
                            <SelectTrigger :class="{ 'border-red-500': errors.loja_id }">
                                <SelectValue placeholder="Selecione uma loja" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem 
                                    v-for="loja in lojas" 
                                    :key="loja.id" 
                                    :value="loja.id.toString()"
                                >
                                    {{ loja.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.loja_id" />
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Tipo e Quantidade -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Movimentação</CardTitle>
                <CardDescription>
                    Tipo de movimentação e quantidade
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Tipo -->
                    <div class="space-y-2">
                        <Label for="tipo">Tipo de Movimentação *</Label>
                        <Select
                            v-model="form.tipo"
                            :disabled="processing"
                        >
                            <SelectTrigger :class="{ 'border-red-500': errors.tipo }">
                                <SelectValue placeholder="Selecione o tipo" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem 
                                    v-for="tipo in tiposMovimentacao" 
                                    :key="tipo.value" 
                                    :value="tipo.value"
                                >
                                    {{ tipo.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.tipo" />
                    </div>

                    <!-- Quantidade -->
                    <div class="space-y-2">
                        <Label for="quantidade">Quantidade *</Label>
                        <Input
                            id="quantidade"
                            v-model="form.quantidade"
                            type="number"
                            placeholder="0"
                            :class="{ 'border-red-500': errors.quantidade }"
                            :disabled="processing"
                        />
                        <InputError :message="errors.quantidade" />
                        <p class="text-xs text-muted-foreground">
                            Use números positivos para entradas e negativos para saídas
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Observações -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Observações</CardTitle>
                <CardDescription>
                    Informações adicionais sobre a movimentação
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="space-y-2">
                    <Label for="observacoes">Observações</Label>
                    <Textarea
                        id="observacoes"
                        v-model="form.observacoes"
                        placeholder="Digite observações sobre a movimentação (opcional)"
                        :class="{ 'border-red-500': errors.observacoes }"
                        :disabled="processing"
                        rows="4"
                    />
                    <InputError :message="errors.observacoes" />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
