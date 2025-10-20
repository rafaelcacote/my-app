<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';

interface Loja {
    id?: number;
    empresa_id?: number;
    nome?: string;
    cnpj?: string | null;
    telefone?: string | null;
    email?: string | null;
    ativo?: boolean;
}

interface Props {
    loja?: Loja;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    loja: () => ({}),
    isCreate: false
});

const cnpjInput = ref(props.loja?.cnpj || '');
const telefoneInput = ref(props.loja?.telefone || '');
const ativo = ref(props.isCreate ? true : !!props.loja?.ativo);

watch(() => props.loja, (newLoja) => {
    if (newLoja) {
        cnpjInput.value = newLoja.cnpj || '';
        telefoneInput.value = newLoja.telefone || '';
        ativo.value = !!newLoja.ativo;
    }
}, { deep: true });

const formatCNPJ = (value: string) => {
    const digits = value.replace(/\D/g, '');
    if (digits.length <= 2) return digits;
    if (digits.length <= 5) return `${digits.slice(0, 2)}.${digits.slice(2)}`;
    if (digits.length <= 8) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5)}`;
    if (digits.length <= 12) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8)}`;
    return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12, 14)}`;
};

const handleCNPJInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    cnpjInput.value = formatCNPJ(input.value);
};

const formatPhone = (value: string) => {
    const digits = value.replace(/\D/g, '');
    if (digits.length <= 2) return digits;
    if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
};

const handlePhoneInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    telefoneInput.value = formatPhone(input.value);
};

watch(cnpjInput, (value) => {
    if (props.form) {
        props.form.cnpj = value;
    }
});

watch(telefoneInput, (value) => {
    if (props.form) {
        props.form.telefone = value;
    }
});

watch(ativo, (value) => {
    if (props.form) {
        props.form.ativo = value ? 1 : 0;
    }
});
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle class="text-2xl">Informações da Loja</CardTitle>
            <CardDescription>
                Preencha os dados básicos da loja
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Linha 1: Nome, CNPJ, Email, Telefone -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Nome -->
                <div class="space-y-2">
                    <Label for="nome" class="text-sm font-medium">
                        Nome <span class="text-red-500">*</span>
                    </Label>
                    <Input
                        id="nome"
                        v-model="form.nome"
                        type="text"
                        placeholder="Nome da loja"
                        :disabled="processing"
                        :class="{ 'border-red-500': errors.nome }"
                    />
                    <InputError v-if="errors.nome" :message="errors.nome" />
                </div>

                <!-- CNPJ -->
                <div class="space-y-2">
                    <Label for="cnpj" class="text-sm font-medium">CNPJ</Label>
                    <Input
                        id="cnpj"
                        v-model="cnpjInput"
                        @input="handleCNPJInput"
                        type="text"
                        placeholder="XX.XXX.XXX/XXXX-XX"
                        maxlength="18"
                        :disabled="processing"
                        :class="{ 'border-red-500': errors.cnpj }"
                    />
                    <InputError v-if="errors.cnpj" :message="errors.cnpj" />
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-medium">E-mail</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="email@exemplo.com"
                        :disabled="processing"
                        :class="{ 'border-red-500': errors.email }"
                    />
                    <InputError v-if="errors.email" :message="errors.email" />
                </div>

                <!-- Telefone -->
                <div class="space-y-2">
                    <Label for="telefone" class="text-sm font-medium">Telefone</Label>
                    <Input
                        id="telefone"
                        v-model="telefoneInput"
                        @input="handlePhoneInput"
                        type="text"
                        placeholder="(XX) XXXXX-XXXX"
                        maxlength="15"
                        :disabled="processing"
                        :class="{ 'border-red-500': errors.telefone }"
                    />
                    <InputError v-if="errors.telefone" :message="errors.telefone" />
                </div>
            </div>

            <!-- Linha 2: Status Loja -->
            <div class="flex items-center justify-between space-x-2 rounded-lg border border-border p-4">
                <div class="space-y-0.5">
                    <Label for="ativo" class="text-base font-medium">Status da Loja</Label>
                    <p class="text-sm text-muted-foreground">
                        {{ ativo ? 'A loja está ativa no sistema' : 'A loja está inativa no sistema' }}
                    </p>
                </div>
                <Switch
                    id="ativo"
                    v-model:checked="ativo"
                    :disabled="processing"
                />
            </div>
        </CardContent>
    </Card>
</template>

