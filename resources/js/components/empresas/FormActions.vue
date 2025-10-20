<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Link } from '@inertiajs/vue3';
import { index as empresasIndex } from '@/routes/empresas';

interface Props {
    processing: boolean;
    isCreate?: boolean;
    recentlySuccessful?: boolean;
}

withDefaults(defineProps<Props>(), {
    isCreate: false,
    recentlySuccessful: false
});
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardContent class="pt-6">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:items-center">
                <Link :href="empresasIndex().url">
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="processing"
                        class="w-full sm:w-auto flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow-sm"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </Button>
                </Link>
                
                <Transition
                    v-if="!isCreate"
                    enter-active-class="transition ease-in-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    leave-active-class="transition ease-in-out duration-150"
                    leave-to-class="opacity-0 scale-95"
                >
                    <p
                        v-show="recentlySuccessful"
                        class="text-sm font-medium text-green-600 dark:text-green-500"
                    >
                        ✓ Salvo com sucesso
                    </p>
                </Transition>

                <Button 
                    type="submit" 
                    :disabled="processing"
                    class="w-full sm:w-auto flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow-sm"
                >
                    <span v-if="!processing" class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        {{ isCreate ? 'Cadastrar Empresa' : 'Salvar Alterações' }}
                    </span>
                    <span v-else class="flex items-center gap-2">
                        <svg class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Salvando...
                    </span>
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
