<script setup lang="ts">
import { cn } from '@/lib/utils';
import { inject, Ref } from 'vue';

const props = withDefaults(
    defineProps<{
        class?: string;
    }>(),
    {
        class: '',
    }
);

const value = inject<Ref<string>>('selectValue');

const handleChange = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    if (value) {
        value.value = target.value;
    }
};
</script>

<template>
    <select
        :value="value"
        @change="handleChange"
        :class="
            cn(
                'flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                props.class
            )
        "
    >
        <slot />
    </select>
</template>

