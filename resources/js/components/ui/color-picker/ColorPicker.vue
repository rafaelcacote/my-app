<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

interface Props {
    modelValue?: string;
    disabled?: boolean;
    placeholder?: string;
    className?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '#000000',
    disabled: false,
    placeholder: '#000000',
    className: '',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const isOpen = ref(false);
const hexInput = ref(props.modelValue);

// Cores pré-definidas para facilitar a seleção
const presetColors = [
    '#000000', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF',
    '#FFFF00', '#FF00FF', '#00FFFF', '#FFA500', '#800080',
    '#FFC0CB', '#A52A2A', '#808080', '#008000', '#000080',
    '#FFD700', '#DC143C', '#32CD32', '#4169E1', '#FF6347',
    '#9370DB', '#20B2AA', '#F0E68C', '#FF69B4', '#CD853F',
    '#40E0D0', '#EE82EE', '#90EE90', '#F5DEB3', '#FFB6C1'
];

// Validação e formatação do código HEX
const isValidHex = (hex: string): boolean => {
    return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
};

const formatHex = (hex: string): string => {
    // Remove caracteres inválidos e adiciona # se necessário
    let cleanHex = hex.replace(/[^A-Fa-f0-9]/g, '');
    
    if (cleanHex.length === 3) {
        // Converte formato curto para longo (ex: #FFF -> #FFFFFF)
        cleanHex = cleanHex.split('').map(char => char + char).join('');
    }
    
    if (cleanHex.length === 6) {
        return '#' + cleanHex.toUpperCase();
    }
    
    return hex;
};

const currentColor = computed(() => {
    return isValidHex(hexInput.value) ? hexInput.value : '#000000';
});

const handleColorSelect = (color: string) => {
    hexInput.value = color;
    emit('update:modelValue', color);
    isOpen.value = false;
};

const handleHexInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const formatted = formatHex(target.value);
    hexInput.value = formatted;
    
    if (isValidHex(formatted)) {
        emit('update:modelValue', formatted);
    }
};

const handleNativeColorPicker = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const color = target.value;
    hexInput.value = color;
    emit('update:modelValue', color);
};

// Sincronizar com o valor externo
watch(() => props.modelValue, (newValue) => {
    if (newValue && isValidHex(newValue)) {
        hexInput.value = newValue;
    }
});
</script>

<template>
    <div class="space-y-3">
        <Label>Selecionar Cor</Label>
        
        <div class="flex items-center gap-3">
            <!-- Input de texto para código HEX -->
            <div class="flex-1">
                <Input
                    :model-value="hexInput"
                    @input="handleHexInput"
                    :placeholder="placeholder"
                    :disabled="disabled"
                    :class="cn('font-mono', className)"
                />
            </div>
            
            <!-- Preview da cor -->
            <div 
                class="h-10 w-16 rounded border-2 border-border flex-shrink-0"
                :style="{ backgroundColor: currentColor }"
            />
            
            <!-- Color Picker nativo -->
            <input
                type="color"
                :value="currentColor"
                @input="handleNativeColorPicker"
                :disabled="disabled"
                class="h-10 w-10 rounded border border-border cursor-pointer disabled:cursor-not-allowed"
            />
        </div>
        
        <!-- Botão para abrir paleta de cores -->
        <Button 
            variant="outline" 
            :disabled="disabled"
            class="w-full justify-start"
            @click="isOpen = !isOpen"
        >
            <div class="flex items-center gap-2">
                <div 
                    class="h-4 w-4 rounded border"
                    :style="{ backgroundColor: currentColor }"
                />
                Escolher cor pré-definida
            </div>
        </Button>
        
        <!-- Paleta de cores (simplificada) -->
        <div 
            v-if="isOpen"
            class="mt-2 p-4 border rounded-md bg-background shadow-md"
        >
            <h4 class="text-sm font-medium mb-3">Cores Pré-definidas</h4>
            <div class="grid grid-cols-5 gap-2">
                <button
                    v-for="color in presetColors"
                    :key="color"
                    @click="handleColorSelect(color)"
                    class="h-8 w-full rounded border-2 border-border hover:border-primary transition-colors"
                    :style="{ backgroundColor: color }"
                    :title="color"
                />
            </div>
        </div>
        
        <p class="text-xs text-muted-foreground">
            Digite o código hexadecimal ou use o seletor de cor
        </p>
    </div>
</template>
