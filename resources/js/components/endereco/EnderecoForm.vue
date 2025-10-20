<script setup lang="ts">
import { ref, watch, onMounted, computed, nextTick } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { MapPin } from 'lucide-vue-next';
import axios from 'axios';

interface Estado {
    id: number;
    nome: string;
    uf: string;
}

interface Municipio {
    id: number;
    nome: string;
    estado_id: number;
}

interface Endereco {
    id?: number;
    endereco?: string | null;
    numero?: string | null;
    complemento?: string | null;
    bairro?: string | null;
    municipio_id?: number | null;
    cep?: string | null;
    referencia?: string | null;
}

interface Props {
    endereco?: Endereco | null;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
    modelValue?: Endereco;
}

const props = withDefaults(defineProps<Props>(), {
    endereco: undefined,
    isCreate: false,
    modelValue: () => ({})
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: Endereco): void;
}>();

// Estado local para campos formatados
const cepInput = ref(props.endereco?.cep || props.modelValue?.cep || '');
const estados = ref<Estado[]>([]);
const municipios = ref<Municipio[]>([]);
const selectedEstadoId = ref<number | null>(null);
const loadingCep = ref(false);
const loadingMunicipios = ref(false);
const cepError = ref<string | null>(null);

// Refs para os inputs
const numeroInputRef = ref<HTMLInputElement | null>(null);
const cepInputRef = ref<HTMLInputElement | null>(null);

// Computed para o município selecionado
const selectedMunicipioId = computed({
    get: () => props.form ? props.form.endereco.municipio_id : props.modelValue.municipio_id,
    set: (value) => {
        if (props.form) {
            props.form.endereco.municipio_id = value;
        } else {
            emit('update:modelValue', { ...props.modelValue, municipio_id: value });
        }
    }
});

// Carrega estados ao montar o componente
onMounted(async () => {
    try {
        const response = await axios.get('/api/estados');
        estados.value = response.data;
        
        // Se já tem um município selecionado, carrega o estado correspondente
        if (selectedMunicipioId.value) {
            const municipio = await axios.get(`/api/municipios/${selectedMunicipioId.value}`);
            selectedEstadoId.value = municipio.data.estado_id;
            await loadMunicipios(municipio.data.estado_id);
        }
    } catch (error) {
        console.error('Erro ao carregar estados:', error);
    }
});

// Atualiza os campos quando o endereço muda
watch(() => props.endereco, (newEndereco) => {
    if (newEndereco) {
        cepInput.value = newEndereco.cep || '';
    }
}, { deep: true });

// Watch para mudança de estado
watch(selectedEstadoId, async (newEstadoId) => {
    if (newEstadoId) {
        await loadMunicipios(newEstadoId);
    } else {
        municipios.value = [];
        selectedMunicipioId.value = null;
    }
});

// Carrega municípios por estado
const loadMunicipios = async (estadoId: number) => {
    loadingMunicipios.value = true;
    try {
        const response = await axios.get(`/api/municipios?estado_id=${estadoId}`);
        municipios.value = response.data;
    } catch (error) {
        console.error('Erro ao carregar municípios:', error);
    } finally {
        loadingMunicipios.value = false;
    }
};

// Formatação de CEP
const formatCEP = (value: string) => {
    // Remove tudo que não é dígito
    const digits = value.replace(/\D/g, '');
    
    // Formata no padrão XXXXX-XXX
    if (digits.length <= 5) return digits;
    return `${digits.slice(0, 5)}-${digits.slice(5, 8)}`;
};

// Limpa os campos do endereço
const clearAddressFields = () => {
    if (props.form) {
        props.form.endereco.endereco = '';
        props.form.endereco.bairro = '';
        props.form.endereco.complemento = '';
    } else {
        emit('update:modelValue', {
            ...props.modelValue,
            endereco: '',
            bairro: '',
            complemento: ''
        });
    }
    selectedEstadoId.value = null;
    selectedMunicipioId.value = null;
};

