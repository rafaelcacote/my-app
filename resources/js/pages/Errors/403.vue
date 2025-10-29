<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertTriangle, Home, LogIn } from 'lucide-vue-next';

const props = defineProps<{
    status?: number;
    message?: string;
}>();

const goToLogin = () => {
    router.post('/logout', {}, {
        onFinish: () => {
            router.visit('/login');
        }
    });
};

const goHome = () => {
    router.visit('/');
};
</script>

<template>
    <Head title="Acesso Negado" />

    <div class="flex min-h-screen items-center justify-center bg-background p-4">
        <Card class="w-full max-w-md">
            <CardHeader class="text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-destructive/10">
                    <AlertTriangle class="h-8 w-8 text-destructive" />
                </div>
                <CardTitle class="text-2xl">Acesso Negado</CardTitle>
                <CardDescription class="mt-2">
                    {{ message || 'Você não tem permissão para acessar esta página.' }}
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="rounded-lg bg-muted p-4 text-sm">
                    <p class="font-medium mb-2">Possíveis causas:</p>
                    <ul class="list-disc list-inside space-y-1 text-muted-foreground">
                        <li>Seu perfil não tem permissão para acessar esta área</li>
                        <li>As permissões não foram configuradas corretamente</li>
                        <li>Sua sessão pode ter expirado</li>
                    </ul>
                </div>
            </CardContent>
            <CardFooter class="flex gap-2">
                <Button 
                    variant="default" 
                    class="flex-1"
                    @click="goToLogin"
                >
                    <LogIn class="mr-2 h-4 w-4" />
                    Voltar ao Login
                </Button>
                <Button 
                    variant="outline" 
                    class="flex-1"
                    @click="goHome"
                >
                    <Home class="mr-2 h-4 w-4" />
                    Página Inicial
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>

