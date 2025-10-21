<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import InputError from '@/components/InputError.vue';
import { Plus, Trash2 } from 'lucide-vue-next';

// Props
interface Cliente {
    id: number;
    nome: string;
    tipo: string;
}

interface Loja {
    id: number;
    nome: string;
}

interface Cor {
    id: number;
    nome: string;
}

interface Tamanho {
    id: number;
    nome: string;
}

interface Produto {
    id: number;
    nome: string;
}

interface ProdutoVariacao {
    id: number;
    produto: Produto;
    cor: Cor;
    tamanho: Tamanho;
}

interface VendaItem {
    id: number;
    produto_variacao_id: number;
    quantidade: number;
    preco_unitario: number;
    desconto: number;
    produto_variacao: ProdutoVariacao;
}

interface Props {
    venda?: any;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    clientes: Cliente[];
    lojas: Loja[];
    produtos: ProdutoVariacao[];
    isCreate?: boolean;
}

const props = defineProps<Props>();

// Estado local para itens
const novoItem = ref({
    produto_variacao_id: '',
    quantidade: 1,
    preco_unitario: 0,
    desconto: 0,
});

// Computed
const produtosDisponiveis = computed(() => {
    return props.produtos.filter(produto => 
        !props.form?.itens?.some((item: any) => item.produto_variacao_id == produto.id)
    );
});

const produtoSelecionado = computed(() => {
    if (!novoItem.value.produto_variacao_id) return null;
    return props.produtos.find(p => p.id == novoItem.value.produto_variacao_id);
});

const totalItens = computed(() => {
    if (!props.form?.itens) return 0;
    return props.form.itens.reduce((total: number, item: any) => {
        return total + (item.quantidade * item.preco_unitario - item.desconto);
    }, 0);
});

const totalVenda = computed(() => {
    const subtotal = totalItens.value;
    const desconto = props.form?.desconto || 0;
    return subtotal - desconto;
});

// Métodos
const adicionarItem = () => {
    if (!novoItem.value.produto_variacao_id || !novoItem.value.quantidade || !novoItem.value.preco_unitario) {
        return;
    }

    const produto = produtoSelecionado.value;
    if (!produto) return;

    const item = {
        produto_variacao_id: parseInt(novoItem.value.produto_variacao_id),
        quantidade: parseInt(novoItem.value.quantidade.toString()),
        preco_unitario: parseFloat(novoItem.value.preco_unitario.toString()),
        desconto: parseFloat(novoItem.value.desconto.toString()) || 0,
    };

    if (!props.form.itens) {
        props.form.itens = [];
    }

    props.form.itens.push(item);
    
    // Atualiza totais
    props.form.subtotal = totalItens.value;
    props.form.total = totalVenda.value;

    // Limpa o formulário
    novoItem.value = {
        produto_variacao_id: '',
        quantidade: 1,
        preco_unitario: 0,
        desconto: 0,
    };
};

const removerItem = (index: number) => {
    props.form.itens.splice(index, 1);
    props.form.subtotal = totalItens.value;
    props.form.total = totalVenda.value;
};

const calcularTotalItem = (item: any) => {
    return (item.quantidade * item.preco_unitario) - item.desconto;
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};

// Watchers
watch(() => props.form?.desconto, () => {
    props.form.total = totalVenda.value;
});

watch(() => novoItem.value.produto_variacao_id, (newValue) => {
    if (newValue && produtoSelecionado.value) {
        // Aqui você pode definir um preço padrão baseado no produto
        // Por enquanto, deixamos o usuário inserir manualmente
    }
});
</script>

