<script setup lang="ts">
withDefaults(
    defineProps<{
        placeholder?: string;
    }>(),
    {
        placeholder: 'Selecione...',
    }
);
</script>

<template>
    <span class="text-sm">{{ placeholder }}</span>
</template>

