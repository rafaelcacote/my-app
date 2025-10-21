<script setup lang="ts">
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="space-y-2">
                <Skeleton class="h-8 w-48" />
                <Skeleton class="h-4 w-64" />
            </div>
            <Skeleton class="h-9 w-32" />
        </div>

        <!-- Métricas Principais -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="i in 4" :key="i">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Skeleton class="h-4 w-24" />
                    <Skeleton class="h-4 w-4" />
                </CardHeader>
                <CardContent>
                    <Skeleton class="h-8 w-20 mb-2" />
                    <Skeleton class="h-3 w-32" />
                </CardContent>
            </Card>
        </div>

        <!-- Segunda linha de métricas -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="i in 3" :key="i">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <Skeleton class="h-4 w-28" />
                    <Skeleton class="h-4 w-4" />
                </CardHeader>
                <CardContent>
                    <Skeleton class="h-8 w-16 mb-2" />
                    <Skeleton class="h-3 w-24" />
                </CardContent>
            </Card>
        </div>

        <!-- Gráficos -->
        <div class="grid gap-6 md:grid-cols-2">
            <Card v-for="i in 4" :key="i">
                <CardHeader>
                    <Skeleton class="h-6 w-40 mb-2" />
                    <Skeleton class="h-4 w-56" />
                </CardHeader>
                <CardContent>
                    <Skeleton class="h-80 w-full" />
                </CardContent>
            </Card>
        </div>

        <!-- Tabelas -->
        <div class="grid gap-6 md:grid-cols-2">
            <Card v-for="i in 2" :key="i">
                <CardHeader>
                    <Skeleton class="h-6 w-48 mb-2" />
                    <Skeleton class="h-4 w-64" />
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div v-for="j in 5" :key="j" class="flex items-center justify-between">
                            <Skeleton class="h-4 w-32" />
                            <Skeleton class="h-4 w-20" />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
