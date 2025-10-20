<script setup lang="ts">
import UserController from '@/actions/App/Http/Controllers/UserController';
import { index as usersIndex } from '@/routes/users';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import UserForm from '@/components/users/UserForm.vue';
import FormActions from '@/components/users/FormActions.vue';
import { User as UserIcon } from 'lucide-vue-next';
import type { BreadcrumbItem, Empresa } from '@/types';

interface Role {
    id: number;
    name: string;
}

interface Props {
    empresas: Empresa[];
    roles: Role[];
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Usuários', href: usersIndex().url },
    { title: 'Novo Usuário', href: UserController.create().url },
];

const form = useForm({
    name: '',
    email: '',
    cpf: '',
    password: '',
    password_confirmation: '',
    empresa_id: null as number | null,
    tipo: '',
    ativo: true,
    lojas: [] as number[],
    roles: [] as number[],
});

const handleSubmit = () => {
    form.post(UserController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Usuário cadastrado com sucesso!');
        },
        onError: (errors) => {
            console.log('Errors:', errors);
            toast.error('Erro ao cadastrar usuário', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Novo Usuário" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <UserIcon class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Novo Usuário
                    </h1>
                </div>
                <p class="text-base text-muted-foreground">
                    Preencha as informações abaixo para cadastrar um novo usuário no sistema.
                </p>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <UserForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :isCreate="true"
                    :empresas="empresas"
                    :roles="roles"
                />
                
                <FormActions 
                    :processing="form.processing"
                    :isCreate="true"
                />
            </form>
        </div>
    </AppLayout>
</template>
