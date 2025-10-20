import { ref } from 'vue';

export interface Toast {
    id: string;
    type: 'success' | 'error' | 'info' | 'warning';
    title: string;
    message?: string;
    duration?: number;
    progress?: number;
    startTime?: number;
}

const toasts = ref<Toast[]>([]);
const progressIntervals = ref<Map<string, number>>(new Map());

let idCounter = 0;

export const useToast = () => {
    const add = (toast: Omit<Toast, 'id'>) => {
        const id = `toast-${++idCounter}`;
        const startTime = Date.now();
        const duration = toast.duration || 5000;
        
        const newToast: Toast = {
            id,
            duration,
            progress: 100,
            startTime,
            ...toast,
        };

        toasts.value.push(newToast);

        // Start progress bar animation
        if (duration > 0) {
            const interval = window.setInterval(() => {
                const now = Date.now();
                const elapsed = now - startTime;
                const remaining = Math.max(0, duration - elapsed);
                
                // Update progress
                const toastIndex = toasts.value.findIndex(t => t.id === id);
                if (toastIndex > -1) {
                    toasts.value[toastIndex].progress = (remaining / duration) * 100;
                }
                
                if (remaining <= 0) {
                    clearInterval(interval);
                    progressIntervals.value.delete(id);
                    remove(id);
                }
            }, 10);
            
            progressIntervals.value.set(id, interval);
        }

        return id;
    };

    const remove = (id: string) => {
        const index = toasts.value.findIndex((t) => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
        
        // Clear interval if exists
        const interval = progressIntervals.value.get(id);
        if (interval) {
            clearInterval(interval);
            progressIntervals.value.delete(id);
        }
    };

    const success = (title: string, message?: string, duration?: number) => {
        return add({ type: 'success', title, message, duration });
    };

    const error = (title: string, message?: string, duration?: number) => {
        return add({ type: 'error', title, message, duration });
    };

    const info = (title: string, message?: string, duration?: number) => {
        return add({ type: 'info', title, message, duration });
    };

    const warning = (title: string, message?: string, duration?: number) => {
        return add({ type: 'warning', title, message, duration });
    };

    const clear = () => {
        // Clear all intervals
        progressIntervals.value.forEach(interval => clearInterval(interval));
        progressIntervals.value.clear();
        toasts.value = [];
    };

    return {
        toasts,
        add,
        remove,
        success,
        error,
        info,
        warning,
        clear,
    };
};

