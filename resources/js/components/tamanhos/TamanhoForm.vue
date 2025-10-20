<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';

interface Tamanho {
    id: number;
    nome: string;
    tipo: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    tamanho?: Tamanho;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
}

const props = defineProps<Props>();

const tiposTamanho = [
    { value: 'numerico', label: 'Numérico' },
    { value: 'alfabetico', label: 'Alfabético' },
    { value: 'alfanumerico', label: 'Alfanumérico' },
];
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle>Dados do Tamanho</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados do tamanho' : 'Atualize os dados do tamanho' }}
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
                    placeholder="Digite o nome do tamanho"
                    :class="{ 'border-red-500': errors.nome }"
                    :disabled="processing"
                />
                <InputError :message="errors.nome" />
            </div>

            <!-- Tipo -->
            <div class="space-y-2">
                <Label for="tipo">Tipo *</Label>
                <Select
                    v-model="form.tipo"
                    :disabled="processing"
                >
                    <SelectTrigger :class="{ 'border-red-500': errors.tipo }">
                        <SelectValue placeholder="Selecione o tipo do tamanho" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem 
                            v-for="tipo in tiposTamanho" 
                            :key="tipo.value" 
                            :value="tipo.value"
                        >
                            {{ tipo.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.tipo" />
            </div>
        </CardContent>
    </Card>
</template>
