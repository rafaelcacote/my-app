<script setup lang="ts">
const props = defineProps<{
    value: string;
}>();
</script>

<template>
    <option :value="props.value">
        <slot />
    </option>
</template>

