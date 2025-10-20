<script setup lang="ts">
import PermissionController from '@/actions/App/Http/Controllers/PermissionController';
import { index as permissoesIndex } from '@/routes/permissoes';
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import { Key } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Permission { 
    id: number; 
    name: string; 
    guard_name: string; 
    created_at: string; 
    updated_at: string 
}

const props = defineProps<{ permissao: Permission }>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Permissões', href: permissoesIndex().url },
    { title: `Editar ${props.permissao.name}`, href: PermissionController.edit(props.permissao).url },
];
</script>

<template>
    <Head :title="`Editar ${props.permissao.name}`" />
    
    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <Key class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Editar Permissão
                    </h1>
                </div>
                <p class="text-base text-muted-foreground">
                    Atualize as informações da permissão de acesso.
                </p>
            </div>

            <!-- Info Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ props.permissao.id }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Criado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(props.permissao.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="PermissionController.update.form(props.permissao)"
                enctype="multipart/form-data"
                :options="{ preserveScroll: true }"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle>Dados da Permissão</CardTitle>
                        <CardDescription>
                            Atualize os dados básicos da permissão de acesso
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Nome da Permissão</Label>
                            <Input
                                id="name"
                                name="name"
                                :value="props.permissao.name"
                                placeholder="Ex: ver-usuarios, criar-produtos, etc."
                                :class="{ 'border-red-500': errors.name }"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="guard_name">Guard</Label>
                            <Input
                                id="guard_name"
                                name="guard_name"
                                :value="props.permissao.guard_name"
                                placeholder="web"
                                :class="{ 'border-red-500': errors.guard_name }"
                            />
                            <InputError :message="errors.guard_name" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions -->
                <Card class="border-border shadow-sm">
                    <CardContent class="pt-6">
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:items-center">
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="processing"
                                @click="$inertia.visit(permissoesIndex().url)"
                            >
                                Cancelar
                            </Button>
                            
                            <Transition>
                                <p v-show="recentlySuccessful" class="text-sm font-medium text-green-600">
                                    ✓ Salvo com sucesso
                                </p>
                            </Transition>

                            <Button type="submit" :disabled="processing">
                                <span v-if="!processing">
                                    Salvar Alterações
                                </span>
                                <span v-else>
                                    Salvando...
                                </span>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </Form>
        </div>
    </AppLayout>
</template>