// Busca CEP na API do ViaCEP
const searchCep = async (cep: string) => {
    const cleanCep = cep.replace(/\D/g, '');
    
    if (cleanCep.length !== 8) return;
    
    loadingCep.value = true;
    cepError.value = null; // Limpa erro anterior
    
    try {
        const response = await axios.get(`/api/cep/${cleanCep}`);
        const data = response.data;
        
        // Verifica se há erro na resposta
        if (data.error) {
            cepError.value = data.error;
            clearAddressFields();
            return;
        }
        
        // CEP encontrado com sucesso - limpa erro
        cepError.value = null;
        
        // Preenche os campos automaticamente
        if (props.form) {
            props.form.endereco.endereco = data.endereco || '';
            props.form.endereco.bairro = data.bairro || '';
            props.form.endereco.complemento = data.complemento || '';
            
        } else {
            emit('update:modelValue', {
                ...props.modelValue,
                endereco: data.endereco || '',
                bairro: data.bairro || '',
                complemento: data.complemento || ''
            });
        }
        
        // Seleciona o estado e município
        if (data.estado_id) {
            selectedEstadoId.value = data.estado_id;
            
            // Aguarda o carregamento dos municípios e seleciona o correto
            if (data.municipio_id) {
                setTimeout(() => {
                    selectedMunicipioId.value = data.municipio_id;
                }, 300);
            }
        }
        
        // Move o foco para o campo número após preencher os dados
        await nextTick();
        setTimeout(() => {
            try {
                // Tenta múltiplas formas de acessar o input
                const refValue = numeroInputRef.value as any;
                let inputElement = null;
                
                // Tenta como componente Vue
                if (refValue?.$el) {
                    inputElement = refValue.$el.querySelector('input');
                }
                
                // Tenta direto como HTMLElement
                if (!inputElement && refValue?.tagName) {
                    inputElement = refValue;
                }
                
                // Tenta buscar por ID como fallback
                if (!inputElement) {
                    inputElement = document.getElementById('numero');
                }
                
                if (inputElement && typeof inputElement.focus === 'function') {
                    inputElement.focus();
                }
            } catch (e) {
                // Silenciosamente falha se não conseguir focar
            }
        }, 600); // Aguarda um pouco mais para garantir que os selects foram atualizados
    } catch (error: any) {
        // Trata diferentes tipos de erro
        if (error.response?.status === 404) {
            cepError.value = 'CEP não encontrado';
        } else if (error.response?.data?.error) {
            cepError.value = error.response.data.error;
        } else {
            cepError.value = 'Erro ao buscar CEP. Tente novamente.';
        }
        
        // Limpa os campos quando houver erro
        clearAddressFields();
        
        // Retorna o foco para o campo CEP para o usuário poder digitar novamente
        await nextTick();
        setTimeout(() => {
            try {
                // Tenta múltiplas formas de acessar o input
                const refValue = cepInputRef.value as any;
                let inputElement = null;
                
                // Tenta como componente Vue
                if (refValue?.$el) {
                    inputElement = refValue.$el.querySelector('input');
                }
                
                // Tenta direto como HTMLElement
                if (!inputElement && refValue?.tagName) {
                    inputElement = refValue;
                }
                
                // Tenta buscar por ID como fallback
                if (!inputElement) {
                    inputElement = document.getElementById('cep');
                }
                
                if (inputElement && typeof inputElement.focus === 'function') {
                    inputElement.focus();
                    if (typeof inputElement.select === 'function') {
                        inputElement.select();
                    }
                }
            } catch (e) {
                // Silenciosamente falha se não conseguir focar
            }
        }, 200);
    } finally {
        loadingCep.value = false;
    }
};

const handleCEPInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    cepInput.value = formatCEP(input.value);
    
    // Limpa o erro quando o usuário começa a digitar
    cepError.value = null;
    
    if (props.form) {
        props.form.endereco.cep = cepInput.value;
    } else {
        emit('update:modelValue', { ...props.modelValue, cep: cepInput.value });
    }
    
    // Busca o CEP automaticamente quando completar 8 dígitos
    const cleanCep = cepInput.value.replace(/\D/g, '');
    if (cleanCep.length === 8) {
        searchCep(cepInput.value);
    }
};

