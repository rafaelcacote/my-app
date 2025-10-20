<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';

interface Empresa {
    id?: number;
    razao_social?: string;
    nome_fantasia?: string;
    cnpj?: string | null;
    email?: string;
    logo_path?: string | null;
    logo_url?: string | null;
    telefone?: string | null;
    ativo?: boolean;
    data_adesao?: string;
    data_expiracao?: string | null;
}

interface Props {
    empresa?: Empresa;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    empresa: () => ({}),
    isCreate: false
});

// Estado local para campos formatados
const cnpjInput = ref(props.empresa?.cnpj || '');
const telefoneInput = ref(props.empresa?.telefone || '');
const logoPreview = ref(props.empresa?.logo_url || '');
const ativo = ref(props.isCreate ? true : !!props.empresa?.ativo);
const logoFile = ref<File | null>(null);

// Atualiza os campos quando a empresa muda
watch(() => props.empresa, (newEmpresa) => {
    if (newEmpresa) {
        cnpjInput.value = newEmpresa.cnpj || '';
        telefoneInput.value = newEmpresa.telefone || '';
        logoPreview.value = newEmpresa.logo_url || '';
        ativo.value = !!newEmpresa.ativo;
    }
}, { deep: true });

// Função para lidar com o upload de imagens
const handleLogoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        console.log('File selected:', file);
        console.log('File type:', file.type);
        console.log('File size:', file.size);
        
        logoFile.value = file;
        
        // Se estiver usando useForm, atualiza o form
        if (props.form) {
            props.form.logo = file;
            console.log('Form logo updated:', props.form.logo);
        }
        
        // Cria uma URL para pré-visualização
        logoPreview.value = URL.createObjectURL(file);
    }
};

// Formatação de CNPJ
const formatCNPJ = (value: string) => {
    // Remove tudo que não é dígito
    const digits = value.replace(/\D/g, '');
    
    // Formata no padrão XX.XXX.XXX/XXXX-XX
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

// Formatação de telefone
const formatPhone = (value: string) => {
    // Remove tudo que não é dígito
    const digits = value.replace(/\D/g, '');
    
    // Formata no padrão (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
    if (digits.length <= 2) return digits;
    if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
};

const handlePhoneInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    telefoneInput.value = formatPhone(input.value);
};

const onAtivoChange = (value: boolean) => {
    ativo.value = value;
};

