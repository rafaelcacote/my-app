<script setup lang="ts">
import { useToast, type Toast } from '@/composables/useToast';
import {
    CheckCircle2,
    XCircle,
    AlertCircle,
    Info,
    X,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    toast: Toast;
}

const props = defineProps<Props>();
const { remove } = useToast();

const icon = computed(() => {
    switch (props.toast.type) {
        case 'success':
            return CheckCircle2;
        case 'error':
            return XCircle;
        case 'warning':
            return AlertCircle;
        case 'info':
            return Info;
        default:
            return Info;
    }
});

const colorClasses = computed(() => {
    switch (props.toast.type) {
        case 'success':
            return 'bg-green-50 border-green-200 text-green-900 dark:bg-green-900/20 dark:border-green-800 dark:text-green-100';
        case 'error':
            return 'bg-red-50 border-red-200 text-red-900 dark:bg-red-900/20 dark:border-red-800 dark:text-red-100';
        case 'warning':
            return 'bg-yellow-50 border-yellow-200 text-yellow-900 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-100';
        case 'info':
            return 'bg-blue-50 border-blue-200 text-blue-900 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-100';
        default:
            return 'bg-gray-50 border-gray-200 text-gray-900 dark:bg-gray-900/20 dark:border-gray-800 dark:text-gray-100';
    }
});

const iconColorClasses = computed(() => {
    switch (props.toast.type) {
        case 'success':
            return 'text-green-600 dark:text-green-400';
        case 'error':
            return 'text-red-600 dark:text-red-400';
        case 'warning':
            return 'text-yellow-600 dark:text-yellow-400';
        case 'info':
            return 'text-blue-600 dark:text-blue-400';
        default:
            return 'text-gray-600 dark:text-gray-400';
    }
});
</script>

<template>
    <div
        :class="[
            'pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg border shadow-lg',
            colorClasses,
        ]"
        role="alert"
    >
        <div class="p-4">
            <div class="flex items-start">
                <div class="shrink-0">
                    <component
                        :is="icon"
                        :class="['h-5 w-5', iconColorClasses]"
                        aria-hidden="true"
                    />
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium">
                        {{ toast.title }}
                    </p>
                    <p v-if="toast.message" class="mt-1 text-sm opacity-90">
                        {{ toast.message }}
                    </p>
                </div>
                <div class="ml-4 flex shrink-0">
                    <button
                        type="button"
                        @click="remove(toast.id)"
                        class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                    >
                        <span class="sr-only">Fechar</span>
                        <X class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </div>
        <!-- Progress bar -->
        <div 
            v-if="toast.duration && toast.duration > 0" 
            class="h-1 w-full bg-gray-200 dark:bg-gray-700"
        >
            <div 
                :class="[
                    'h-full transition-all duration-100 ease-linear',
                    {
                        'bg-green-500 dark:bg-green-400': toast.type === 'success',
                        'bg-red-500 dark:bg-red-400': toast.type === 'error',
                        'bg-yellow-500 dark:bg-yellow-400': toast.type === 'warning',
                        'bg-blue-500 dark:bg-blue-400': toast.type === 'info',
                        'bg-gray-500 dark:bg-gray-400': !toast.type
                    }
                ]"
                :style="{ width: `${toast.progress || 100}%` }"
            ></div>
        </div>
    </div>
</template>

