<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { useForm } from '@inertiajs/vue3';

interface Produto {
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

interface ProdutoVariacao {
    id: number;
    produto_id: number | null;
    cor_id: number | null;
    tamanho_id: number | null;
    preco_custo: number | null;
    preco_venda: number | null;
    codigo_barras: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    form: any;
    produtoVariacao?: ProdutoVariacao;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
    produtos: Produto[];
    cores: Cor[];
    tamanhos: Tamanho[];
}

const props = defineProps<Props>();

// Refs para campos de preço com máscara
const precoCustoInput = ref('');
const precoVendaInput = ref('');
const ativo = ref(props.isCreate ? true : !!props.produtoVariacao?.ativo);

// Inicializar valores ao montar o componente
watch(() => props.produtoVariacao, (produtoVariacao) => {
    if (produtoVariacao) {
        precoCustoInput.value = formatCurrencyInput(produtoVariacao.preco_custo);
        precoVendaInput.value = formatCurrencyInput(produtoVariacao.preco_venda);
        ativo.value = !!produtoVariacao.ativo;
    }
}, { immediate: true });

// Função para converter número em string formatada (ex: 10.50 -> "R$ 10,50")
const formatCurrencyInput = (value: number | null | undefined): string => {
    if (value === null || value === undefined || value === 0) return '';
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
};

// Função para aplicar máscara de moeda enquanto digita
const formatCurrency = (value: string): string => {
    // Remove tudo que não é dígito
    const digits = value.replace(/\D/g, '');
    
    // Se não houver dígitos, retorna vazio
    if (digits.length === 0) return '';
    
    // Converte para número (dividindo por 100 para ter centavos)
    const amount = parseInt(digits) / 100;
    
    // Formata como moeda
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(amount);
};

// Função para converter o valor formatado de volta para número
const parseCurrency = (value: string): number => {
    // Remove símbolos de moeda e espaços
    const clean = value.replace(/[R$\s.]/g, '').replace(',', '.');
    return parseFloat(clean) || 0;
};

// Handlers para os inputs de preço
const handlePrecoCustoInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    precoCustoInput.value = formatCurrency(input.value);
    // Atualiza o form com o valor numérico
    if (props.form) {
        props.form.preco_custo = parseCurrency(precoCustoInput.value);
    }
};

const handlePrecoVendaInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    precoVendaInput.value = formatCurrency(input.value);
    // Atualiza o form com o valor numérico
    if (props.form) {
        props.form.preco_venda = parseCurrency(precoVendaInput.value);
    }
};

// Watchers para sincronizar o valor ativo com o form
watch(ativo, (value) => {
    if (props.form) {
        props.form.ativo = value;
    }
});
</script>

<template>
    <div class="space-y-6">
        <!-- Seleção de Produto -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Produto Base</CardTitle>
                <CardDescription>
                    Selecione o produto para criar a variação
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="space-y-2">
                    <Label for="produto_id">Produto *</Label>
                    <Select
                        v-model="form.produto_id"
                        :disabled="processing"
                    >
                        <SelectTrigger :class="{ 'border-red-500': errors.produto_id }">
                            <SelectValue placeholder="Selecione um produto" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem 
                                v-for="produto in produtos" 
                                :key="produto.id" 
                                :value="produto.id.toString()"
                            >
                                {{ produto.nome }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.produto_id" />
                </div>
            </CardContent>
        </Card>

        <!-- Variações -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Variações</CardTitle>
                <CardDescription>
                    Cor e tamanho da variação
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Cor -->
                    <div class="space-y-2">
                        <Label for="cor_id">Cor</Label>
                        <Select
                            v-model="form.cor_id"
                            :disabled="processing"
                        >
                            <SelectTrigger :class="{ 'border-red-500': errors.cor_id }">
                                <SelectValue placeholder="Selecione uma cor" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">Sem cor específica</SelectItem>
                                <SelectItem 
                                    v-for="cor in cores" 
                                    :key="cor.id" 
                                    :value="cor.id.toString()"
                                >
                                    {{ cor.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.cor_id" />
                    </div>

                    <!-- Tamanho -->
                    <div class="space-y-2">
                        <Label for="tamanho_id">Tamanho</Label>
                        <Select
                            v-model="form.tamanho_id"
                            :disabled="processing"
                        >
                            <SelectTrigger :class="{ 'border-red-500': errors.tamanho_id }">
                                <SelectValue placeholder="Selecione um tamanho" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">Sem tamanho específico</SelectItem>
                                <SelectItem 
                                    v-for="tamanho in tamanhos" 
                                    :key="tamanho.id" 
                                    :value="tamanho.id.toString()"
                                >
                                    {{ tamanho.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.tamanho_id" />
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Preços -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Preços Específicos</CardTitle>
                <CardDescription>
                    Preços específicos para esta variação (opcional)
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Preço de Custo -->
                    <div class="space-y-2">
                        <Label for="preco_custo">Preço de Custo</Label>
                        <Input
                            id="preco_custo"
                            v-model="precoCustoInput"
                            @input="handlePrecoCustoInput"
                            type="text"
                            placeholder="R$ 0,00"
                            :class="{ 'border-red-500': errors.preco_custo }"
                            :disabled="processing"
                        />
                        <InputError :message="errors.preco_custo" />
                        <p class="text-xs text-muted-foreground">
                            Deixe em branco para usar o preço do produto base
                        </p>
                    </div>

                    <!-- Preço de Venda -->
                    <div class="space-y-2">
                        <Label for="preco_venda">Preço de Venda</Label>
                        <Input
                            id="preco_venda"
                            v-model="precoVendaInput"
                            @input="handlePrecoVendaInput"
                            type="text"
                            placeholder="R$ 0,00"
                            :class="{ 'border-red-500': errors.preco_venda }"
                            :disabled="processing"
                        />
                         <InputError :message="errors.preco_venda" />
                        <p class="text-xs text-muted-foreground">
                            Deixe em branco para usar o preço do produto base
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Código de Barras e Status -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Identificação e Status</CardTitle>
            </CardHeader>
            <CardContent class="space-y-6">
                <!-- Código de Barras -->
                <div class="space-y-2">
                    <Label for="codigo_barras">Código de Barras</Label>
                    <Input
                        id="codigo_barras"
                        v-model="form.codigo_barras"
                        type="text"
                        placeholder="Digite o código de barras específico (opcional)"
                        :class="{ 'border-red-500': errors.codigo_barras }"
                        :disabled="processing"
                    />
                    <InputError :message="errors.codigo_barras" />
                </div>

                <!-- Status Ativo -->
                <div class="flex items-center space-x-2">
                    <Switch
                        id="ativo"
                        v-model:checked="ativo"
                        :disabled="processing"
                    />
                    <Label for="ativo">Variação ativa</Label>
                </div>
                <InputError :message="errors.ativo" />
            </CardContent>
        </Card>
    </div>
</template>
