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
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import InputError from '@/components/InputError.vue';
import { Shield, ChevronDown, ChevronRight, Key } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { ref } from 'vue';

interface Permission {
    id: number;
    name: string;
    guard_name: string;
}

interface PermissionGroup {
    name: string;
    permissions: Permission[];
}

interface Props {
    permissionsGrouped: PermissionGroup[];
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Perfis', href: perfisIndex().url },
    { title: 'Novo Perfil', href: RoleController.create().url },
];

const form = useForm({
    name: '',
    guard_name: 'web',
    permissions: [] as number[],
});

// Estado dos módulos expandidos
const expandedModules = ref<Record<string, boolean>>({});

const toggleModule = (moduleName: string) => {
    expandedModules.value[moduleName] = !expandedModules.value[moduleName];
};

const togglePermission = (permissionId: number) => {
    const index = form.permissions.indexOf(permissionId);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permissionId);
    }
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
    form.post(RoleController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Perfil criado com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao criar perfil', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Novo Perfil" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <Shield class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Novo Perfil
                    </h1>
                </div>
                <p class="text-base text-muted-foreground">
                    Preencha as informações abaixo para criar um novo perfil de acesso.
                </p>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle>Dados do Perfil</CardTitle>
                        <CardDescription>
                            Informe os dados básicos do perfil de acesso
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
                            Selecione as permissões que este perfil terá acesso
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="!props.permissionsGrouped || props.permissionsGrouped.length === 0" class="text-center py-8 text-muted-foreground">
                            <Key class="h-12 w-12 mx-auto mb-4 opacity-50" />
                            <p>Nenhuma permissão encontrada.</p>
                            <p class="text-sm">Crie algumas permissões primeiro para poder atribuí-las aos perfis.</p>
                        </div>
                        
                        <div v-else class="space-y-3">
                            <div v-for="module in props.permissionsGrouped" :key="module.name" class="border rounded-lg">
                                <Collapsible :open="expandedModules[module.name]">
                                    <CollapsibleTrigger 
                                        @click="toggleModule(module.name)"
                                        class="flex w-full items-center justify-between p-4 hover:bg-muted/50 transition-colors"
                                    >
                                        <div class="flex items-center gap-3">
                                            <Checkbox
                                                :checked="module.permissions.every(p => form.permissions.includes(p.id))"
                                                :indeterminate="module.permissions.some(p => form.permissions.includes(p.id)) && !module.permissions.every(p => form.permissions.includes(p.id))"
                                                @click.stop="toggleAllPermissionsInModule(module)"
                                            />
                                            <span class="font-medium">{{ module.name }}</span>
                                            <span class="text-sm text-muted-foreground">
                                                ({{ module.permissions.length }} permissões)
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-muted-foreground">
                                                {{ module.permissions.filter(p => form.permissions.includes(p.id)).length }}/{{ module.permissions.length }}
                                            </span>
                                            <ChevronDown 
                                                v-if="expandedModules[module.name]" 
                                                class="h-4 w-4 text-muted-foreground" 
                                            />
                                            <ChevronRight 
                                                v-else 
                                                class="h-4 w-4 text-muted-foreground" 
                                            />
                                        </div>
                                    </CollapsibleTrigger>
                                    
                                    <CollapsibleContent class="px-4 pb-4">
                                        <div class="grid gap-2 pl-6">
                                            <div 
                                                v-for="permission in module.permissions" 
                                                :key="permission.id"
                                                class="flex items-center gap-3 py-2 px-3 rounded-md hover:bg-muted/30 transition-colors"
                                            >
                                                <Checkbox
                                                    :id="`permission-${permission.id}`"
                                                    :checked="form.permissions.includes(permission.id)"
                                                    @update:checked="togglePermission(permission.id)"
                                                />
                                                <Label 
                                                    :for="`permission-${permission.id}`" 
                                                    class="flex-1 cursor-pointer text-sm"
                                                >
                                                    {{ permission.name }}
                                                </Label>
                                                <span class="text-xs text-muted-foreground">
                                                    {{ permission.guard_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </CollapsibleContent>
                                </Collapsible>
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
                                    Criar Perfil
                                </span>
                                <span v-else>
                                    Criando...
                                </span>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>


