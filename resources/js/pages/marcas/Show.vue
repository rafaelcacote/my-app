<script setup lang="ts">
import MarcaController from '@/actions/App/Http/Controllers/MarcaController';
import { index as marcasIndex } from '@/routes/marcas';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, ArrowLeft, Tag } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Marca {
    id: number;
    nome: string;
    descricao: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    marca: Marca;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Marcas', href: marcasIndex().url },
    { title: props.marca.nome, href: '#' },
];
</script>

<template>
    <Head :title="`Marca: ${marca.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Tag class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">{{ marca.nome }}</h1>
                        <Badge
                            :variant="marca.ativo ? 'default' : 'secondary'"
                            class="ml-2"
                        >
                            {{ marca.ativo ? 'Ativo' : 'Inativo' }}
                        </Badge>
                    </div>
                    <p class="text-base text-muted-foreground">
                        Detalhes da marca
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="MarcaController.edit(marca).url">
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
                            <p class="text-foreground">{{ marca.nome }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Descrição:</span>
                            <p class="text-foreground">
                                {{ marca.descricao || 'Sem descrição' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Status:</span>
                            <Badge
                                :variant="marca.ativo ? 'default' : 'secondary'"
                                class="ml-2"
                            >
                                {{ marca.ativo ? 'Ativo' : 'Inativo' }}
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
                            <p class="font-mono text-foreground">{{ marca.id }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Criado em:</span>
                            <p class="text-foreground">
                                {{ new Date(marca.created_at).toLocaleDateString('pt-BR') }} às 
                                {{ new Date(marca.created_at).toLocaleTimeString('pt-BR') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Atualizado em:</span>
                            <p class="text-foreground">
                                {{ new Date(marca.updated_at).toLocaleDateString('pt-BR') }} às 
                                {{ new Date(marca.updated_at).toLocaleTimeString('pt-BR') }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Back Button -->
            <div class="flex justify-start">
                <Link :href="marcasIndex().url">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Voltar para Marcas
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
