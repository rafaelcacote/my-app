<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';

// Props
interface Cliente {
    id: number;
    nome: string;
}

interface Loja {
    id: number;
    nome: string;
}

interface Venda {
    id: number;
    numero_venda: string;
    cliente: Cliente | null;
    loja: Loja;
}

interface Props {
    pagamento?: any;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    venda?: Venda;
    isCreate?: boolean;
}

const props = defineProps<Props>();

// Formatação de valor monetário
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};

// Watchers para formatação automática
watch(() => props.form?.valor, (newValue) => {
    if (props.form && newValue) {
        // Remove formatação para cálculo
        const numericValue = parseFloat(newValue.toString().replace(/[^\d,]/g, '').replace(',', '.'));
        if (!isNaN(numericValue)) {
            props.form.valor = numericValue;
        }
    }
});

// Computed para valor formatado
const valorFormatado = computed(() => {
    if (!props.form?.valor) return '';
    return formatCurrency(props.form.valor);
});
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle>Dados do Pagamento</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados do pagamento' : 'Atualize os dados do pagamento' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Informações da Venda (se fornecida) -->
            <div v-if="venda" class="space-y-4">
                <h3 class="text-lg font-semibold">Venda Relacionada</h3>
                <div class="rounded-lg bg-muted p-4">
                    <div class="grid gap-2 text-sm md:grid-cols-3">
                        <div>
                            <span class="font-medium text-muted-foreground">Número:</span>
                            <span class="ml-2 text-foreground">{{ venda.numero_venda }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-muted-foreground">Cliente:</span>
                            <span class="ml-2 text-foreground">{{ venda.cliente?.nome || 'Cliente não informado' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-muted-foreground">Loja:</span>
                            <span class="ml-2 text-foreground">{{ venda.loja.nome }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados do Pagamento -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Venda ID (se não fornecida) -->
                <div v-if="!venda" class="space-y-2">
                    <Label for="venda_id">Venda *</Label>
                    <Input
                        id="venda_id"
                        v-model="form.venda_id"
                        type="number"
                        :class="{ 'border-red-500': errors.venda_id }"
                        placeholder="ID da venda"
                        :disabled="processing"
                    />
                    <InputError :message="errors.venda_id" />
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

                <!-- Valor -->
                <div class="space-y-2">
                    <Label for="valor">Valor *</Label>
                    <Input
                        id="valor"
                        v-model.number="form.valor"
                        type="number"
                        step="0.01"
                        min="0.01"
                        :class="{ 'border-red-500': errors.valor }"
                        placeholder="0,00"
                        :disabled="processing"
                    />
                    <InputError :message="errors.valor" />
                    <p v-if="form.valor" class="text-sm text-muted-foreground">
                        Valor formatado: {{ valorFormatado }}
                    </p>
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
                            <SelectItem value="pago">Pago</SelectItem>
                            <SelectItem value="cancelado">Cancelado</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.status" />
                </div>

                <!-- Data do Pagamento -->
                <div class="space-y-2">
                    <Label for="data_pagamento">Data do Pagamento</Label>
                    <Input
                        id="data_pagamento"
                        v-model="form.data_pagamento"
                        type="datetime-local"
                        :class="{ 'border-red-500': errors.data_pagamento }"
                        :disabled="processing"
                    />
                    <InputError :message="errors.data_pagamento" />
                </div>
            </div>

            <!-- Observações -->
            <div class="space-y-2">
                <Label for="observacoes">Observações</Label>
                <Textarea
                    id="observacoes"
                    v-model="form.observacoes"
                    :class="{ 'border-red-500': errors.observacoes }"
                    placeholder="Observações sobre o pagamento..."
                    :disabled="processing"
                />
                <InputError :message="errors.observacoes" />
            </div>

            <!-- Informações Adicionais -->
            <div v-if="!isCreate" class="space-y-4">
                <h3 class="text-lg font-semibold">Informações do Sistema</h3>
                <div class="grid gap-2 text-sm md:grid-cols-2">
                    <div class="flex justify-between">
                        <span class="font-medium text-muted-foreground">ID do Pagamento:</span>
                        <span class="font-mono text-foreground">{{ pagamento?.id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-muted-foreground">Criado em:</span>
                        <span class="text-foreground">
                            {{ pagamento?.created_at ? new Date(pagamento.created_at).toLocaleDateString('pt-BR') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

