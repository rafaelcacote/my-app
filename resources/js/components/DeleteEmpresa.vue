<script setup lang="ts">
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

interface Empresa {
    id: number;
    razao_social: string;
}

interface Props {
    empresa: Empresa;
}

defineProps<Props>();
const toast = useToast();
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall
            title="Excluir empresa"
            description="Exclua esta empresa permanentemente do sistema"
        />
        <div
            class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
        >
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">Atenção</p>
                <p class="text-sm">
                    Esta ação não pode ser desfeita. Proceda com cuidado.
                </p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive" data-test="delete-empresa-button"
                        >Excluir empresa</Button
                    >
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="EmpresaController.destroy.form(empresa)"
                        :options="{
                            preserveScroll: true,
                        }"
                        @success="toast.success('Empresa excluída com sucesso!')"
                        @error="toast.error('Erro ao excluir empresa', 'Tente novamente mais tarde.')"
                        class="space-y-6"
                        v-slot="{ processing }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle
                                >Tem certeza que deseja excluir esta empresa?</DialogTitle
                            >
                            <DialogDescription>
                                A empresa <strong>{{ empresa.razao_social }}</strong> será excluída permanentemente.
                                Esta ação não pode ser desfeita.
                            </DialogDescription>
                        </DialogHeader>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary">
                                    Cancelar
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                :disabled="processing"
                                data-test="confirm-delete-empresa-button"
                            >
                                {{ processing ? 'Excluindo...' : 'Excluir empresa' }}
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>

