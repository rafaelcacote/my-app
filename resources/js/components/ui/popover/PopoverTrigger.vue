<script setup lang="ts">
import { inject } from 'vue';
import { PopoverContextKey, type PopoverContext } from './context';

const context = inject<PopoverContext>(PopoverContextKey);

if (!context) {
    throw new Error('PopoverTrigger must be used within a Popover');
}

const { open, setOpen } = context;

const handleClick = () => {
    setOpen(!open.value);
};
</script>

<template>
    <div @click="handleClick">
        <slot />
    </div>
</template>
