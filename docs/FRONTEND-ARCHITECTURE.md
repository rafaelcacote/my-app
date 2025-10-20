# Arquitetura Frontend

Este documento detalha a arquitetura frontend do sistema, incluindo estrutura de componentes Vue 3, sistema de layouts, composables, integração com Inertia.js e padrões de desenvolvimento.

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Estrutura de Diretórios](#estrutura-de-diretórios)
3. [Sistema de Páginas Inertia](#sistema-de-páginas-inertia)
4. [Sistema de Layouts](#sistema-de-layouts)
5. [Componentes](#componentes)
6. [Composables](#composables)
7. [Sistema de Rotas (Wayfinder)](#sistema-de-rotas-wayfinder)
8. [TypeScript Types](#typescript-types)
9. [UI Components Library](#ui-components-library)
10. [Comunicação com Backend](#comunicação-com-backend)
11. [Exemplo Completo](#exemplo-completo)
12. [Boas Práticas](#boas-práticas)

## Visão Geral

### Stack Frontend

- **Vue 3** - Framework JavaScript progressivo com Composition API
- **TypeScript** - Type safety e melhor DX
- **Inertia.js** - Adapter entre Laravel e Vue (SPA experience)
- **Tailwind CSS 4** - Utility-first CSS framework
- **Reka UI** - Headless UI components (baseado em Radix UI)
- **Lucide Icons** - Biblioteca de ícones moderna
- **Wayfinder** - Type-safe Laravel routes no frontend

### Arquitetura SPA Híbrida

```
┌─────────────────────────────────────────┐
│           Laravel Backend               │
│  ┌───────────────────────────────────┐ │
│  │         Controller                │ │
│  │  Inertia::render('Page', [...])  │ │
│  └────────────┬──────────────────────┘ │
└───────────────┼─────────────────────────┘
                │ JSON Response
                ▼
┌─────────────────────────────────────────┐
│          Inertia.js Layer               │
│   (SPA Navigation + State Management)   │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│          Vue 3 Frontend                 │
│  ┌───────────────────────────────────┐ │
│  │      Page Component               │ │
│  │         (props)                   │ │
│  │           ↓                       │ │
│  │       Layouts                     │ │
│  │           ↓                       │ │
│  │      Components                   │ │
│  └───────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

## Estrutura de Diretórios

```
resources/js/
├── actions/                    # Wayfinder generated routes
│   ├── App/
│   │   └── Http/
│   │       └── Controllers/   # Type-safe route helpers
│   ├── Illuminate/
│   └── Laravel/
│
├── components/                 # Componentes reutilizáveis
│   ├── ui/                    # UI components (shadcn-vue style)
│   │   ├── button/
│   │   ├── input/
│   │   ├── card/
│   │   └── ...
│   ├── AlertError.vue
│   ├── AppHeader.vue
│   ├── AppSidebar.vue
│   └── ...
│
├── composables/               # Lógica reutilizável Vue
│   ├── useAppearance.ts
│   ├── useTwoFactorAuth.ts
│   └── useInitials.ts
│
├── layouts/                   # Layouts da aplicação
│   ├── app/
│   │   ├── AppSidebarLayout.vue
│   │   └── AppHeaderLayout.vue
│   ├── auth/
│   │   ├── AuthCardLayout.vue
│   │   └── AuthSplitLayout.vue
│   ├── settings/
│   │   └── Layout.vue
│   ├── AppLayout.vue
│   └── AuthLayout.vue
│
├── pages/                     # Páginas Inertia
│   ├── auth/
│   │   ├── Login.vue
│   │   ├── Register.vue
│   │   └── ...
│   ├── settings/
│   │   ├── Profile.vue
│   │   ├── Password.vue
│   │   └── ...
│   ├── Dashboard.vue
│   └── Welcome.vue
│
├── types/                     # TypeScript definitions
│   ├── index.d.ts
│   └── globals.d.ts
│
├── lib/                       # Utilities
│   └── utils.ts              # Helper functions
│
├── app.ts                     # Entry point
└── ssr.ts                     # SSR entry (opcional)
```

## Sistema de Páginas Inertia

### Estrutura de Uma Página

As páginas Inertia são componentes Vue que recebem props do backend.

```vue
<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';

// Props recebidas do controller
interface Props {
    user: User;
    status?: string;
}

const props = defineProps<Props>();

// Breadcrumbs para navegação
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <!-- Meta tags -->
    <Head title="Dashboard" />

    <!-- Layout wrapper -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Conteúdo da página -->
        <div class="p-6">
            <h1>Welcome, {{ user.name }}!</h1>
        </div>
    </AppLayout>
</template>
```

### Props do Backend

Props são passadas do controller Laravel:

```php
// Backend - Controller
return Inertia::render('settings/Profile', [
    'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
    'status' => $request->session()->get('status'),
]);
```

```vue
<!-- Frontend - Page Component -->
<script setup lang="ts">
interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();
</script>
```

### Shared Props Globais

Props compartilhadas em todas as páginas (definidas em `HandleInertiaRequests`):

```php
// app/Http/Middleware/HandleInertiaRequests.php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'name' => config('app.name'),
        'auth' => [
            'user' => $request->user(),
        ],
        'sidebarOpen' => ! $request->hasCookie('sidebar_state'),
    ];
}
```

Acessar no frontend:

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user; // Acesso ao usuário autenticado
const appName = page.props.name;   // Nome da aplicação
</script>
```

## Sistema de Layouts

### Hierarquia de Layouts

```
AppLayout.vue (Wrapper principal)
    ↓
AppSidebarLayout.vue (Layout com sidebar)
    ├── AppSidebar (Navegação lateral)
    ├── AppHeader (Header superior)
    └── AppContent (Conteúdo principal)
```

### AppLayout (Layout Principal)

```vue
<!-- resources/js/layouts/AppLayout.vue -->
<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
```

### AppSidebarLayout

Layout com sidebar para páginas autenticadas:

```vue
<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import AppSidebar from '@/components/AppSidebar.vue';
import AppHeader from '@/components/AppHeader.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const defaultOpen = page.props.sidebarOpen;
</script>

<template>
    <SidebarProvider :default-open="defaultOpen">
        <AppSidebar />
        <main class="flex-1 overflow-auto">
            <AppHeader :breadcrumbs="breadcrumbs" />
            <div class="p-6">
                <slot />
            </div>
        </main>
    </SidebarProvider>
</template>
```

### AuthLayout

Layout para páginas de autenticação:

```vue
<script setup lang="ts">
import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue';
</script>

<template>
    <AuthSplitLayout>
        <slot />
    </AuthSplitLayout>
</template>
```

### SettingsLayout

Layout específico para páginas de configurações:

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const tabs = [
    { name: 'Profile', href: '/settings/profile' },
    { name: 'Password', href: '/settings/password' },
    { name: 'Two-Factor', href: '/settings/two-factor' },
    { name: 'Appearance', href: '/settings/appearance' },
];
</script>

<template>
    <div class="space-y-6">
        <!-- Tabs de navegação -->
        <nav class="flex space-x-4 border-b">
            <Link
                v-for="tab in tabs"
                :key="tab.name"
                :href="tab.href"
                :class="[
                    'px-4 py-2',
                    route().current(tab.href) ? 'border-b-2 border-primary' : ''
                ]"
            >
                {{ tab.name }}
            </Link>
        </nav>

        <!-- Conteúdo -->
        <slot />
    </div>
</template>
```

### Uso de Layouts

```vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Seu conteúdo aqui -->
    </AppLayout>
</template>
```

## Componentes

### Estrutura de Componentes

#### Componentes de Aplicação

Localizados em `resources/js/components/`:

```vue
<!-- AlertError.vue -->
<script setup lang="ts">
interface Props {
    message?: string;
}

defineProps<Props>();
</script>

<template>
    <div v-if="message" class="rounded-md bg-red-50 p-4">
        <p class="text-sm text-red-800">{{ message }}</p>
    </div>
</template>
```

#### Componentes Reutilizáveis

```vue
<!-- Heading.vue -->
<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="space-y-2">
        <h2 class="text-2xl font-bold">{{ title }}</h2>
        <p v-if="description" class="text-muted-foreground">
            {{ description }}
        </p>
    </div>
</template>
```

### Convenções de Componentes

#### Nomenclatura
- **PascalCase** para nomes de arquivo: `AppHeader.vue`, `UserProfile.vue`
- **Props** tipadas com TypeScript
- **Emits** declarados explicitamente

```vue
<script setup lang="ts">
// Props
interface Props {
    modelValue: string;
    placeholder?: string;
}

const props = defineProps<Props>();

// Emits
const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

// Handlers
const handleInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    emit('update:modelValue', target.value);
};
</script>
```

#### Composition API

Use Composition API (setup script):

```vue
<script setup lang="ts">
import { ref, computed } from 'vue';

// State
const count = ref(0);

// Computed
const doubled = computed(() => count.value * 2);

// Methods
const increment = () => {
    count.value++;
};
</script>
```

### Componentes Complexos - Exemplo

```vue
<!-- DeleteUser.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const open = ref(false);

const form = useForm({
    password: '',
});

const deleteUser = () => {
    form.delete(ProfileController.destroy().url, {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
        },
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button variant="destructive">Delete Account</Button>
        </DialogTrigger>
        
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Delete Account</DialogTitle>
                <DialogDescription>
                    This action cannot be undone. Please enter your password to confirm.
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="deleteUser">
                <div class="space-y-4">
                    <div>
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                        />
                    </div>
                </div>

                <DialogFooter class="mt-6">
                    <Button type="button" variant="outline" @click="open = false">
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="form.processing"
                    >
                        Delete Account
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
```

## Composables

### O que são Composables

Composables são funções reutilizáveis que encapsulam lógica stateful usando a Composition API.

### Estrutura de um Composable

```typescript
// resources/js/composables/useCounter.ts
import { ref, computed } from 'vue';

export function useCounter(initialValue = 0) {
    // State
    const count = ref(initialValue);

    // Computed
    const doubled = computed(() => count.value * 2);

    // Methods
    const increment = () => {
        count.value++;
    };

    const decrement = () => {
        count.value--;
    };

    const reset = () => {
        count.value = initialValue;
    };

    // Return public API
    return {
        count,
        doubled,
        increment,
        decrement,
        reset,
    };
}
```

### Exemplo Real: useTwoFactorAuth

```typescript
// resources/js/composables/useTwoFactorAuth.ts
import { qrCode, recoveryCodes, secretKey } from '@/routes/two-factor';
import { computed, ref } from 'vue';

const fetchJson = async <T>(url: string): Promise<T> => {
    const response = await fetch(url, {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Failed to fetch: ${response.status}`);
    }

    return response.json();
};

const errors = ref<string[]>([]);
const manualSetupKey = ref<string | null>(null);
const qrCodeSvg = ref<string | null>(null);
const recoveryCodesList = ref<string[]>([]);

const hasSetupData = computed<boolean>(
    () => qrCodeSvg.value !== null && manualSetupKey.value !== null,
);

export const useTwoFactorAuth = () => {
    const fetchQrCode = async (): Promise<void> => {
        try {
            const { svg } = await fetchJson<{ svg: string }>(qrCode.url());
            qrCodeSvg.value = svg;
        } catch {
            errors.value.push('Failed to fetch QR code');
            qrCodeSvg.value = null;
        }
    };

    const fetchSetupKey = async (): Promise<void> => {
        try {
            const { secretKey: key } = await fetchJson<{ secretKey: string }>(
                secretKey.url(),
            );
            manualSetupKey.value = key;
        } catch {
            errors.value.push('Failed to fetch setup key');
            manualSetupKey.value = null;
        }
    };

    const fetchRecoveryCodes = async (): Promise<void> => {
        try {
            recoveryCodesList.value = await fetchJson<string[]>(
                recoveryCodes.url(),
            );
        } catch {
            errors.value.push('Failed to fetch recovery codes');
        }
    };

    const clearSetupData = (): void => {
        manualSetupKey.value = null;
        qrCodeSvg.value = null;
    };

    return {
        // State
        qrCodeSvg,
        manualSetupKey,
        recoveryCodesList,
        errors,
        hasSetupData,

        // Methods
        fetchQrCode,
        fetchSetupKey,
        fetchRecoveryCodes,
        clearSetupData,
    };
};
```

### Uso em Componentes

```vue
<script setup lang="ts">
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';

const {
    qrCodeSvg,
    manualSetupKey,
    hasSetupData,
    fetchQrCode,
    fetchSetupKey,
} = useTwoFactorAuth();

// Buscar dados ao montar
onMounted(async () => {
    await Promise.all([fetchQrCode(), fetchSetupKey()]);
});
</script>

<template>
    <div v-if="hasSetupData">
        <div v-html="qrCodeSvg" />
        <p>Manual key: {{ manualSetupKey }}</p>
    </div>
</template>
```

### Composables Existentes

#### useAppearance

Gerencia tema claro/escuro:

```typescript
import { useAppearance } from '@/composables/useAppearance';

const { theme, setTheme } = useAppearance();

// Mudar tema
setTheme('dark');
setTheme('light');
setTheme('system');
```

#### useInitials

Extrai iniciais de um nome:

```typescript
import { useInitials } from '@/composables/useInitials';

const initials = useInitials('John Doe'); // "JD"
```

## Sistema de Rotas (Wayfinder)

### O que é Wayfinder

Wayfinder gera automaticamente type-safe route helpers baseados nas rotas Laravel.

### Geração Automática

```php
// Backend - routes/web.php
Route::get('settings/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

Route::patch('settings/profile', [ProfileController::class, 'update'])
    ->name('profile.update');
```

Wayfinder gera automaticamente:

```typescript
// resources/js/actions/App/Http/Controllers/Settings/ProfileController.ts

export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.url = (options?: RouteQueryOptions) => {
    return '/settings/profile' + queryParams(options)
}

export const update = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

update.url = (options?: RouteQueryOptions) => {
    return '/settings/profile' + queryParams(options)
}
```

### Uso no Frontend

#### Navegação (Links)

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { edit } from '@/routes/profile';
</script>

<template>
    <Link :href="edit().url">
        Edit Profile
    </Link>
</template>
```

#### Formulários

```vue
<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
</script>

<template>
    <Form v-bind="ProfileController.update.form()">
        <input name="name" />
        <input name="email" />
        <button type="submit">Save</button>
    </Form>
</template>
```

#### Programaticamente

```vue
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';

const navigateToProfile = () => {
    router.visit(ProfileController.edit().url);
};

const updateProfile = (data: ProfileData) => {
    router.patch(ProfileController.update().url, data);
};
</script>
```

### Query Parameters

```typescript
import { dashboard } from '@/routes';

// Sem query params
dashboard().url // "/dashboard"

// Com query params
dashboard({ query: { tab: 'overview' } }).url // "/dashboard?tab=overview"

// Merge query params
dashboard({ mergeQuery: { page: 1 } }).url
```

### Route Parameters

Para rotas com parâmetros:

```php
// Backend
Route::get('products/{product}', [ProductController::class, 'show'])
    ->name('products.show');
```

```typescript
// Frontend
import ProductController from '@/actions/App/Http/Controllers/ProductController';

ProductController.show(productId).url // "/products/123"
```

## TypeScript Types

### Definição de Types

```typescript
// resources/js/types/index.d.ts

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
};
```

### Uso de Types em Componentes

```vue
<script setup lang="ts">
import type { User, BreadcrumbItem } from '@/types';

interface Props {
    user: User;
    breadcrumbs?: BreadcrumbItem[];
}

const props = defineProps<Props>();
</script>
```

### Extending Page Props

```typescript
// Para uma página específica
import type { AppPageProps, User } from '@/types';

interface ProfilePageProps extends AppPageProps {
    mustVerifyEmail: boolean;
    status?: string;
}
```

## UI Components Library

O sistema usa uma biblioteca de componentes UI baseada em **shadcn/ui** (adaptada para Vue com Reka UI).

### Estrutura dos Componentes UI

```
resources/js/components/ui/
├── button/
│   ├── Button.vue
│   └── index.ts
├── input/
│   ├── Input.vue
│   └── index.ts
├── card/
│   ├── Card.vue
│   ├── CardHeader.vue
│   ├── CardContent.vue
│   ├── CardFooter.vue
│   └── index.ts
└── ...
```

### Exemplo: Button

```vue
<!-- resources/js/components/ui/button/Button.vue -->
<script setup lang="ts">
import { type ButtonVariantProps, buttonVariants } from './';
import { cn } from '@/lib/utils';

interface Props {
    variant?: ButtonVariantProps['variant'];
    size?: ButtonVariantProps['size'];
    as?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
    size: 'default',
    as: 'button',
});
</script>

<template>
    <component
        :is="as"
        :class="cn(buttonVariants({ variant, size }), $attrs.class)"
    >
        <slot />
    </component>
</template>
```

### Uso de Componentes UI

```vue
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Create Account</CardTitle>
            <CardDescription>Enter your details below</CardDescription>
        </CardHeader>
        
        <CardContent class="space-y-4">
            <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input id="email" type="email" placeholder="m@example.com" />
            </div>
            
            <div class="space-y-2">
                <Label for="password">Password</Label>
                <Input id="password" type="password" />
            </div>
        </CardContent>
        
        <CardFooter>
            <Button class="w-full">Create account</Button>
        </CardFooter>
    </Card>
</template>
```

### Variantes de Componentes

Componentes suportam variantes via Class Variance Authority:

```vue
<!-- Variantes de Button -->
<Button variant="default">Default</Button>
<Button variant="destructive">Delete</Button>
<Button variant="outline">Outline</Button>
<Button variant="ghost">Ghost</Button>
<Button variant="link">Link</Button>

<!-- Tamanhos -->
<Button size="sm">Small</Button>
<Button size="default">Default</Button>
<Button size="lg">Large</Button>
```

### Componentes Disponíveis

- **Button** - Botões com variantes
- **Input** - Campos de input
- **Label** - Labels para formulários
- **Card** - Cards containers
- **Dialog** - Modals/Dialogs
- **Alert** - Alertas e notificações
- **Badge** - Badges e tags
- **Checkbox** - Checkboxes
- **Avatar** - Avatares de usuário
- **Dropdown Menu** - Menus dropdown
- **Sidebar** - Sidebar navigation
- **Breadcrumb** - Breadcrumbs de navegação
- E muitos outros...

## Comunicação com Backend

### Inertia Forms

#### useForm Hook

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';

const form = useForm({
    name: '',
    email: '',
});

const submit = () => {
    form.patch(ProfileController.update().url, {
        preserveScroll: true,
        onSuccess: () => {
            // Sucesso
        },
        onError: () => {
            // Erro
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <input v-model="form.name" />
        <input v-model="form.email" />
        
        <p v-if="form.errors.name">{{ form.errors.name }}</p>
        <p v-if="form.errors.email">{{ form.errors.email }}</p>
        
        <button :disabled="form.processing">
            Save
        </button>
    </form>
</template>
```

#### Form Component (Declarativo)

```vue
<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
</script>

<template>
    <Form
        v-bind="ProfileController.update.form()"
        v-slot="{ errors, processing }"
    >
        <Input name="name" :error="errors.name" />
        <Input name="email" type="email" :error="errors.email" />
        
        <Button :disabled="processing">Save</Button>
    </Form>
</template>
```

### Inertia Router

```vue
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import ProductController from '@/actions/App/Http/Controllers/ProductController';

// GET request (visit)
const visitProduct = (id: number) => {
    router.visit(ProductController.show(id).url);
};

// POST request
const createProduct = (data: ProductData) => {
    router.post(ProductController.store().url, data, {
        onSuccess: () => {
            // Success callback
        },
    });
};

// PATCH request
const updateProduct = (id: number, data: ProductData) => {
    router.patch(ProductController.update(id).url, data);
};

// DELETE request
const deleteProduct = (id: number) => {
    router.delete(ProductController.destroy(id).url, {
        onBefore: () => confirm('Are you sure?'),
    });
};
</script>
```

### Fetch API (para endpoints JSON)

```vue
<script setup lang="ts">
import { ref } from 'vue';
import { qrCode } from '@/routes/two-factor';

const qrCodeSvg = ref<string | null>(null);

const fetchQrCode = async () => {
    const response = await fetch(qrCode.url(), {
        headers: { Accept: 'application/json' },
    });
    
    const data = await response.json();
    qrCodeSvg.value = data.svg;
};
</script>
```

## Exemplo Completo

Aqui está um exemplo completo de uma página CRUD com formulário.

### Backend Controller

```php
namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('products/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        return to_route('products.show', $product)
            ->with('success', 'Product created successfully');
    }
}
```

### Frontend Page

```vue
<!-- resources/js/pages/products/Create.vue -->
<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ProductController from '@/actions/App/Http/Controllers/ProductController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';

interface Category {
    id: number;
    name: string;
}

interface Props {
    categories: Category[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Products', href: '/products' },
    { title: 'Create', href: '/products/create' },
];

const form = useForm({
    name: '',
    slug: '',
    description: '',
    price: '',
    stock: '',
    category_id: '',
    is_active: true,
});

const submit = () => {
    form.post(ProductController.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <div class="mb-6">
                <h1 class="text-3xl font-bold">Create Product</h1>
                <p class="text-muted-foreground">
                    Add a new product to your inventory
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Name -->
                <div class="space-y-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        required
                        placeholder="Product name"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Slug -->
                <div class="space-y-2">
                    <Label for="slug">Slug</Label>
                    <Input
                        id="slug"
                        v-model="form.slug"
                        required
                        placeholder="product-slug"
                    />
                    <InputError :message="form.errors.slug" />
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        placeholder="Product description"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <!-- Price and Stock -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="price">Price</Label>
                        <Input
                            id="price"
                            v-model="form.price"
                            type="number"
                            step="0.01"
                            required
                            placeholder="0.00"
                        />
                        <InputError :message="form.errors.price" />
                    </div>

                    <div class="space-y-2">
                        <Label for="stock">Stock</Label>
                        <Input
                            id="stock"
                            v-model="form.stock"
                            type="number"
                            required
                            placeholder="0"
                        />
                        <InputError :message="form.errors.stock" />
                    </div>
                </div>

                <!-- Category -->
                <div class="space-y-2">
                    <Label for="category">Category</Label>
                    <Select v-model="form.category_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Select a category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id.toString()"
                            >
                                {{ category.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                    >
                        Create Product
                    </Button>

                    <Button
                        type="button"
                        variant="outline"
                        @click="form.reset()"
                    >
                        Reset
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
```

## Boas Práticas

### 1. Use Composition API

```vue
<!-- ✅ Bom -->
<script setup lang="ts">
import { ref, computed } from 'vue';

const count = ref(0);
const doubled = computed(() => count.value * 2);
</script>

<!-- ❌ Evite Options API -->
<script lang="ts">
export default {
    data() {
        return { count: 0 };
    },
    computed: {
        doubled() {
            return this.count * 2;
        }
    }
}
</script>
```

### 2. Sempre Tipar Props e Emits

```vue
<script setup lang="ts">
// ✅ Bom
interface Props {
    title: string;
    count?: number;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    update: [value: number];
}>();
</script>
```

### 3. Use Wayfinder para Rotas

```vue
<!-- ✅ Bom -->
<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
</script>

<template>
    <Link :href="ProfileController.edit().url">Edit</Link>
</template>

<!-- ❌ Evite hardcoded URLs -->
<template>
    <Link href="/settings/profile">Edit</Link>
</template>
```

### 4. Extraia Lógica Complexa para Composables

```vue
<!-- ✅ Bom -->
<script setup lang="ts">
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';

const { qrCodeSvg, fetchQrCode } = useTwoFactorAuth();
</script>

<!-- ❌ Evite lógica complexa no componente -->
<script setup lang="ts">
const qrCodeSvg = ref(null);
const fetchQrCode = async () => {
    // Muita lógica aqui...
};
</script>
```

### 5. Use Layouts para Estrutura Comum

```vue
<!-- ✅ Bom -->
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div><!-- Conteúdo --></div>
    </AppLayout>
</template>

<!-- ❌ Evite duplicar estrutura -->
<template>
    <div>
        <header>...</header>
        <sidebar>...</sidebar>
        <main><!-- Conteúdo --></main>
    </div>
</template>
```

### 6. Componentes Pequenos e Focados

```vue
<!-- ✅ Bom - Componente focado -->
<!-- UserAvatar.vue -->
<script setup lang="ts">
interface Props {
    user: User;
}
defineProps<Props>();
</script>

<template>
    <Avatar>
        <AvatarImage :src="user.avatar" />
        <AvatarFallback>{{ user.initials }}</AvatarFallback>
    </Avatar>
</template>

<!-- ❌ Evite componentes gigantes -->
<!-- Giant component com 500+ linhas -->
```

### 7. Feedback Visual Adequado

```vue
<script setup lang="ts">
const form = useForm({...});

const submit = () => {
    form.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            // Feedback de sucesso
            toast.success('Saved!');
        },
        onError: () => {
            // Feedback de erro
            toast.error('Something went wrong');
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <!-- Mostrar estado de loading -->
        <Button :disabled="form.processing">
            <Loader v-if="form.processing" class="animate-spin" />
            Save
        </Button>
        
        <!-- Mostrar erros -->
        <InputError :message="form.errors.name" />
    </form>
</template>
```

### 8. Use v-bind para Atributos Dinâmicos

```vue
<!-- ✅ Bom -->
<Form v-bind="ProfileController.update.form()">
    ...
</Form>

<!-- ❌ Evite -->
<Form
    :action="ProfileController.update().url"
    :method="ProfileController.update().method"
>
    ...
</Form>
```

### 9. Organize Imports

```vue
<script setup lang="ts">
// Vue imports
import { ref, computed, onMounted } from 'vue';

// Inertia imports
import { Head, Link, useForm } from '@inertiajs/vue3';

// Actions/Routes
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';

// Layouts
import AppLayout from '@/layouts/AppLayout.vue';

// Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

// Composables
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';

// Types
import type { User, BreadcrumbItem } from '@/types';
</script>
```

### 10. Prefira Declarativo sobre Imperativo

```vue
<!-- ✅ Bom - Declarativo -->
<Form v-bind="ProfileController.update.form()">
    <Input name="email" />
</Form>

<!-- ❌ Evite - Imperativo -->
<form @submit.prevent="handleSubmit">
    <input v-model="email" @input="validateEmail" />
</form>
```

## Conclusão

A arquitetura frontend do sistema é moderna, type-safe e altamente produtiva. Principais pontos:

- **Vue 3 + Composition API** para componentes reativos e reutilizáveis
- **TypeScript** para type safety em todo o frontend
- **Inertia.js** para SPA experience sem complexidade de API
- **Wayfinder** para rotas type-safe
- **Componentes UI reutilizáveis** baseados em shadcn/ui
- **Composables** para lógica compartilhada
- **Layouts** para estrutura consistente

Seguindo os padrões e práticas documentados aqui, você garantirá um código frontend consistente, manutenível e escalável.

