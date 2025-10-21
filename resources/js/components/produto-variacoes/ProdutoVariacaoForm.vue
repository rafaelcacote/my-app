<script setup lang="ts">
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
                            v-model="form.preco_custo"
                            type="text"
                            placeholder="0,00"
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
                            v-model="form.preco_venda"
                            type="text"
                            placeholder="0,00"
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
                        v-model:checked="form.ativo"
                        :disabled="processing"
                    />
                    <Label for="ativo">Variação ativa</Label>
                </div>
                <InputError :message="errors.ativo" />
            </CardContent>
        </Card>
    </div>
</template>
