<script setup lang="ts">
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        class?: string;
    }>(),
    {
        class: '',
    }
);
</script>

<template>
    <div :class="cn('relative', props.class)">
        <slot />
    </div>
</template>

