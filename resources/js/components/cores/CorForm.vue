<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';

interface Cor {
    id: number;
    nome: string;
    codigo_hex: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    cor?: Cor;
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
            <CardTitle>Dados da Cor</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados da cor' : 'Atualize os dados da cor' }}
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
                    placeholder="Digite o nome da cor"
                    :class="{ 'border-red-500': errors.nome }"
                    :disabled="processing"
                />
                <InputError :message="errors.nome" />
            </div>

            <!-- Código HEX -->
            <div class="space-y-2">
                <Label for="codigo_hex">Código HEX *</Label>
                <div class="flex items-center gap-3">
                    <Input
                        id="codigo_hex"
                        v-model="form.codigo_hex"
                        type="text"
                        placeholder="#000000"
                        :class="{ 'border-red-500': errors.codigo_hex }"
                        :disabled="processing"
                        class="flex-1"
                    />
                    <div 
                        class="h-10 w-16 rounded border-2 border-border"
                        :style="{ backgroundColor: form.codigo_hex || '#000000' }"
                    />
                </div>
                <InputError :message="errors.codigo_hex" />
                <p class="text-xs text-muted-foreground">
                    Digite o código hexadecimal da cor (ex: #FF0000 para vermelho)
                </p>
            </div>
        </CardContent>
    </Card>
</template>
