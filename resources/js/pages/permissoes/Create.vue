<script setup lang="ts">
import PermissionController from '@/actions/App/Http/Controllers/PermissionController';
import { index as permissoesIndex } from '@/routes/permissoes';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import { Key } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Permissões', href: permissoesIndex().url },
    { title: 'Nova Permissão', href: PermissionController.create().url },
];

const form = useForm({
    name: '',
    guard_name: 'web',
});

const handleSubmit = () => {
    form.post(PermissionController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Permissão criada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao criar permissão', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Nova Permissão" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <Key class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Nova Permissão
                    </h1>
                </div>
                <p class="text-base text-muted-foreground">
                    Preencha as informações abaixo para criar uma nova permissão de acesso.
                </p>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle>Dados da Permissão</CardTitle>
                        <CardDescription>
                            Informe os dados básicos da permissão de acesso
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Nome da Permissão</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Ex: ver-usuarios, criar-produtos, etc."
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

                <!-- Actions -->
                <Card class="border-border shadow-sm">
                    <CardContent class="pt-6">
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:items-center">
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(permissoesIndex().url)"
                            >
                                Cancelar
                            </Button>
                            
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="!form.processing">
                                    Criar Permissão
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