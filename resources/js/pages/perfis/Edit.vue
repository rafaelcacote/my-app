<script setup lang="ts">
import RoleController from '@/actions/App/Http/Controllers/RoleController';
import { index as perfisIndex } from '@/routes/perfis';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { Shield, Key } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { onMounted } from 'vue';

interface Permission {
    id: number;
    name: string;
    guard_name: string;
}

interface PermissionGroup {
    name: string;
    permissions: Permission[];
}

interface Role { 
    id: number; 
    name: string; 
    guard_name: string; 
    created_at: string; 
    updated_at: string 
}

interface Props {
    perfil: Role;
    permissionsGrouped: PermissionGroup[];
    rolePermissions: number[];
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Perfis', href: perfisIndex().url },
    { title: `Editar ${props.perfil.name}`, href: RoleController.edit(props.perfil).url },
];

// Form para gerenciar as permissões
const form = useForm({
    name: props.perfil.name,
    guard_name: props.perfil.guard_name,
    permissions: [...props.rolePermissions], // Copia as permissões existentes
});

// Debug: verificar os dados recebidos
onMounted(() => {
    console.log('✅ Permissões carregadas:', props.rolePermissions.length, 'ids');
    console.log('✅ Form inicializado com:', form.permissions.length, 'permissões');
});

const togglePermission = (permissionId: number) => {
    const index = form.permissions.indexOf(permissionId);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permissionId);
    }
};

const isPermissionChecked = (permissionId: number): boolean => {
    return form.permissions.some(id => Number(id) === Number(permissionId));
};

const areAllPermissionsInModuleChecked = (module: PermissionGroup): boolean => {
    return module.permissions.every(p => isPermissionChecked(p.id));
};

const areSomePermissionsInModuleChecked = (module: PermissionGroup): boolean => {
    return module.permissions.some(p => isPermissionChecked(p.id));
};

const getCheckedPermissionsCountInModule = (module: PermissionGroup): number => {
    return module.permissions.filter(p => isPermissionChecked(p.id)).length;
};

const toggleAllPermissionsInModule = (module: PermissionGroup) => {
    const modulePermissionIds = module.permissions.map(p => p.id);
    const allSelected = modulePermissionIds.every(id => form.permissions.includes(id));
    
    if (allSelected) {
        // Remove todas as permissões do módulo
        form.permissions = form.permissions.filter(id => !modulePermissionIds.includes(id));
    } else {
        // Adiciona todas as permissões do módulo
        modulePermissionIds.forEach(id => {
            if (!form.permissions.includes(id)) {
                form.permissions.push(id);
            }
        });
    }
};

const handleSubmit = () => {
    form.put(RoleController.update(props.perfil).url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Perfil atualizado com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao atualizar perfil', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head :title="`Editar ${props.perfil.name}`" />
    
    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <Shield class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Editar Perfil
                    </h1>
                </div>
                <p class="text-base text-muted-foreground">
                    Atualize as informações do perfil de acesso.
                </p>
            </div>

            <!-- Info Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ props.perfil.id }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Criado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(props.perfil.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle>Dados do Perfil</CardTitle>
                        <CardDescription>
                            Atualize os dados básicos do perfil de acesso
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Nome do Perfil</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Ex: Administrador, Vendedor, etc."
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="guard_name">Guard</Label>
                            <Input
                                id="guard_name"
                                v-model="form.guard_name"
                                placeholder="web"
                                :class="{ 'border-red-500': form.errors.guard_name }"
                            />
                            <InputError :message="form.errors.guard_name" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Permissões Card -->
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Key class="h-5 w-5 text-primary" />
                            Permissões do Perfil
                        </CardTitle>
                        <CardDescription>
                            Gerencie as permissões atribuídas a este perfil
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="!props.permissionsGrouped || props.permissionsGrouped.length === 0" class="text-center py-8 text-muted-foreground">
                            <Key class="h-12 w-12 mx-auto mb-4 opacity-50" />
                            <p>Nenhuma permissão encontrada.</p>
                            <p class="text-sm">Crie algumas permissões primeiro para poder atribuí-las aos perfis.</p>
                        </div>
                        
                        <div v-else class="space-y-4">
                            <div v-for="module in props.permissionsGrouped" :key="module.name" class="border rounded-lg overflow-hidden">
                                <!-- Header do módulo× -->
                                <div class="bg-muted/50 px-4 py-3 border-b flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <Checkbox
                                            :model-value="areAllPermissionsInModuleChecked(module)"
                                            :indeterminate="areSomePermissionsInModuleChecked(module) && !areAllPermissionsInModuleChecked(module)"
                                            @click.prevent.stop="toggleAllPermissionsInModule(module)"
                                        />
                                        <div>
                                            <span class="font-semibold text-sm">{{ module.name }}</span>
                                            <span class="text-xs text-muted-foreground ml-2">
                                                ({{ module.permissions.length }} permissões)
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-muted-foreground bg-background px-2 py-1 rounded">
                                        {{ getCheckedPermissionsCountInModule(module) }}/{{ module.permissions.length }}
                                    </span>
                                </div>
                                
                                <!-- Lista de permissões em grid -->
                                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <div 
                                        v-for="permission in module.permissions" 
                                        :key="permission.id"
                                        class="flex items-center gap-3 py-2 px-3 rounded-md border hover:bg-muted/50 transition-colors"
                                    >
                                        <Checkbox
                                            :id="`permission-${permission.id}`"
                                            :model-value="isPermissionChecked(permission.id)"
                                            @click.prevent.stop="togglePermission(permission.id)"
                                        />
                                        <Label 
                                            :for="`permission-${permission.id}`" 
                                            class="flex-1 cursor-pointer text-sm truncate"
                                            :title="permission.name"
                                        >
                                            {{ permission.name }}
                                        </Label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <InputError :message="form.errors.permissions" />
                    </CardContent>
                </Card>

                <!-- Actions -->
                <Card class="border-border shadow-sm">
                    <CardContent class="pt-6">
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:items-center">
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(perfisIndex().url)"
                            >
                                Cancelar
                            </Button>
                            
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="!form.processing">
                                    Salvar Alterações
                                </span>
                                <span v-else>
                                    Salvando...
                                </span>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>