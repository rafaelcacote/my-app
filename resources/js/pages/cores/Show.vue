<script setup lang="ts">
import CorController from '@/actions/App/Http/Controllers/CorController';
import { index as coresIndex } from '@/routes/cores';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, ArrowLeft, Palette } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Cor {
    id: number;
    nome: string;
    codigo_hex: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    cor: Cor;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Cores', href: coresIndex().url },
    { title: props.cor.nome, href: '#' },
];
</script>

<template>
    <Head :title="`Cor: ${cor.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <Palette class="h-8 w-8 text-primary" />
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">{{ cor.nome }}</h1>
                        <div 
                            class="ml-2 h-6 w-6 rounded-full border-2 border-border"
                            :style="{ backgroundColor: cor.codigo_hex }"
                        />
                    </div>
                    <p class="text-base text-muted-foreground">
                        Detalhes da cor
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="CorController.edit(cor).url">
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
                            <Palette class="h-5 w-5" />
                            Informações Básicas
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Nome:</span>
                            <p class="text-foreground">{{ cor.nome }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Código HEX:</span>
                            <div class="flex items-center gap-3">
                                <p class="font-mono text-foreground">{{ cor.codigo_hex }}</p>
                                <div 
                                    class="h-6 w-6 rounded border-2 border-border"
                                    :style="{ backgroundColor: cor.codigo_hex }"
                                />
                            </div>
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
                            <p class="font-mono text-foreground">{{ cor.id }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Criado em:</span>
                            <p class="text-foreground">
                                {{ new Date(cor.created_at).toLocaleDateString('pt-BR') }} às 
                                {{ new Date(cor.created_at).toLocaleTimeString('pt-BR') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-muted-foreground">Atualizado em:</span>
                            <p class="text-foreground">
                                {{ new Date(cor.updated_at).toLocaleDateString('pt-BR') }} às 
                                {{ new Date(cor.updated_at).toLocaleTimeString('pt-BR') }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Back Button -->
            <div class="flex justify-start">
                <Link :href="coresIndex().url">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Voltar para Cores
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
