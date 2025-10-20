<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';

interface Categoria {
    id: number;
    nome: string;
    descricao: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    categoria?: Categoria;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
}

const props = defineProps<Props>();
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle>Dados da Categoria</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados da categoria' : 'Atualize os dados da categoria' }}
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
                    placeholder="Digite o nome da categoria"
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
                    placeholder="Digite uma descrição para a categoria (opcional)"
                    :class="{ 'border-red-500': errors.descricao }"
                    :disabled="processing"
                    rows="3"
                />
                <InputError :message="errors.descricao" />
            </div>

            <!-- Status Ativo -->
            <div class="flex items-center space-x-2">
                <Switch
                    id="ativo"
                    v-model:checked="form.ativo"
                    :disabled="processing"
                />
                <Label for="ativo">Categoria ativa</Label>
            </div>
            <InputError :message="errors.ativo" />
        </CardContent>
    </Card>
</template>
