<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle>Dados do Fornecedor</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados cadastrais do fornecedor' : 'Atualize os dados cadastrais do fornecedor' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Informações Básicas -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium">Informações Básicas</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="nome">Nome *</Label>
                        <Input
                            id="nome"
                            v-model="form.nome"
                            placeholder="Nome do fornecedor"
                            :class="{ 'border-red-500': errors.nome }"
                        />
                        <InputError :message="errors.nome" />
                    </div>

                    <div class="space-y-2">
                        <Label for="cpf_cnpj">CPF/CNPJ</Label>
                        <Input
                            id="cpf_cnpj"
                            v-model="cpfCnpjFormatted"
                            placeholder="000.000.000-00 ou 00.000.000/0000-00"
                            :class="{ 'border-red-500': errors.cpf_cnpj }"
                        />
                        <InputError :message="errors.cpf_cnpj" />
                    </div>

                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="email@exemplo.com"
                            :class="{ 'border-red-500': errors.email }"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="space-y-2">
                        <Label for="telefone">Telefone</Label>
                        <Input
                            id="telefone"
                            v-model="telefoneFormatted"
                            placeholder="(00) 00000-0000"
                            :class="{ 'border-red-500': errors.telefone }"
                        />
                        <InputError :message="errors.telefone" />
                    </div>


                    <div class="space-y-2">
                        <Label for="ativo">Status</Label>
                        <div class="flex items-center space-x-2">
                            <Switch
                                id="ativo"
                                v-model:checked="form.ativo"
                                :disabled="processing"
                            />
                            <Label for="ativo" class="text-sm font-normal">
                                {{ form.ativo ? 'Ativo' : 'Inativo' }}
                            </Label>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';

interface Fornecedor {
    id: number;
    uuid: string;
    nome: string;
    cpf_cnpj: string | null;
    email: string | null;
    telefone: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    fornecedor?: Fornecedor;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
}

const props = defineProps<Props>();

// Formatação de CPF/CNPJ
const formatCPFCNPJ = (value: string) => {
    const digits = value.replace(/\D/g, '');
    if (digits.length <= 11) {
        // CPF: XXX.XXX.XXX-XX
        if (digits.length <= 3) return digits;
        if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`;
        if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`;
        return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9, 11)}`;
    } else {
        // CNPJ: XX.XXX.XXX/XXXX-XX
        if (digits.length <= 2) return digits;
        if (digits.length <= 5) return `${digits.slice(0, 2)}.${digits.slice(2)}`;
        if (digits.length <= 8) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5)}`;
        if (digits.length <= 12) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8)}`;
        return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12, 14)}`;
    }
};

// Formatação de telefone
const formatPhone = (value: string) => {
    const digits = value.replace(/\D/g, '');
    if (digits.length <= 2) return digits;
    if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
};


const cpfCnpjFormatted = computed({
    get: () => props.form?.cpf_cnpj || props.fornecedor?.cpf_cnpj || '',
    set: (value: string) => {
        const formatted = formatCPFCNPJ(value);
        if (props.form) {
            props.form.cpf_cnpj = formatted;
        }
    }
});

const telefoneFormatted = computed({
    get: () => props.form?.telefone || props.fornecedor?.telefone || '',
    set: (value: string) => {
        const formatted = formatPhone(value);
        if (props.form) {
            props.form.telefone = formatted;
        }
    }
});

</script>
