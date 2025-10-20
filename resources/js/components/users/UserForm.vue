<script setup lang="ts">
import { ref, watch, computed, nextTick, onMounted } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Building2, Store, Shield } from 'lucide-vue-next';
import type { User, Empresa, Loja } from '@/types';

interface Role {
    id: number;
    name: string;
}

interface Props {
    user?: User;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
    empresas: Empresa[];
    roles: Role[];
}

const props = withDefaults(defineProps<Props>(), {
    user: undefined,
    isCreate: false
});

// Estado local para campos
const selectedEmpresaId = ref<string>('');
const empresaSearch = ref('');
const lojasDisponiveis = ref<Loja[]>([]);
const lojasSelecionadas = ref<number[]>([]);
const perfisSelecionados = ref<number[]>([]);
const ativo = ref(props.isCreate ? true : !!props.user?.ativo);
const selectedIndex = ref(-1);

// Função para aplicar máscara do CPF
const applyCpfMask = (value: string): string => {
    const numbers = value.replace(/\D/g, '');
    return numbers.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
};

// Computed para CPF com máscara
const cpfMasked = computed(() => {
    const cpf = props.form?.cpf || props.user?.cpf || '';
    return applyCpfMask(cpf);
});

// Função para atualizar CPF
const updateCpf = (value: string | number) => {
    const numbers = String(value).replace(/\D/g, '');
    if (props.form) {
        props.form.cpf = numbers;
    }
};

// Função para buscar lojas quando uma empresa é selecionada
const buscarLojas = async (empresaId: number) => {
    try {
        const response = await fetch(`/api/lojas-by-empresa?empresa_id=${empresaId}`);
        const lojas = await response.json();
        lojasDisponiveis.value = lojas;
    } catch (error) {
        console.error('Erro ao buscar lojas:', error);
        lojasDisponiveis.value = [];
    }
};

// Função para selecionar empresa
const selecionarEmpresa = (empresaId: string, isInitialization = false) => {
    const previousEmpresaId = selectedEmpresaId.value;
    selectedEmpresaId.value = empresaId;
    empresaSearch.value = ''; // Limpar busca após seleção
    selectedIndex.value = -1; // Resetar índice selecionado
    
    // Atualizar form se existir
    if (props.form) {
        props.form.empresa_id = empresaId ? parseInt(empresaId) : null;
    }
    
    // Buscar lojas da empresa
    if (empresaId) {
        buscarLojas(parseInt(empresaId));
    } else {
        lojasDisponiveis.value = [];
    }
    
    // Limpar lojas selecionadas apenas quando trocar de empresa (não na inicialização)
    if (!isInitialization && previousEmpresaId !== empresaId) {
        lojasSelecionadas.value = [];
        if (props.form) {
            props.form.lojas = [];
        }
    }
};

// Atualiza os campos quando o usuário muda
watch(() => props.user, (newUser) => {
    if (newUser) {
        // Inicializar empresa com flag de inicialização para não limpar lojas
        if (newUser.empresa) {
            selecionarEmpresa(newUser.empresa.id.toString(), true);
        }
        
        lojasSelecionadas.value = newUser.lojas?.map(loja => loja.id) || [];
        perfisSelecionados.value = newUser.roles?.map(role => role.id) || [];
        ativo.value = !!newUser.ativo;
        
        // Sincronizar lojas e perfis com form
        if (props.form) {
            props.form.lojas = [...lojasSelecionadas.value];
            props.form.roles = [...perfisSelecionados.value];
        }
    }
}, { deep: true, immediate: true });

// Computed para filtrar empresas com base na busca
const empresasFiltradas = computed(() => {
    if (!empresaSearch.value) return props.empresas;
    return props.empresas.filter(empresa => {
        const razaoSocial = empresa.razao_social?.toLowerCase() || '';
        const nomeFantasia = empresa.nome_fantasia?.toLowerCase() || '';
        const searchTerm = empresaSearch.value.toLowerCase();
        
        return razaoSocial.includes(searchTerm) || nomeFantasia.includes(searchTerm);
    });
});

// Computed para empresa selecionada
const empresaSelecionada = computed(() => {
    if (!selectedEmpresaId.value) return null;
    return props.empresas.find(e => e.id.toString() === selectedEmpresaId.value);
});

