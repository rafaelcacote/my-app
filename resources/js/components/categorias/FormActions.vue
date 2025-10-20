<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Link } from '@inertiajs/vue3';
import { index as categoriasIndex } from '@/routes/categorias';

interface Props {
    processing: boolean;
    isCreate?: boolean;
    recentlySuccessful?: boolean;
}
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardContent class="pt-6">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:items-center">
                <Link :href="categoriasIndex().url">
                    <Button type="button" variant="outline" :disabled="processing">
                        Cancelar
                    </Button>
                </Link>
                
                <Transition v-if="!isCreate">
                    <p v-show="recentlySuccessful" class="text-sm font-medium text-green-600">
                        ✓ Salvo com sucesso
                    </p>
                </Transition>

                <Button type="submit" :disabled="processing">
                    <span v-if="!processing">
                        {{ isCreate ? 'Cadastrar Categoria' : 'Salvar Alterações' }}
                    </span>
                    <span v-else>
                        Salvando...
                    </span>
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
