<script setup lang="ts">
import { ref, inject, watch, onUnmounted } from 'vue';
import { cn } from '@/lib/utils';
import { PopoverContextKey, type PopoverContext } from './context';

const context = inject<PopoverContext>(PopoverContextKey);

if (!context) {
    throw new Error('PopoverContent must be used within a Popover');
}

const { open, setOpen } = context;

const props = defineProps<{
    class?: string;
}>();

const contentRef = ref<HTMLDivElement>();

// Fechar quando clicar fora
const handleClickOutside = (event: Event) => {
    if (contentRef.value && !contentRef.value.contains(event.target as Node)) {
        setOpen(false);
    }
};

// Adicionar listener quando o popover estiver aberto
watch(open, (isOpen) => {
    if (isOpen) {
        document.addEventListener('click', handleClickOutside);
    } else {
        document.removeEventListener('click', handleClickOutside);
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div
        v-if="open"
        ref="contentRef"
        :class="cn(
            'absolute z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-4 text-popover-foreground shadow-md',
            'animate-in fade-in-0 zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95',
            props.class
        )"
        style="top: 100%; left: 0; margin-top: 4px;"
    >
        <slot />
    </div>
</template>
