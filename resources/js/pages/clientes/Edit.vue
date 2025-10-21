<script setup lang="ts">
import ClienteController from '@/actions/App/Http/Controllers/ClienteController';
import { index as clientesIndex } from '@/routes/clientes';
import { Form, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import ClienteForm from '@/components/clientes/ClienteForm.vue';
import FormActions from '@/components/clientes/FormActions.vue';
import { computed } from 'vue';

interface Cliente {
    id: number;
    uuid: string;
    nome: string;
    tipo: string;
    cpf_cnpj: string | null;
    email: string | null;
    telefone: string | null;
    data_nascimento: string | null;
    cep: string | null;
    logradouro: string | null;
    numero: string | null;
    complemento: string | null;
    bairro: string | null;
    cidade: string | null;
    estado: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    cliente: Cliente;
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'Clientes', href: clientesIndex().url },
    { label: `Editar ${props.cliente.nome}`, href: null },
]);
</script>

<template>
    <Head :title="`Editar ${cliente.nome}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (UUID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">UUID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ cliente.uuid }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(cliente.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="ClienteController.update.form(cliente)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Cliente atualizado com sucesso!')"
                @error="toast.error('Erro ao atualizar cliente')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <ClienteForm 
                    :cliente="cliente"
                    :errors="errors"
                    :processing="processing"
                />
                
                <FormActions 
                    :processing="processing"
                    :recentlySuccessful="recentlySuccessful"
                />
            </Form>
        </div>
    </AppLayout>
</template>