// Data padrão para data de adesão em criação
const defaultDataAdesao = new Date().toISOString().split('T')[0];
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader class="space-y-1 pb-6">
            <CardTitle class="text-2xl">Dados da Empresa</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados cadastrais da empresa' : 'Atualize os dados cadastrais da empresa' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Linha 1: Razão Social, Nome Fantasia, CNPJ -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Razão Social -->
                <div class="space-y-2">
                    <Label for="razao_social">
                        Razão Social
                    </Label>
                    <Input
                        v-if="form"
                        id="razao_social"
                        name="razao_social"
                        v-model="form.razao_social"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors.razao_social }"
                        placeholder="Ex: Empresa LTDA"
                        :disabled="processing"
                        autocomplete="organization"
                    />
                    <Input
                        v-else
                        id="razao_social"
                        name="razao_social"
                        :default-value="empresa?.razao_social"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors.razao_social }"
                        placeholder="Ex: Empresa LTDA"
                        :disabled="processing"
                        autocomplete="organization"
                    />
                    <InputError :message="errors.razao_social || (!isCreate && !empresa?.razao_social ? 'A razão social é obrigatória.' : '')" />
                </div>

                <!-- Nome Fantasia -->
                <div class="space-y-2">
                    <Label for="nome_fantasia">
                        Nome Fantasia
                    </Label>
                    <Input
                        v-if="form"
                        id="nome_fantasia"
                        name="nome_fantasia"
                        v-model="form.nome_fantasia"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors.nome_fantasia }"
                        placeholder="Ex: Empresa"
                        :disabled="processing"
                    />
                    <Input
                        v-else
                        id="nome_fantasia"
                        name="nome_fantasia"
                        :default-value="empresa?.nome_fantasia"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors.nome_fantasia }"
                        placeholder="Ex: Empresa"
                        :disabled="processing"
                    />
                    <InputError :message="errors.nome_fantasia || (!isCreate && !empresa?.nome_fantasia ? 'O nome fantasia é obrigatório.' : '')" />
                </div>

                <!-- CNPJ -->
                <div class="space-y-2">
                    <Label for="cnpj">CNPJ</Label>
                    <Input
                        v-if="form"
                        id="cnpj"
                        name="cnpj"
                        v-model="form.cnpj"
                        @input="form.cnpj = formatCNPJ($event.target.value)"
                        placeholder="00.000.000/0000-00"
                        maxlength="18"
                        :disabled="processing"
                    />
                    <Input
                        v-else
                        id="cnpj"
                        name="cnpj"
                        v-model="cnpjInput"
                        @input="handleCNPJInput"
                        placeholder="00.000.000/0000-00"
                        maxlength="18"
                        :disabled="processing"
                    />
                    <InputError :message="errors.cnpj" />
                </div>
            </div>

            <!-- Linha 2: Telefone, Email, Data Adesão, Data Expiração -->
            <div class="grid gap-4 md:grid-cols-4">
                <!-- Telefone -->
                <div class="space-y-2">
                    <Label for="telefone">Telefone</Label>
                    <Input
                        v-if="form"
                        id="telefone"
                        name="telefone"
                        v-model="form.telefone"
                        @input="form.telefone = formatPhone($event.target.value)"
                        placeholder="(00) 00000-0000"
                        maxlength="15"
                        :disabled="processing"
                        autocomplete="tel"
                    />
                    <Input
                        v-else
                        id="telefone"
                        name="telefone"
                        v-model="telefoneInput"
                        @input="handlePhoneInput"
                        placeholder="(00) 00000-0000"
                        maxlength="15"
                        :disabled="processing"
                        autocomplete="tel"
                    />
                    <InputError :message="errors.telefone" />
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        v-if="form"
                        id="email"
                        name="email"
                        type="email"
                        v-model="form.email"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors.email }"
                        placeholder="empresa@example.com"
                        :disabled="processing"
                        autocomplete="email"
                    />
                    <Input
                        v-else
                        id="email"
                        name="email"
                        type="email"
                        :default-value="empresa?.email"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors.email }"
                        placeholder="empresa@example.com"
                        :disabled="processing"
                        autocomplete="email"
                    />
                    <InputError :message="errors.email || (!isCreate && !empresa?.email ? 'O email é obrigatório.' : '')" />
                </div>

                <!-- Data de Adesão -->
                <div class="space-y-2">
                    <Label for="data_adesao">Data de Adesão</Label>
                    <Input
                        v-if="form"
                        id="data_adesao"
                        name="data_adesao"
                        type="date"
                        v-model="form.data_adesao"
                        :disabled="processing"
                    />
                    <Input
                        v-else
                        id="data_adesao"
                        name="data_adesao"
                        type="date"
                        :default-value="isCreate ? defaultDataAdesao : empresa?.data_adesao?.split('T')[0]"
                        :disabled="processing"
                    />
                    <InputError :message="errors.data_adesao" />
                </div>

                <!-- Data de Expiração -->
                <div class="space-y-2">
                    <Label for="data_expiracao">Data de Expiração</Label>
                    <Input
                        v-if="form"
                        id="data_expiracao"
                        name="data_expiracao"
                        type="date"
                        v-model="form.data_expiracao"
                        :disabled="processing"
                    />
                    <Input
                        v-else
                        id="data_expiracao"
                        name="data_expiracao"
                        type="date"
                        :default-value="empresa?.data_expiracao?.split('T')[0]"
                        :disabled="processing"
                    />
                    <InputError :message="errors.data_expiracao" />
                </div>
            </div>

            <!-- Linha 3: Status e Logo -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Status da Empresa -->
                <div class="space-y-2">
                    <Label for="ativo">Status da Empresa</Label>
                    <div class="flex items-center space-x-2 pt-2">
                        <Switch
                            id="ativo"
                            :checked="form ? form.ativo === 1 : ativo"
                            @update:checked="(value) => { 
                                if (form) {
                                    form.ativo = value ? 1 : 0;
                                } else {
                                    onAtivoChange(value);
                                }
                            }"
                            :disabled="processing"
                        />
                        <Label for="ativo" class="cursor-pointer">
                            {{ (form ? form.ativo === 1 : ativo) ? 'Ativa' : 'Inativa' }}
                        </Label>
                        <input v-if="!form" type="hidden" name="ativo" :value="ativo ? 1 : 0" />
                    </div>
                    <InputError :message="errors.ativo" />
                </div>

                <!-- Logo -->
                <div class="space-y-2">
                    <Label for="logo">Logo da Empresa</Label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <Input
                                id="logo"
                                name="logo"
                                type="file"
                                accept="image/png, image/jpeg, image/jpg, image/gif"
                                @change="handleLogoUpload"
                                :class="{ 'border-red-500 focus-visible:ring-red-500': errors.logo }"
                                :disabled="processing"
                            />
                            <InputError :message="errors.logo" />
                        </div>
                        
                        <div v-if="logoPreview" class="flex-shrink-0">
                            <p class="text-sm text-muted-foreground mb-1">Pré-visualização:</p>
                            <div class="w-24 h-24 rounded border overflow-hidden flex items-center justify-center bg-gray-50">
                                <img 
                                    :src="logoPreview" 
                                    alt="Logo da empresa" 
                                    class="max-w-full max-h-full object-contain"
                                    @error="() => { logoPreview = '' }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

