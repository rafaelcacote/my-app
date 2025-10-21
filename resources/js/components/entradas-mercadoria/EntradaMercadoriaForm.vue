<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle>Dados da Entrada</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados da entrada de mercadoria' : 'Atualize os dados da entrada de mercadoria' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Informações Básicas -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium">Informações Básicas</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="loja_id">Loja *</Label>
                        <Select v-model="form.loja_id">
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

                    <div class="space-y-2">
                        <Label for="fornecedor_id">Fornecedor</Label>
                        <Select v-model="form.fornecedor_id">
                            <SelectTrigger :class="{ 'border-red-500': errors.fornecedor_id }">
                                <SelectValue placeholder="Selecione o fornecedor" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">Sem fornecedor</SelectItem>
                                <SelectItem v-for="fornecedor in fornecedores" :key="fornecedor.id" :value="fornecedor.id.toString()">
                                    {{ fornecedor.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.fornecedor_id" />
                    </div>

                    <div class="space-y-2">
                        <Label for="numero_nota">Número da Nota</Label>
                        <Input
                            id="numero_nota"
                            v-model="form.numero_nota"
                            placeholder="Número da nota fiscal"
                            :class="{ 'border-red-500': errors.numero_nota }"
                        />
                        <InputError :message="errors.numero_nota" />
                    </div>

                    <div class="space-y-2">
                        <Label for="data_entrada">Data de Entrada *</Label>
                        <Input
                            id="data_entrada"
                            v-model="form.data_entrada"
                            type="date"
                            :class="{ 'border-red-500': errors.data_entrada }"
                        />
                        <InputError :message="errors.data_entrada" />
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <Label for="observacoes">Observações</Label>
                        <Textarea
                            id="observacoes"
                            v-model="form.observacoes"
                            placeholder="Observações sobre a entrada..."
                            rows="3"
                            :class="{ 'border-red-500': errors.observacoes }"
                        />
                        <InputError :message="errors.observacoes" />
                    </div>
                </div>
            </div>

            <!-- Itens da Entrada -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium">Itens da Entrada</h3>
                </div>

                <!-- Campo de Adição de Item -->
                <div class="border rounded-lg p-4 space-y-4">
                    <h4 class="font-medium">Adicionar Novo Item</h4>
                    
                    <div class="grid gap-4 md:grid-cols-4">
                        <!-- Produto com Autocomplete -->
                        <div class="space-y-2">
                            <Label>Produto *</Label>
                            <div class="relative">
                                <Input
                                    ref="produtoInputRef"
                                    v-model="currentItem.produtoSearch"
                                    placeholder="Digite o nome do produto..."
                                    @keydown.down.prevent="selectNextSuggestion"
                                    @keydown.up.prevent="selectPrevSuggestion"
                                    @keydown.enter.prevent="selectProduto"
                                    @input="filterProdutos"
                                    @focus="showSuggestions = true"
                                    @blur="hideSuggestions"
                                />
                                
                                <!-- Dropdown de Sugestões -->
                                <div 
                                    v-if="showSuggestions && filteredProdutos.length > 0" 
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
                                >
                                    <div 
                                        v-for="(variacao, index) in filteredProdutos" 
                                        :key="variacao.id"
                                        :class="[
                                            'px-3 py-2 cursor-pointer text-sm',
                                            selectedSuggestionIndex === index ? 'bg-blue-100 text-blue-900' : 'hover:bg-gray-100'
                                        ]"
                                        @click="selectProdutoVariacao(variacao)"
                                    >
                                        {{ getProdutoVariacaoLabel(variacao) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quantidade -->
                        <div class="space-y-2">
                            <Label>Quantidade *</Label>
                            <Input
                                ref="quantidadeInputRef"
                                v-model.number="currentItem.quantidade"
                                type="number"
                                min="1"
                                placeholder="0"
                                @keydown.enter.prevent="focusPrecoUnitario"
                            />
                        </div>

                        <!-- Preço Unitário -->
                        <div class="space-y-2">
                            <Label>Preço Unitário *</Label>
                            <Input
                                ref="precoUnitarioInputRef"
                                v-model.number="currentItem.preco_unitario"
                                type="number"
                                step="0.01"
                                min="0.01"
                                placeholder="0.00"
                                @keydown.enter.prevent="addCurrentItem"
                                @input="calculateCurrentItemTotal"
                            />
                        </div>

                        <!-- Total -->
                        <div class="space-y-2">
                            <Label>Total</Label>
                            <Input
                                v-model="currentItem.total"
                                readonly
                                placeholder="0.00"
                                class="bg-muted"
                            />
                        </div>
                    </div>
                    
                    <!-- Botão para adicionar item -->
                    <div class="flex justify-end">
                        <Button 
                            type="button" 
                            @click="addCurrentItem"
                            :disabled="!canAddItem"
                            variant="outline"
                            size="sm"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Adicionar Item
                        </Button>
                    </div>
                </div>

                <!-- Lista de Itens Adicionados -->
                <div v-if="form.itens.length === 0" class="text-center py-8 text-muted-foreground border-2 border-dashed rounded-lg">
                    Nenhum item adicionado ainda.
                </div>

                <div v-else class="space-y-4">
                    <div v-for="(item, index) in form.itens" :key="index" class="border rounded-lg p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="font-medium">Item {{ index + 1 }}</h4>
                            <Button type="button" variant="outline" size="sm" @click="removeItem(index)">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>

                        <div class="grid gap-4 md:grid-cols-4">
                            <div class="space-y-2">
                                <Label>Produto</Label>
                                <Input
                                    :value="getProdutoVariacaoLabelById(item.produto_variacao_id)"
                                    readonly
                                    class="bg-muted"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>Quantidade</Label>
                                <Input
                                    :value="item.quantidade"
                                    readonly
                                    class="bg-muted"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>Preço Unitário</Label>
                                <Input
                                    :value="formatCurrency(item.preco_unitario)"
                                    readonly
                                    class="bg-muted"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>Total</Label>
                                <Input
                                    :value="formatCurrency(item.total)"
                                    readonly
                                    class="bg-muted"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Geral -->
                <div v-if="form.itens.length > 0" class="border-t pt-4">
                    <div class="flex justify-end">
                        <div class="text-right">
                            <p class="text-sm text-muted-foreground">Valor Total da Entrada</p>
                            <p class="text-2xl font-bold">{{ formatCurrency(totalGeral) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Plus, Trash2 } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';

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
    loja_id: number;
    fornecedor_id: number | null;
    numero_nota: string | null;
    data_entrada: string;
    valor_total: number;
    observacoes: string | null;
    itens?: Array<{
        id: number;
        produto_variacao_id: number;
        quantidade: number;
        preco_unitario: number;
        total: number;
    }>;
}

interface Props {
    entrada?: EntradaMercadoria;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
    lojas: Loja[];
    fornecedores: Fornecedor[];
    produtoVariacoes: ProdutoVariacao[];
}

const props = defineProps<Props>();

// Refs para os inputs
const produtoInputRef = ref<HTMLInputElement>();
const quantidadeInputRef = ref<HTMLInputElement>();
const precoUnitarioInputRef = ref<HTMLInputElement>();

// Estado do item atual sendo digitado
const currentItem = ref({
    produtoSearch: '',
    produto_variacao_id: null as number | null,
    quantidade: 1,
    preco_unitario: 0,
    total: 0,
});

// Estado do autocomplete
const showSuggestions = ref(false);
const selectedSuggestionIndex = ref(-1);
const filteredProdutos = ref<ProdutoVariacao[]>([]);

const getProdutoVariacaoLabel = (variacao: ProdutoVariacao) => {
    const produto = variacao.produto?.nome || 'Produto';
    const cor = variacao.cor?.nome;
    const tamanho = variacao.tamanho?.nome;
    
    let label = produto;
    if (cor) label += ` - ${cor}`;
    if (tamanho) label += ` - ${tamanho}`;
    
    return label;
};

const getProdutoVariacaoLabelById = (id: number) => {
    const variacao = props.produtoVariacoes.find(v => v.id === id);
    return variacao ? getProdutoVariacaoLabel(variacao) : '';
};

const filterProdutos = () => {
    const search = currentItem.value.produtoSearch.toLowerCase();
    if (search.length < 2) {
        filteredProdutos.value = [];
        showSuggestions.value = false;
        return;
    }
    
    filteredProdutos.value = props.produtoVariacoes.filter(variacao => 
        getProdutoVariacaoLabel(variacao).toLowerCase().includes(search)
    );
    selectedSuggestionIndex.value = -1;
    showSuggestions.value = true;
};

const selectNextSuggestion = () => {
    if (filteredProdutos.value.length > 0) {
        selectedSuggestionIndex.value = Math.min(
            selectedSuggestionIndex.value + 1, 
            filteredProdutos.value.length - 1
        );
    }
};

const selectPrevSuggestion = () => {
    if (selectedSuggestionIndex.value > 0) {
        selectedSuggestionIndex.value--;
    }
};

const selectProduto = () => {
    if (selectedSuggestionIndex.value >= 0 && filteredProdutos.value[selectedSuggestionIndex.value]) {
        selectProdutoVariacao(filteredProdutos.value[selectedSuggestionIndex.value]);
    }
};

const selectProdutoVariacao = (variacao: ProdutoVariacao) => {
    currentItem.value.produto_variacao_id = variacao.id;
    currentItem.value.produtoSearch = getProdutoVariacaoLabel(variacao);
    showSuggestions.value = false;
    selectedSuggestionIndex.value = -1;
    
    // Focar no próximo campo
    nextTick(() => {
        quantidadeInputRef.value?.focus();
    });
};

const hideSuggestions = () => {
    // Delay para permitir clique no dropdown
    setTimeout(() => {
        showSuggestions.value = false;
    }, 200);
};

const focusPrecoUnitario = () => {
    precoUnitarioInputRef.value?.focus();
};

const calculateCurrentItemTotal = () => {
    currentItem.value.total = (currentItem.value.quantidade || 0) * (currentItem.value.preco_unitario || 0);
};

const canAddItem = computed(() => {
    return currentItem.value.produto_variacao_id && 
           currentItem.value.quantidade > 0 && 
           currentItem.value.preco_unitario > 0;
});

const addCurrentItem = () => {
    if (!canAddItem.value) return;
    
    if (props.form) {
        props.form.itens.push({
            produto_variacao_id: currentItem.value.produto_variacao_id!,
            quantidade: currentItem.value.quantidade,
            preco_unitario: currentItem.value.preco_unitario,
            total: currentItem.value.total,
        });
        
        // Limpar o item atual
        currentItem.value = {
            produtoSearch: '',
            produto_variacao_id: null,
            quantidade: 1,
            preco_unitario: 0,
            total: 0,
        };
        
        // Focar novamente no campo produto
        nextTick(() => {
            produtoInputRef.value?.focus();
        });
        
        calculateTotalGeral();
    }
};

const removeItem = (index: number) => {
    if (props.form) {
        props.form.itens.splice(index, 1);
        calculateTotalGeral();
    }
};

const calculateTotalGeral = () => {
    if (props.form) {
        const total = props.form.itens.reduce((sum: number, item: any) => sum + (item.total || 0), 0);
        props.form.valor_total = total;
    }
};

const totalGeral = computed(() => {
    if (props.form) {
        return props.form.itens.reduce((sum: number, item: any) => sum + (item.total || 0), 0);
    }
    return 0;
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};

// Inicializar itens se for edição
watch(() => props.entrada, (entrada) => {
    if (entrada && entrada.itens && props.form) {
        props.form.itens = entrada.itens.map(item => ({
            produto_variacao_id: item.produto_variacao_id,
            quantidade: item.quantidade,
            preco_unitario: item.preco_unitario,
            total: item.total,
        }));
    }
}, { immediate: true });
</script>
