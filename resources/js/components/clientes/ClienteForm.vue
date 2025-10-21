<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';

// Props
interface Props {
    cliente?: any;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
}

const props = defineProps<Props>();

// Formatação de CPF/CNPJ
const formatCPFCNPJ = (value: string) => {
    const digits = value.replace(/\D/g, '');
    const tipo = props.form?.tipo || props.cliente?.tipo || 'fisica';
    
    if (tipo === 'fisica') {
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


// Watchers para formatação automática
watch(() => props.form?.cpf_cnpj, (newValue) => {
    if (props.form && newValue) {
        props.form.cpf_cnpj = formatCPFCNPJ(newValue);
    }
});

watch(() => props.form?.telefone, (newValue) => {
    if (props.form && newValue) {
        props.form.telefone = formatPhone(newValue);
    }
});

</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle>Dados do Cliente</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados cadastrais do cliente' : 'Atualize os dados cadastrais do cliente' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Dados Básicos -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Dados Básicos</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Nome -->
                    <div class="space-y-2">
                        <Label for="nome">Nome *</Label>
                        <Input
                            id="nome"
                            v-model="form.nome"
                            :class="{ 'border-red-500': errors.nome }"
                            placeholder="Nome completo"
                            :disabled="processing"
                        />
                        <InputError :message="errors.nome" />
                    </div>

                    <!-- Tipo -->
                    <div class="space-y-2">
                        <Label for="tipo">Tipo *</Label>
                        <Select v-model="form.tipo" :disabled="processing">
                            <SelectTrigger :class="{ 'border-red-500': errors.tipo }">
                                <SelectValue placeholder="Selecione o tipo" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="fisica">Pessoa Física</SelectItem>
                                <SelectItem value="juridica">Pessoa Jurídica</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.tipo" />
                    </div>

                    <!-- CPF/CNPJ -->
                    <div class="space-y-2">
                        <Label for="cpf_cnpj">{{ form.tipo === 'fisica' ? 'CPF' : 'CNPJ' }}</Label>
                        <Input
                            id="cpf_cnpj"
                            v-model="form.cpf_cnpj"
                            :class="{ 'border-red-500': errors.cpf_cnpj }"
                            :placeholder="form.tipo === 'fisica' ? '000.000.000-00' : '00.000.000/0000-00'"
                            :disabled="processing"
                        />
                        <InputError :message="errors.cpf_cnpj" />
                    </div>

                    <!-- Data de Nascimento -->
                    <div class="space-y-2">
                        <Label for="data_nascimento">Data de Nascimento</Label>
                        <Input
                            id="data_nascimento"
                            v-model="form.data_nascimento"
                            type="date"
                            :class="{ 'border-red-500': errors.data_nascimento }"
                            :disabled="processing"
                        />
                        <InputError :message="errors.data_nascimento" />
                    </div>
                </div>
            </div>

            <!-- Contato -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Contato</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Email -->
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            :class="{ 'border-red-500': errors.email }"
                            placeholder="email@exemplo.com"
                            :disabled="processing"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <!-- Telefone -->
                    <div class="space-y-2">
                        <Label for="telefone">Telefone</Label>
                        <Input
                            id="telefone"
                            v-model="form.telefone"
                            :class="{ 'border-red-500': errors.telefone }"
                            placeholder="(00) 00000-0000"
                            :disabled="processing"
                        />
                        <InputError :message="errors.telefone" />
                    </div>
                </div>
            </div>


            <!-- Status -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Status</h3>
                <div class="flex items-center space-x-2">
                    <Switch
                        id="ativo"
                        v-model:checked="form.ativo"
                        :disabled="processing"
                    />
                    <Label for="ativo">Cliente ativo</Label>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

