<script setup lang="ts">
import { computed, ref, watch } from 'vue';

interface Props {
  modelValue?: boolean;
  disabled?: boolean;
  id?: string;
  name?: string;
  class?: string;
  checked?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: undefined,
  disabled: false,
  id: undefined,
  name: undefined,
  class: '',
  checked: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'update:checked', value: boolean): void;
}>();

const isChecked = ref(props.modelValue !== undefined ? props.modelValue : props.checked);

watch(() => props.modelValue, (value) => {
  if (value !== undefined) {
    isChecked.value = value;
  }
});

watch(() => props.checked, (value) => {
  isChecked.value = value;
});

const toggle = () => {
  if (props.disabled) return;
  
  isChecked.value = !isChecked.value;
  
  if (props.modelValue !== undefined) {
    emit('update:modelValue', isChecked.value);
  }
  
  emit('update:checked', isChecked.value);
};

const switchClasses = computed(() => {
  return [
    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background',
    isChecked.value ? 'bg-primary' : 'bg-input',
    props.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
    props.class
  ];
});

const thumbClasses = computed(() => {
  return [
    'pointer-events-none block h-5 w-5 rounded-full bg-background shadow-lg ring-0 transition-transform',
    isChecked.value ? 'translate-x-5' : 'translate-x-1'
  ];
});
</script>

<template>
  <button
    :id="id"
    type="button"
    role="switch"
    :aria-checked="isChecked"
    :disabled="disabled"
    :class="switchClasses"
    @click="toggle"
  >
    <span :class="thumbClasses" />
  </button>
</template>
