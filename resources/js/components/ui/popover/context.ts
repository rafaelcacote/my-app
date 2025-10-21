import type { Ref } from 'vue';

export interface PopoverContext {
    open: Ref<boolean>;
    setOpen: (value: boolean) => void;
}

export const PopoverContextKey = Symbol('PopoverContext');