<template>
    <div class="space-y-6">
        <!-- Dados Básicos -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Dados da Venda</CardTitle>
                <CardDescription>
                    {{ isCreate ? 'Informe os dados básicos da venda' : 'Atualize os dados da venda' }}
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Loja -->
                    <div class="space-y-2">
                        <Label for="loja_id">Loja *</Label>
                        <Select v-model="form.loja_id" :disabled="processing">
                            <SelectTrigger :class="{ 'border-red-500': errors.loja_id }">
                                <SelectValue placeholder="Selecione a loja" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="loja in lojas" :key="loja.id" :value="loja.id.toString()">
                                    {{ loja.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.loja_id" />
                    </div>

                    <!-- Cliente -->
                    <div class="space-y-2">
                        <Label for="cliente_id">Cliente</Label>
                        <Select v-model="form.cliente_id" :disabled="processing">
                            <SelectTrigger :class="{ 'border-red-500': errors.cliente_id }">
                                <SelectValue placeholder="Selecione o cliente (opcional)" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">Cliente não informado</SelectItem>
                                <SelectItem v-for="cliente in clientes" :key="cliente.id" :value="cliente.id.toString()">
                                    {{ cliente.nome }} ({{ cliente.tipo === 'fisica' ? 'PF' : 'PJ' }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.cliente_id" />
                    </div>

                    <!-- Número da Venda -->
                    <div class="space-y-2">
                        <Label for="numero_venda">Número da Venda</Label>
                        <Input
                            id="numero_venda"
                            v-model="form.numero_venda"
                            :class="{ 'border-red-500': errors.numero_venda }"
                            placeholder="Será gerado automaticamente se não informado"
                            :disabled="processing"
                        />
                        <InputError :message="errors.numero_venda" />
                    </div>

                    <!-- Data da Venda -->
                    <div class="space-y-2">
                        <Label for="data_venda">Data da Venda</Label>
                        <Input
                            id="data_venda"
                            v-model="form.data_venda"
                            type="date"
                            :class="{ 'border-red-500': errors.data_venda }"
                            :disabled="processing"
                        />
                        <InputError :message="errors.data_venda" />
                    </div>

                    <!-- Forma de Pagamento -->
                    <div class="space-y-2">
                        <Label for="forma_pagamento">Forma de Pagamento *</Label>
                        <Select v-model="form.forma_pagamento" :disabled="processing">
                            <SelectTrigger :class="{ 'border-red-500': errors.forma_pagamento }">
                                <SelectValue placeholder="Selecione a forma de pagamento" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="dinheiro">Dinheiro</SelectItem>
                                <SelectItem value="cartao_debito">Cartão de Débito</SelectItem>
                                <SelectItem value="cartao_credito">Cartão de Crédito</SelectItem>
                                <SelectItem value="pix">PIX</SelectItem>
                                <SelectItem value="boleto">Boleto</SelectItem>
                                <SelectItem value="transferencia">Transferência</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.forma_pagamento" />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status" :disabled="processing">
                            <SelectTrigger :class="{ 'border-red-500': errors.status }">
                                <SelectValue placeholder="Selecione o status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="pendente">Pendente</SelectItem>
                                <SelectItem value="concluida">Concluída</SelectItem>
                                <SelectItem value="cancelada">Cancelada</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.status" />
                    </div>
                </div>

                <!-- Observações -->
                <div class="space-y-2">
                    <Label for="observacoes">Observações</Label>
                    <Textarea
                        id="observacoes"
                        v-model="form.observacoes"
                        :class="{ 'border-red-500': errors.observacoes }"
                        placeholder="Observações sobre a venda..."
                        :disabled="processing"
                    />
                    <InputError :message="errors.observacoes" />
                </div>
            </CardContent>
        </Card>

        <!-- Itens da Venda -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Itens da Venda</CardTitle>
                <CardDescription>
                    Adicione os produtos que serão vendidos
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <!-- Formulário para adicionar item -->
                <div class="grid gap-4 md:grid-cols-5">
                    <div class="space-y-2">
                        <Label for="produto_variacao_id">Produto</Label>
                        <Select v-model="novoItem.produto_variacao_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione o produto" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="produto in produtosDisponiveis" :key="produto.id" :value="produto.id.toString()">
                                    {{ produto.produto.nome }} - {{ produto.cor.nome }} - {{ produto.tamanho.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <Label for="quantidade">Quantidade</Label>
                        <Input
                            id="quantidade"
                            v-model.number="novoItem.quantidade"
                            type="number"
                            min="1"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="preco_unitario">Preço Unitário</Label>
                        <Input
                            id="preco_unitario"
                            v-model.number="novoItem.preco_unitario"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="desconto">Desconto</Label>
                        <Input
                            id="desconto"
                            v-model.number="novoItem.desconto"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label>&nbsp;</Label>
                        <Button type="button" @click="adicionarItem" :disabled="!novoItem.produto_variacao_id">
                            <Plus class="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                <!-- Lista de itens -->
                <div v-if="form.itens && form.itens.length > 0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Produto</TableHead>
                                <TableHead>Cor</TableHead>
                                <TableHead>Tamanho</TableHead>
                                <TableHead>Quantidade</TableHead>
                                <TableHead>Preço Unitário</TableHead>
                                <TableHead>Desconto</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead class="w-[50px]"></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(item, index) in form.itens" :key="index">
                                <TableCell class="font-medium">
                                    {{ produtos.find(p => p.id == item.produto_variacao_id)?.produto.nome }}
                                </TableCell>
                                <TableCell>
                                    {{ produtos.find(p => p.id == item.produto_variacao_id)?.cor.nome }}
                                </TableCell>
                                <TableCell>
                                    {{ produtos.find(p => p.id == item.produto_variacao_id)?.tamanho.nome }}
                                </TableCell>
                                <TableCell>{{ item.quantidade }}</TableCell>
                                <TableCell>{{ formatCurrency(item.preco_unitario) }}</TableCell>
                                <TableCell>{{ formatCurrency(item.desconto) }}</TableCell>
                                <TableCell class="font-medium">{{ formatCurrency(calcularTotalItem(item)) }}</TableCell>
                                <TableCell>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="removerItem(index)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Resumo dos valores -->
                <div v-if="form.itens && form.itens.length > 0" class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="space-y-2">
                            <Label for="subtotal">Subtotal</Label>
                            <Input
                                id="subtotal"
                                v-model.number="form.subtotal"
                                type="number"
                                step="0.01"
                                readonly
                                class="bg-muted"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="desconto">Desconto Geral</Label>
                            <Input
                                id="desconto"
                                v-model.number="form.desconto"
                                type="number"
                                step="0.01"
                                min="0"
                                :class="{ 'border-red-500': errors.desconto }"
                                :disabled="processing"
                            />
                            <InputError :message="errors.desconto" />
                        </div>

                        <div class="space-y-2">
                            <Label for="total">Total</Label>
                            <Input
                                id="total"
                                v-model.number="form.total"
                                type="number"
                                step="0.01"
                                readonly
                                class="bg-muted font-semibold"
                            />
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8 text-muted-foreground">
                    <p>Nenhum item adicionado à venda</p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