const handleInput = (field: keyof Endereco, value: string | number) => {
    if (props.form) {
        if (!props.form.endereco) {
            props.form.endereco = {};
        }
        props.form.endereco[field] = value;
    } else {
        emit('update:modelValue', { ...props.modelValue, [field]: value });
    }
};
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader class="space-y-1 pb-6">
            <div class="flex items-center gap-2">
                <MapPin class="h-6 w-6 text-primary" />
                <CardTitle class="text-2xl">Endereço</CardTitle>
            </div>
            <CardDescription>
                {{ isCreate ? 'Informe o endereço da empresa' : 'Atualize o endereço da empresa' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Linha 1: CEP, Endereço (maior), Número -->
            <div class="grid gap-4 md:grid-cols-12">
                <!-- CEP -->
                <div class="space-y-2 md:col-span-2">
                    <Label for="cep">CEP</Label>
                    <Input
                        v-if="form"
                        ref="cepInputRef"
                        id="cep"
                        name="endereco[cep]"
                        v-model="cepInput"
                        @input="handleCEPInput"
                        placeholder="00000-000"
                        maxlength="9"
                        :disabled="processing || loadingCep"
                    />
                    <Input
                        v-else
                        ref="cepInputRef"
                        id="cep"
                        name="cep"
                        v-model="cepInput"
                        @input="handleCEPInput"
                        placeholder="00000-000"
                        maxlength="9"
                        :disabled="processing || loadingCep"
                    />
                    <p v-if="loadingCep" class="text-xs text-muted-foreground">Buscando CEP...</p>
                    <p v-if="cepError" class="text-xs text-red-500">{{ cepError }}</p>
                    <InputError :message="errors['endereco.cep'] || errors.cep" />
                </div>

                <!-- Endereço -->
                <div class="space-y-2 md:col-span-8">
                    <Label for="endereco">Endereço</Label>
                    <Input
                        v-if="form"
                        id="endereco"
                        name="endereco[endereco]"
                        v-model="form.endereco.endereco"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors['endereco.endereco'] }"
                        placeholder="Ex: Rua Principal"
                        :disabled="processing"
                    />
                    <Input
                        v-else
                        id="endereco"
                        name="endereco"
                        :value="modelValue.endereco"
                        @input="(e: Event) => handleInput('endereco', (e.target as HTMLInputElement).value)"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors.endereco }"
                        placeholder="Ex: Rua Principal"
                        :disabled="processing"
                    />
                    <InputError :message="errors['endereco.endereco'] || errors.endereco" />
                </div>

                <!-- Número -->
                <div class="space-y-2 md:col-span-2">
                    <Label for="numero">Número</Label>
                    <Input
                        v-if="form"
                        id="numero"
                        ref="numeroInputRef"
                        name="endereco[numero]"
                        v-model="form.endereco.numero"
                        placeholder="Ex: 123"
                        :disabled="processing"
                    />
                    <Input
                        v-else
                        id="numero"
                        ref="numeroInputRef"
                        name="numero"
                        :value="modelValue.numero"
                        @input="(e: Event) => handleInput('numero', (e.target as HTMLInputElement).value)"
                        placeholder="Ex: 123"
                        :disabled="processing"
                    />
                    <InputError :message="errors['endereco.numero'] || errors.numero" />
                </div>
            </div>

            <!-- Linha 2: Complemento, Bairro, Estado, Município -->
            <div class="grid gap-4 md:grid-cols-12">
                <!-- Complemento -->
                <div class="space-y-2 md:col-span-3">
                    <Label for="complemento">Complemento</Label>
                    <Input
                        v-if="form"
                        id="complemento"
                        name="endereco[complemento]"
                        v-model="form.endereco.complemento"
                        placeholder="Ex: Apto 101"
                        :disabled="processing"
                    />
                    <Input
                        v-else
                        id="complemento"
                        name="complemento"
                        :value="modelValue.complemento"
                        @input="(e: Event) => handleInput('complemento', (e.target as HTMLInputElement).value)"
                        placeholder="Ex: Apto 101"
                        :disabled="processing"
                    />
                    <InputError :message="errors['endereco.complemento'] || errors.complemento" />
                </div>

                <!-- Bairro -->
                <div class="space-y-2 md:col-span-3">
                    <Label for="bairro">Bairro</Label>
                    <Input
                        v-if="form"
                        id="bairro"
                        name="endereco[bairro]"
                        v-model="form.endereco.bairro"  
                        placeholder="Ex: Centro"
                        :disabled="processing"
                    />
                    <Input
                        v-else
                        id="bairro"
                        name="bairro"
                        :value="modelValue.bairro"
                        @input="(e: Event) => handleInput('bairro', (e.target as HTMLInputElement).value)"
                        placeholder="Ex: Centro"
                        :disabled="processing"
                    />
                    <InputError :message="errors['endereco.bairro'] || errors.bairro" />
                </div>

                <!-- Estado -->
                <div class="space-y-2 md:col-span-3">
                    <Label for="estado_id">Estado</Label>
                    <select
                        id="estado_id"
                        v-model="selectedEstadoId"
                        :disabled="processing || loadingCep"
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors['endereco.estado_id'] || errors.estado_id }"
                    >
                        <option :value="null">Selecione um estado</option>
                        <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                            {{ estado.nome }} ({{ estado.uf }})
                        </option>
                    </select>
                    <InputError :message="errors['endereco.estado_id'] || errors.estado_id" />
                </div>

                <!-- Município -->
                <div class="space-y-2 md:col-span-3">
                    <Label for="municipio_id">Município</Label>
                    <select
                        v-if="form"
                        id="municipio_id"
                        name="endereco[municipio_id]"
                        v-model="form.endereco.municipio_id"
                        :disabled="processing || loadingCep || loadingMunicipios || !selectedEstadoId"
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors['endereco.municipio_id'] }"
                    >
                        <option :value="null">
                            {{ loadingMunicipios ? 'Carregando...' : (selectedEstadoId ? 'Selecione um município' : 'Selecione um estado primeiro') }}
                        </option>
                        <option v-for="municipio in municipios" :key="municipio.id" :value="municipio.id">
                            {{ municipio.nome }}
                        </option>
                    </select>
                    <select
                        v-else
                        id="municipio_id"
                        name="municipio_id"
                        v-model="selectedMunicipioId"
                        :disabled="processing || loadingCep || loadingMunicipios || !selectedEstadoId"
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{ 'border-red-500 focus-visible:ring-red-500': errors.municipio_id }"
                    >
                        <option :value="null">
                            {{ loadingMunicipios ? 'Carregando...' : (selectedEstadoId ? 'Selecione um município' : 'Selecione um estado primeiro') }}
                        </option>
                        <option v-for="municipio in municipios" :key="municipio.id" :value="municipio.id">
                            {{ municipio.nome }}
                        </option>
                    </select>
                    <InputError :message="errors['endereco.municipio_id'] || errors.municipio_id" />
                </div>
            </div>

            <!-- Linha 3: Referência -->
            <div class="space-y-2">
                <Label for="referencia">Referência</Label>
                <Input
                    v-if="form"
                    id="referencia"
                    name="endereco[referencia]"
                    v-model="form.endereco.referencia"
                    placeholder="Ex: Próximo ao mercado"
                    :disabled="processing"
                />
                <Input
                    v-else
                    id="referencia"
                    name="referencia"
                    :value="modelValue.referencia"
                    @input="(e: Event) => handleInput('referencia', (e.target as HTMLInputElement).value)"
                    placeholder="Ex: Próximo ao mercado"
                    :disabled="processing"
                />
                <InputError :message="errors['endereco.referencia'] || errors.referencia" />
            </div>
        </CardContent>
    </Card>
</template>

