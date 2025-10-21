<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { useForm } from '@inertiajs/vue3';

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
    form: any;
    produto?: Produto;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
    categorias: Categoria[];
    marcas: Marca[];
}

const props = defineProps<Props>();

const formatCurrency = (value: string) => {
    // Remove tudo que não é dígito
    const digits = value.replace(/\D/g, '');
    // Converte para centavos
    const amount = parseInt(digits) / 100;
    // Formata como moeda
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(amount);
};

const parseCurrency = (value: string) => {
    // Remove símbolos de moeda e espaços
    const clean = value.replace(/[R$\s.]/g, '').replace(',', '.');
    return parseFloat(clean) || 0;
};
</script>

<template>
    <div class="space-y-6">
        <!-- Informações Básicas -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Informações Básicas</CardTitle>
                <CardDescription>
                    Dados principais do produto
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <!-- Nome -->
                <div class="space-y-2">
                    <Label for="nome">Nome *</Label>
                    <Input
                        id="nome"
                        v-model="form.nome"
                        type="text"
                        placeholder="Digite o nome do produto"
                        :class="{ 'border-red-500': errors.nome }"
                        :disabled="processing"
                    />
                    <InputError :message="errors.nome" />
                </div>

                <!-- Descrição -->
                <div class="space-y-2">
                    <Label for="descricao">Descrição</Label>
                    <Textarea
                        id="descricao"
                        v-model="form.descricao"
                        placeholder="Digite uma descrição para o produto (opcional)"
                        :class="{ 'border-red-500': errors.descricao }"
                        :disabled="processing"
                        rows="3"
                    />
                    <InputError :message="errors.descricao" />
                </div>

                <!-- Código de Barras -->
                <div class="space-y-2">
                    <Label for="codigo_barras">Código de Barras</Label>
                    <Input
                        id="codigo_barras"
                        v-model="form.codigo_barras"
                        type="text"
                        placeholder="Digite o código de barras (opcional)"
                        :class="{ 'border-red-500': errors.codigo_barras }"
                        :disabled="processing"
                    />
                    <InputError :message="errors.codigo_barras" />
                </div>
            </CardContent>
        </Card>

        <!-- Classificação -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Classificação</CardTitle>
                <CardDescription>
                    Categoria e marca do produto
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Categoria -->
                    <div class="space-y-2">
                        <Label for="categoria_id">Categoria</Label>
                        <Select
                            v-model="form.categoria_id"
                            :disabled="processing"
                        >
                            <SelectTrigger :class="{ 'border-red-500': errors.categoria_id }">
                                <SelectValue placeholder="Selecione uma categoria" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">Sem categoria</SelectItem>
                                <SelectItem 
                                    v-for="categoria in categorias" 
                                    :key="categoria.id" 
                                    :value="categoria.id.toString()"
                                >
                                    {{ categoria.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.categoria_id" />
                    </div>

                    <!-- Marca -->
                    <div class="space-y-2">
                        <Label for="marca_id">Marca</Label>
                        <Select
                            v-model="form.marca_id"
                            :disabled="processing"
                        >
                            <SelectTrigger :class="{ 'border-red-500': errors.marca_id }">
                                <SelectValue placeholder="Selecione uma marca" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">Sem marca</SelectItem>
                                <SelectItem 
                                    v-for="marca in marcas" 
                                    :key="marca.id" 
                                    :value="marca.id.toString()"
                                >
                                    {{ marca.nome }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.marca_id" />
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Preços -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Preços</CardTitle>
                <CardDescription>
                    Preços de custo e venda do produto
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Preço de Custo -->
                    <div class="space-y-2">
                        <Label for="preco_custo">Preço de Custo</Label>
                        <Input
                            id="preco_custo"
                            v-model="form.preco_custo"
                            type="text"
                            placeholder="0,00"
                            :class="{ 'border-red-500': errors.preco_custo }"
                            :disabled="processing"
                        />
                        <InputError :message="errors.preco_custo" />
                    </div>

                    <!-- Preço de Venda -->
                    <div class="space-y-2">
                        <Label for="preco_venda">Preço de Venda</Label>
                        <Input
                            id="preco_venda"
                            v-model="form.preco_venda"
                            type="text"
                            placeholder="0,00"
                            :class="{ 'border-red-500': errors.preco_venda }"
                            :disabled="processing"
                        />
                        <InputError :message="errors.preco_venda" />
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Controle de Estoque -->
        <Card class="border-border shadow-sm">
            <CardHeader>
                <CardTitle>Controle de Estoque</CardTitle>
                <CardDescription>
                    Configurações de estoque do produto
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <!-- Controla Estoque -->
                <div class="flex items-center space-x-2">
                    <Switch
                        id="controla_estoque"
                        v-model:checked="form.controla_estoque"
                        :disabled="processing"
                    />
                    <Label for="controla_estoque">Controlar estoque deste produto</Label>
                </div>
                <InputError :message="errors.controla_estoque" />

                <!-- Estoque Mínimo -->
                <div v-if="form.controla_estoque" class="space-y-2">
                    <Label for="estoque_minimo">Estoque Mínimo</Label>
                    <Input
                        id="estoque_minimo"
                        v-model="form.estoque_minimo"
                        type="number"
                        placeholder="0"
                        min="0"
                        :class="{ 'border-red-500': errors.estoque_minimo }"
                        :disabled="processing"
                    />
                    <InputError :message="errors.estoque_minimo" />
                </div>

                <!-- Status Ativo -->
                <div class="flex items-center space-x-2">
                    <Switch
                        id="ativo"
                        v-model:checked="form.ativo"
                        :disabled="processing"
                    />
                    <Label for="ativo">Produto ativo</Label>
                </div>
                <InputError :message="errors.ativo" />
            </CardContent>
        </Card>
    </div>
</template>
