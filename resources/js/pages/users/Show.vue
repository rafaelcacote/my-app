<script setup lang="ts">
import UserController from '@/actions/App/Http/Controllers/UserController';
import { index as usersIndex, edit as usersEdit } from '@/routes/users';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { 
    User as UserIcon, 
    Mail, 
    Phone, 
    Building2, 
    Store, 
    Calendar,
    Shield,
    Edit,
    ArrowLeft
} from 'lucide-vue-next';
import type { BreadcrumbItem, User } from '@/types';

interface Props {
    user: User;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Usuários', href: usersIndex().url },
    { title: props.user.name, href: UserController.show(props.user.id).url },
];

const formatDate = (date: string | null) => {
    if (!date) return 'Não informado';
    return new Date(date).toLocaleDateString('pt-BR');
};

const formatCpf = (cpf: string | null) => {
    if (!cpf) return 'Não informado';
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
};
</script>

<template>
    <Head :title="`Usuário: ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <UserIcon class="h-8 w-8 text-primary" />
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-foreground">
                                {{ user.name }}
                            </h1>
                            <p class="text-base text-muted-foreground">
                                Detalhes do usuário
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link :href="usersIndex().url">
                            <Button variant="outline" class="gap-2">
                                <ArrowLeft class="h-4 w-4" />
                                Voltar
                            </Button>
                        </Link>
                        <Link :href="usersEdit(user.id).url">
                            <Button class="gap-2">
                                <Edit class="h-4 w-4" />
                                Editar
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- User Details -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <Card class="border-border shadow-sm">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <UserIcon class="h-5 w-5 text-primary" />
                                Informações Pessoais
                            </CardTitle>
                            <CardDescription>
                                Dados básicos do usuário
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">Nome Completo</label>
                                    <p class="text-sm font-medium">{{ user.name }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">Email</label>
                                    <div class="flex items-center gap-2">
                                        <Mail class="h-4 w-4 text-muted-foreground" />
                                        <p class="text-sm font-medium">{{ user.email }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">CPF</label>
                                    <p class="text-sm font-medium">{{ formatCpf(user.cpf) }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">Tipo</label>
                                    <div class="flex items-center gap-2">
                                        <Shield class="h-4 w-4 text-muted-foreground" />
                                        <Badge variant="secondary">{{ user.tipo || 'Não informado' }}</Badge>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Company Information -->
                    <Card v-if="user.empresa" class="border-border shadow-sm">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Building2 class="h-5 w-5 text-primary" />
                                Empresa
                            </CardTitle>
                            <CardDescription>
                                Empresa vinculada ao usuário
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">Razão Social</label>
                                    <p class="text-sm font-medium">{{ user.empresa.razao_social }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">Nome Fantasia</label>
                                    <p class="text-sm font-medium">{{ user.empresa.nome_fantasia || 'Não informado' }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">CNPJ</label>
                                    <p class="text-sm font-medium">{{ user.empresa.cnpj }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">Email</label>
                                    <div class="flex items-center gap-2">
                                        <Mail class="h-4 w-4 text-muted-foreground" />
                                        <p class="text-sm font-medium">{{ user.empresa.email }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Stores Access -->
                    <Card v-if="user.lojas && user.lojas.length > 0" class="border-border shadow-sm">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Store class="h-5 w-5 text-primary" />
                                Lojas de Acesso
                            </CardTitle>
                            <CardDescription>
                                Lojas que o usuário tem acesso
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-3 md:grid-cols-2">
                                <div
                                    v-for="loja in user.lojas"
                                    :key="loja.id"
                                    class="flex items-center gap-3 p-3 border rounded-md bg-muted/50"
                                >
                                    <Store class="h-4 w-4 text-muted-foreground" />
                                    <div class="flex-1">
                                        <div class="font-medium">{{ loja.nome }}</div>
                                        <div v-if="loja.cnpj" class="text-sm text-muted-foreground">
                                            CNPJ: {{ loja.cnpj }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status -->
                    <Card class="border-border shadow-sm">
                        <CardHeader>
                            <CardTitle>Status</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">Usuário Ativo</span>
                                <Badge :variant="user.ativo ? 'default' : 'secondary'">
                                    {{ user.ativo ? 'Ativo' : 'Inativo' }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Dates -->
                    <Card class="border-border shadow-sm">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Calendar class="h-5 w-5 text-primary" />
                                Datas
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Criado em</label>
                                <p class="text-sm font-medium">{{ formatDate(user.created_at) }}</p>
                            </div>
                            <Separator />
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Última atualização</label>
                                <p class="text-sm font-medium">{{ formatDate(user.updated_at) }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