// Função para navegação por teclado
const handleKeyDown = (event: KeyboardEvent) => {
    if (!empresaSearch.value || empresasFiltradas.value.length === 0) return;
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            selectedIndex.value = Math.min(selectedIndex.value + 1, empresasFiltradas.value.length - 1);
            scrollToSelected();
            break;
        case 'ArrowUp':
            event.preventDefault();
            selectedIndex.value = Math.max(selectedIndex.value - 1, -1);
            scrollToSelected();
            break;
        case 'Enter':
            event.preventDefault();
            if (selectedIndex.value >= 0 && selectedIndex.value < empresasFiltradas.value.length) {
                const empresa = empresasFiltradas.value[selectedIndex.value];
                selecionarEmpresa(empresa.id.toString());
            }
            break;
        case 'Escape':
            empresaSearch.value = '';
            selectedIndex.value = -1;
            break;
    }
};

// Função para scroll automático para o item selecionado
const scrollToSelected = () => {
    if (selectedIndex.value >= 0) {
        nextTick(() => {
            const container = document.querySelector('.max-h-48.overflow-y-auto');
            const selectedItem = container?.querySelector(`[data-index="${selectedIndex.value}"]`);
            if (selectedItem && container) {
                selectedItem.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'nearest' 
                });
            }
        });
    }
};

// Função para alternar seleção de loja
const alternarLoja = (lojaId: number) => {
    const index = lojasSelecionadas.value.indexOf(lojaId);
    if (index > -1) {
        lojasSelecionadas.value.splice(index, 1);
    } else {
        lojasSelecionadas.value.push(lojaId);
    }
    
    // Atualizar form se existir
    if (props.form) {
        props.form.lojas = [...lojasSelecionadas.value];
    }
};

// Função para alternar seleção de perfil
const alternarPerfil = (perfilId: number) => {
    const index = perfisSelecionados.value.indexOf(perfilId);
    if (index > -1) {
        perfisSelecionados.value.splice(index, 1);
    } else {
        perfisSelecionados.value.push(perfilId);
    }
    
    // Atualizar form se existir
    if (props.form) {
        props.form.roles = [...perfisSelecionados.value];
    }
};

// Watch para resetar índice quando busca mudar
watch(empresaSearch, () => {
    selectedIndex.value = -1;
});

// Watch para atualizar o form quando campos mudam
watch([ativo], () => {
    if (props.form) {
        props.form.ativo = ativo.value;
    }
});

// Watch para sincronizar lojasSelecionadas com form.lojas
watch(lojasSelecionadas, (newLojas) => {
    if (props.form) {
        props.form.lojas = [...newLojas];
    }
}, { deep: true });

// Watch para sincronizar perfisSelecionados com form.roles
watch(perfisSelecionados, (newPerfis) => {
    if (props.form) {
        props.form.roles = [...newPerfis];
    }
}, { deep: true });

