<script setup lang="ts">
import CategoriaController from '@/actions/App/Http/Controllers/CategoriaController';
import { index as categoriasIndex } from '@/routes/categorias';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, ArrowLeft, Tag } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Categoria {
    id: number;
    nome: string;
    descricao: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    categoria: Categoria;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Categorias', href: categoriasIndex().url },
    { title: props.categoria.nome, href: '#' },
];
</script>

<template>
    <Head :title="`Categoria: ${categoria.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Tag class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">{{ categoria.nome }}</h1>
                        <Badge
                            :variant="categoria.ativo ? 'default' : 'secondary'"
                            class="ml-2"
                        >
                            {{ categoria.ativo ? 'Ativo' : 'Inativo' }}
                        </Badge>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Detalhes da categoria
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="CategoriaController.edit(categoria).url">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Informações Básicas -->
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Tag class="h-5 w-5" />
                            Informações Básicas
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Nome:</span>
                            <p class="text-foreground">{{ categoria.nome }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Descrição:</span>
                            <p class="text-foreground">
                                {{ categoria.descricao || 'Sem descrição' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Status:</span>
                            <Badge
                                :variant="categoria.ativo ? 'default' : 'secondary'"
                                class="ml-2"
                            >
                                {{ categoria.ativo ? 'Ativo' : 'Inativo' }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Informações do Sistema -->
                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle>Informações do Sistema</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">ID:</span>
                            <p class="font-mono text-foreground">{{ categoria.id }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Criado em:</span>
                            <p class="text-foreground">
                                {{ new Date(categoria.created_at).toLocaleDateString('pt-BR') }} às 
                                {{ new Date(categoria.created_at).toLocaleTimeString('pt-BR') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Atualizado em:</span>
                            <p class="text-foreground">
                                {{ new Date(categoria.updated_at).toLocaleDateString('pt-BR') }} às 
                                {{ new Date(categoria.updated_at).toLocaleTimeString('pt-BR') }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Back Button -->
            <div class="flex justify-start">
                <Link :href="categoriasIndex().url">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Voltar para Categorias
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
