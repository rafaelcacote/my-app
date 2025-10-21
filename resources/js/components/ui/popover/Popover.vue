<script setup lang="ts">
import { ref, provide } from 'vue';
import { PopoverContextKey, type PopoverContext } from './context';

const props = defineProps<{
    open?: boolean;
    defaultOpen?: boolean;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const open = ref(props.open ?? props.defaultOpen ?? false);

const setOpen = (value: boolean) => {
    open.value = value;
    emit('update:open', value);
};

const context: PopoverContext = {
    open,
    setOpen,
};

provide(PopoverContextKey, context);
</script>

<template>
    <div class="relative">
        <slot :open="open" :setOpen="setOpen" />
    </div>
</template>