// Inicializar dados quando o componente for montado
onMounted(() => {
    if (props.user) {
        // Inicializar empresa com flag de inicialização para não limpar lojas
        if (props.user.empresa) {
            selecionarEmpresa(props.user.empresa.id.toString(), true);
        }
        
        lojasSelecionadas.value = props.user.lojas?.map(loja => loja.id) || [];
        perfisSelecionados.value = props.user.roles?.map(role => role.id) || [];
        ativo.value = !!props.user.ativo;
        
        // Sincronizar lojas e perfis com form
        if (props.form) {
            props.form.lojas = [...lojasSelecionadas.value];
            props.form.roles = [...perfisSelecionados.value];
        }
    }
    
    // Garantir que form.lojas e form.roles estão inicializados mesmo sem user
    if (props.form && !props.form.lojas) {
        props.form.lojas = [];
    }
    if (props.form && !props.form.roles) {
        props.form.roles = [];
    }
});
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle>Dados do Usuário</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados do usuário' : 'Atualize os dados do usuário' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
        <!-- Linha 1: Nome, Email, CPF, Tipo, Usuário Ativo -->
        <div class="grid gap-4 md:grid-cols-5">
            <!-- Nome -->
            <div class="space-y-2">
                <Label for="name">Nome *</Label>
                <Input
                    id="name"
                    :model-value="form?.name || user?.name || ''"
                    @update:model-value="form && (form.name = $event)"
                    type="text"
                    placeholder="Nome completo do usuário"
                    :class="{ 'border-red-500': errors.name }"
                />
                <InputError :message="errors.name" />
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <Label for="email">Email *</Label>
                <Input
                    id="email"
                    :model-value="form?.email || user?.email || ''"
                    @update:model-value="form && (form.email = $event)"
                    type="email"
                    placeholder="email@exemplo.com"
                    :class="{ 'border-red-500': errors.email }"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- CPF -->
            <div class="space-y-2">
                <Label for="cpf">CPF</Label>
                <Input
                    id="cpf"
                    :model-value="cpfMasked"
                    @update:model-value="updateCpf"
                    type="text"
                    placeholder="000.000.000-00"
                    maxlength="14"
                    :class="{ 'border-red-500': errors.cpf }"
                />
                <InputError :message="errors.cpf" />
            </div>

            <!-- Tipo -->
            <div class="space-y-2">
                <Label for="tipo">Tipo</Label>
                <select
                    id="tipo"
                    :value="form?.tipo || user?.tipo || ''"
                    @change="form && (form.tipo = ($event.target as HTMLSelectElement)?.value)"
                    :class="[
                        'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                        { 'border-red-500': errors.tipo }
                    ]"
                >
                    <option value="">Selecione um tipo</option>
                    <option value="proprietario">Proprietário</option>
                    <option value="gerente">Gerente</option>
                    <option value="vendedor">Vendedor</option>
                    <option value="super_admin">Super Admin</option>
                </select>
                <InputError :message="errors.tipo" />
            </div>

            <!-- Status -->
            <div class="space-y-2">
                <Label for="ativo" class="block text-sm font-medium mb-2">Status</Label>
                <div class="flex items-center space-x-2 h-10">
                    <Switch
                        id="ativo"
                        :model-value="ativo"
                        @update:model-value="ativo = $event"
                        :disabled="processing"
                    />
                    <Label for="ativo" class="text-sm">Ativo</Label>
                </div>
            </div>
        </div>

        <!-- Linha 2: Senha e Confirmação de Senha -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Senha -->
            <div class="space-y-2">
                <Label for="password">{{ isCreate ? 'Senha *' : 'Nova Senha' }}</Label>
                <Input
                    id="password"
                    :model-value="form?.password || ''"
                    @update:model-value="form && (form.password = $event)"
                    type="password"
                    :placeholder="isCreate ? 'Mínimo 8 caracteres' : 'Deixe em branco para manter a atual'"
                    :class="{ 'border-red-500': errors.password }"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Confirmação de Senha -->
            <div v-if="isCreate || form?.password" class="space-y-2">
                <Label for="password_confirmation">Confirmar Senha *</Label>
                <Input
                    id="password_confirmation"
                    :model-value="form?.password_confirmation || ''"
                    @update:model-value="form && (form.password_confirmation = $event)"
                    type="password"
                    placeholder="Digite a senha novamente"
                    :class="{ 'border-red-500': errors.password_confirmation }"
                />
                <InputError :message="errors.password_confirmation" />
            </div>
        </div>

         <!-- Linha 3: Empresa -->
         <div class="space-y-2">
             <Label>Empresa</Label>
             <div class="relative">
                 <Input
                     v-model="empresaSearch"
                     placeholder="Digite para buscar empresa..."
                     class="pr-10"
                     @keydown="handleKeyDown"
                 />
                 <Building2 class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
             </div>
             
             <!-- Lista de empresas filtradas -->
             <div v-if="empresaSearch && empresasFiltradas.length > 0" class="max-h-48 overflow-y-auto border rounded-md bg-background">
                 <div class="px-3 py-2 text-xs text-muted-foreground border-b bg-muted/50">
                     Use ↑↓ para navegar, Enter para selecionar, Esc para cancelar
                 </div>
                 <div
                     v-for="(empresa, index) in empresasFiltradas"
                     :key="empresa.id"
                     :data-index="index"
                     :class="[
                         'px-3 py-2 cursor-pointer border-b last:border-b-0',
                         index === selectedIndex ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'
                     ]"
                     @click="selecionarEmpresa(empresa.id.toString())"
                 >
                     <div class="font-medium">{{ empresa.razao_social }}</div>
                     <div v-if="empresa.nome_fantasia" class="text-sm opacity-75">{{ empresa.nome_fantasia }}</div>
                 </div>
             </div>
             
             <!-- Empresa selecionada -->
             <div v-if="selectedEmpresaId && !empresaSearch" class="flex items-center justify-between p-3 border rounded-md bg-muted/50">
                 <div class="flex items-center gap-2">
                     <Building2 class="h-4 w-4 text-muted-foreground" />
                     <div>
                         <div class="font-medium">{{ empresaSelecionada?.razao_social }}</div>
                         <div v-if="empresaSelecionada?.nome_fantasia" class="text-sm text-muted-foreground">{{ empresaSelecionada?.nome_fantasia }}</div>
                     </div>
                 </div>
                 <Button
                     type="button"
                     variant="outline"
                     size="sm"
                     @click="selecionarEmpresa('')"
                 >
                     Alterar
                 </Button>
             </div>
             
             <InputError :message="errors.empresa_id" />
         </div>

         <!-- Lojas (apenas se empresa selecionada) -->
         <div v-if="selectedEmpresaId && lojasDisponiveis.length > 0" class="space-y-2">
             <Label>Lojas de Acesso</Label>
             <div class="text-sm text-muted-foreground mb-3">
                 Selecione as lojas que o usuário terá acesso:
             </div>
             <div class="grid gap-3 md:grid-cols-2">
                 <div
                    v-for="loja in lojasDisponiveis"
                    :key="loja.id"
                    class="flex items-center space-x-3 p-3 border rounded-md hover:bg-muted/50 cursor-pointer"
                    @click="alternarLoja(loja.id)"
                >
                    <Checkbox
                        :id="`loja-${loja.id}`"
                        :model-value="lojasSelecionadas.includes(loja.id)"
                        @click.stop
                    />
                     <div class="flex items-center gap-2 flex-1">
                         <Store class="h-4 w-4 text-muted-foreground" />
                         <div>
                             <div class="font-medium">{{ loja.nome }}</div>
                             <div v-if="loja.cnpj" class="text-sm text-muted-foreground">CNPJ: {{ loja.cnpj }}</div>
                         </div>
                     </div>
                 </div>
             </div>
             <InputError :message="errors.lojas" />
         </div>
         
         <!-- Mensagem quando não há lojas -->
         <div v-if="selectedEmpresaId && lojasDisponiveis.length === 0" class="text-sm text-muted-foreground p-3 border rounded-md bg-muted/50">
             <Store class="h-4 w-4 inline mr-2" />
             Esta empresa não possui lojas cadastradas.
         </div>
         
         <!-- Perfis -->
         <div v-if="roles && roles.length > 0" class="space-y-2">
             <Label>Perfis de Acesso</Label>
             <div class="text-sm text-muted-foreground mb-3">
                 Selecione os perfis que o usuário terá acesso:
             </div>
             <div class="grid gap-3 md:grid-cols-2">
                 <div
                    v-for="role in roles"
                    :key="role.id"
                    class="flex items-center space-x-3 p-3 border rounded-md hover:bg-muted/50 cursor-pointer"
                    @click="alternarPerfil(role.id)"
                >
                    <Checkbox
                        :id="`role-${role.id}`"
                        :model-value="perfisSelecionados.includes(role.id)"
                        @click.stop
                    />
                     <div class="flex items-center gap-2 flex-1">
                         <Shield class="h-4 w-4 text-muted-foreground" />
                         <div>
                             <div class="font-medium">{{ role.name }}</div>
                         </div>
                     </div>
                 </div>
             </div>
             <InputError :message="errors.roles" />
         </div>
         
         <!-- Mensagem quando não há perfis -->
         <div v-if="!roles || roles.length === 0" class="text-sm text-muted-foreground p-3 border rounded-md bg-muted/50">
             <Shield class="h-4 w-4 inline mr-2" />
             Nenhum perfil cadastrado. Crie perfis primeiro para poder atribuí-los aos usuários.
         </div>
    </CardContent>
    </Card>
</template>
