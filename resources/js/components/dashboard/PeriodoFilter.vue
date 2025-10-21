<script setup lang="ts">
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Calendar } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

interface PeriodoFilterProps {
    periodoInicio: string;
    periodoFim: string;
}

const props = defineProps<PeriodoFilterProps>();

const isOpen = ref(false);
const periodoInicio = ref(props.periodoInicio);
const periodoFim = ref(props.periodoFim);

const aplicarFiltro = () => {
    router.get('/dashboard', {
        periodo_inicio: periodoInicio.value,
        periodo_fim: periodoFim.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
    isOpen.value = false;
};

const definirPeriodoRapido = (dias: number) => {
    const hoje = new Date();
    const inicio = new Date(hoje);
    inicio.setDate(hoje.getDate() - dias);
    
    periodoInicio.value = inicio.toISOString().split('T')[0];
    periodoFim.value = hoje.toISOString().split('T')[0];
};

const formatarData = (data: string) => {
    return new Date(data).toLocaleDateString('pt-BR');
};
</script>

<template>
    <div class="relative">
        <Button variant="outline" size="sm" @click="isOpen = !isOpen">
            <Calendar class="mr-2 h-4 w-4" />
            {{ formatarData(periodoInicio) }} - {{ formatarData(periodoFim) }}
        </Button>

        <div v-if="isOpen" class="absolute right-0 top-full z-50 mt-2 w-80">
            <Card>
                <CardHeader>
                    <CardTitle class="text-sm">Filtrar por Período</CardTitle>
                    <CardDescription>
                        Selecione o período para visualizar as métricas
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Períodos rápidos -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Períodos Rápidos:</label>
                        <div class="grid grid-cols-2 gap-2">
                            <Button variant="outline" size="sm" @click="definirPeriodoRapido(7)">
                                7 dias
                            </Button>
                            <Button variant="outline" size="sm" @click="definirPeriodoRapido(30)">
                                30 dias
                            </Button>
                            <Button variant="outline" size="sm" @click="definirPeriodoRapido(90)">
                                90 dias
                            </Button>
                            <Button variant="outline" size="sm" @click="definirPeriodoRapido(365)">
                                1 ano
                            </Button>
                        </div>
                    </div>

                    <!-- Seleção manual -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Período Personalizado:</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-muted-foreground">Data Início:</label>
                                <input 
                                    v-model="periodoInicio" 
                                    type="date" 
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                />
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground">Data Fim:</label>
                                <input 
                                    v-model="periodoFim" 
                                    type="date" 
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="flex gap-2 pt-2">
                        <Button @click="aplicarFiltro" class="flex-1">
                            Aplicar Filtro
                        </Button>
                        <Button variant="outline" @click="isOpen = false">
                            Cancelar
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
