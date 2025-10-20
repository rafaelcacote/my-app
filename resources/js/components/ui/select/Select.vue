<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    modelValue?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val || ''),
});
</script>

<template>
    <div>
        <slot :value="value" />
    </div>
</template>

