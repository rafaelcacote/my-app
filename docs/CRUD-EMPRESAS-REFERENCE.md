# 📋 Documentação de Referência: CRUD de Empresas

> **Última atualização:** Outubro 2025  
> **Projeto:** ModaFlow  
> **Versão:** 1.0

---

## 📖 Índice

1. [Visão Geral](#visão-geral)
2. [Stack Tecnológica](#stack-tecnológica)
3. [Arquitetura do Sistema](#arquitetura-do-sistema)
4. [Estrutura de Banco de Dados](#estrutura-de-banco-de-dados)
5. [Backend - Laravel](#backend---laravel)
6. [Frontend - Vue + Inertia](#frontend---vue--inertia)
7. [Sistema de Rotas (Wayfinder)](#sistema-de-rotas-wayfinder)
8. [Componentes e UI](#componentes-e-ui)
9. [Upload de Arquivos](#upload-de-arquivos)
10. [Validação de Dados](#validação-de-dados)
11. [Padrões de Código](#padrões-de-código)
12. [Checklist para Novos CRUDs](#checklist-para-novos-cruds)

---

## 🎯 Visão Geral

O CRUD de Empresas é um sistema completo de gerenciamento de empresas que implementa todas as operações básicas (Create, Read, Update, Delete) seguindo as melhores práticas de desenvolvimento full-stack.

### Funcionalidades Implementadas

- ✅ **Listagem paginada** com 15 itens por página
- ✅ **Busca em tempo real** por razão social, nome fantasia e CNPJ
- ✅ **Filtros** por status (ativo/inativo)
- ✅ **Criação** de novas empresas
- ✅ **Edição** de empresas existentes
- ✅ **Exclusão soft delete** (lógica)
- ✅ **Upload de logo** da empresa
- ✅ **Visualização** de detalhes
- ✅ **Validação** de dados no backend e frontend
- ✅ **Notificações toast** de sucesso/erro
- ✅ **Formatação automática** de CNPJ e telefone
- ✅ **Status de expiração** visual com cores

---

## 🔧 Stack Tecnológica

### Backend
- **Laravel 11** - Framework PHP
- **PostgreSQL** - Banco de dados
- **Inertia.js** - Bridge entre Laravel e Vue

### Frontend
- **Vue 3** - Framework JavaScript com Composition API
- **TypeScript** - Tipagem estática
- **TailwindCSS** - Estilização
- **shadcn/ui** - Componentes UI
- **Lucide Vue** - Ícones

### Ferramentas
- **Laravel Wayfinder** - Geração automática de rotas tipadas
- **Vite** - Build tool
- **Pest** - Testes

---

## 🏗️ Arquitetura do Sistema

### Fluxo de Dados

```
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND                              │
│                                                              │
│  ┌──────────────┐      ┌──────────────┐      ┌───────────┐ │
│  │ Vue Pages    │ ───> │ Vue Comps    │ ───> │ Inertia   │ │
│  │ (Index.vue)  │      │ (EmpresaForm)│      │ (Bridge)  │ │
│  └──────────────┘      └──────────────┘      └───────────┘ │
│         │                      │                     │       │
└─────────┼──────────────────────┼─────────────────────┼───────┘
          │                      │                     │
          └──────────────────────┴─────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────┐
│                        BACKEND                               │
│                                                              │
│  ┌──────────────┐      ┌──────────────┐      ┌───────────┐ │
│  │ Routes       │ ───> │ Controller   │ ───> │ Model     │ │
│  │ (web.php)    │      │ (Empresa)    │      │ (Empresa) │ │
│  └──────────────┘      └──────────────┘      └───────────┘ │
│         │                      │                     │       │
│         │              ┌───────┴───────┐             │       │
│         │              │               │             │       │
│         │      ┌───────▼──────┐ ┌──────▼──────┐     │       │
│         │      │ StoreRequest │ │UpdateRequest│     │       │
│         │      │ (Validation) │ │ (Validation)│     │       │
│         │      └──────────────┘ └─────────────┘     │       │
│         │                                            │       │
└─────────┼────────────────────────────────────────────┼───────┘
          │                                            │
          │                                            ▼
          │                                    ┌──────────────┐
          │                                    │  PostgreSQL  │
          │                                    │   Database   │
          │                                    └──────────────┘
          │
          ▼
┌─────────────────────────────────────────────────────────────┐
│                    LARAVEL WAYFINDER                         │
│                                                              │
│  Gera automaticamente:                                       │
│  - routes/empresas/index.ts (rotas tipadas)                 │
│  - actions/EmpresaController.ts (actions tipadas)           │
└─────────────────────────────────────────────────────────────┘
```

### Estrutura de Diretórios

```
modaflow/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── EmpresaController.php         # Controller principal
│   │   └── Requests/
│   │       ├── EmpresaStoreRequest.php       # Validação para criação
│   │       └── EmpresaUpdateRequest.php      # Validação para atualização
│   └── Models/
│       └── Empresa.php                        # Model Eloquent
│
├── database/
│   └── migrations/
│       └── 2025_10_13_183601_create_empresas_table.php
│
├── resources/
│   └── js/
│       ├── actions/
│       │   └── App/Http/Controllers/
│       │       └── EmpresaController.ts       # Actions geradas (Wayfinder)
│       ├── components/
│       │   └── empresas/
│       │       ├── EmpresaForm.vue            # Formulário reutilizável
│       │       └── FormActions.vue            # Botões de ação
│       ├── pages/
│       │   └── empresas/
│       │       ├── Index.vue                  # Listagem
│       │       ├── Create.vue                 # Criação
│       │       └── Edit.vue                   # Edição
│       └── routes/
│           └── empresas/
│               └── index.ts                   # Rotas geradas (Wayfinder)
│
├── routes/
│   └── web.php                                # Definição de rotas Laravel
│
└── public/
    └── storage/
        └── empresas/
            └── logos/                         # Logos das empresas
```

---

## 💾 Estrutura de Banco de Dados

### Migration

**Arquivo:** `database/migrations/2025_10_13_183601_create_empresas_table.php`

```php
Schema::create('multitenancy.empresas', function (Blueprint $table) {
    // Chave primária e identificador único
    $table->id();
    $table->uuid('uuid')->unique()->default(DB::raw('gen_random_uuid()'));
    
    // Dados cadastrais
    $table->string('razao_social', 255);
    $table->string('nome_fantasia', 255);
    $table->string('cnpj', 18)->nullable()->unique();
    $table->string('email', 255);
    $table->string('telefone', 20)->nullable();
    $table->string('logo_path', 255)->nullable();
    
    // Status e controle
    $table->boolean('ativo')->default(true);
    $table->timestampTz('data_adesao')->default(DB::raw('CURRENT_TIMESTAMP'));
    $table->timestampTz('data_expiracao')->nullable();
    
    // Timestamps padrão
    $table->timestampsTz(); // created_at, updated_at
    $table->softDeletes();  // deleted_at
    
    // Índices para performance
    $table->index('ativo');
    $table->index('cnpj');
    $table->index('email');
});
```

### Características do Schema

1. **Schema Personalizado:** `multitenancy` (para suporte futuro a multi-tenancy)
2. **UUID:** Gerado automaticamente pelo PostgreSQL
3. **Soft Deletes:** Exclusão lógica ao invés de física
4. **Timestamps com Timezone:** Para suporte internacional
5. **Índices:** Em campos frequentemente consultados

---

## ⚙️ Backend - Laravel

### 1. Model (Empresa.php)

**Arquivo:** `app/Models/Empresa.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'empresas';

    protected $fillable = [
        'uuid',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'email',
        'logo_path',
        'telefone',
        'ativo',
        'data_adesao',
        'data_expiracao',
    ];

    protected $appends = ['logo_url'];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'ativo' => 'boolean',
            'data_adesao' => 'datetime',
            'data_expiracao' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Accessor para URL da logo
    public function getLogoUrlAttribute()
    {
        return $this->logo_path 
            ? asset('storage/' . $this->logo_path) 
            : null;
    }

    // Query Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativas($query)
    {
        return $query->where('ativo', false);
    }
}
```

**Características importantes:**

- ✅ **SoftDeletes** para exclusão lógica
- ✅ **Casts** para conversão automática de tipos
- ✅ **Accessor** `logo_url` para gerar URL pública
- ✅ **Appends** para incluir `logo_url` automaticamente
- ✅ **Query Scopes** para filtros reutilizáveis

### 2. Controller (EmpresaController.php)

**Arquivo:** `app/Http/Controllers/EmpresaController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmpresaStoreRequest;
use App\Http\Requests\EmpresaUpdateRequest;
use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class EmpresaController extends Controller
{
    /**
     * Display a listing of empresas.
     */
    public function index(Request $request): Response
    {
        $query = Empresa::query();

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Busca por razão social, nome fantasia ou CNPJ
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('razao_social', 'ilike', "%{$search}%")
                    ->orWhere('nome_fantasia', 'ilike', "%{$search}%")
                    ->orWhere('cnpj', 'like', "%{$search}%");
            });
        }

        $empresas = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('empresas/Index', [
            'empresas' => $empresas,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new empresa.
     */
    public function create(): Response
    {
        return Inertia::render('empresas/Create');
    }

    /**
     * Store a newly created empresa.
     */
    public function store(EmpresaStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Processa o upload da imagem se existir
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')
                ->store('empresas/logos', 'public');
        }

        Empresa::create($data);

        return to_route('empresas.index')
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    /**
     * Display the specified empresa.
     */
    public function show(Empresa $empresa): Response
    {
        return Inertia::render('empresas/Show', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * Show the form for editing the specified empresa.
     */
    public function edit(Empresa $empresa): Response
    {
        return Inertia::render('empresas/Edit', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * Update the specified empresa.
     */
    public function update(EmpresaUpdateRequest $request, Empresa $empresa): RedirectResponse
    {
        $data = $request->validated();
        
        // Processa o upload da imagem se existir
        if ($request->hasFile('logo')) {
            // Remove a imagem anterior se existir
            if ($empresa->logo_path && Storage::disk('public')->exists($empresa->logo_path)) {
                Storage::disk('public')->delete($empresa->logo_path);
            }
            
            $data['logo_path'] = $request->file('logo')
                ->store('empresas/logos', 'public');
        }

        $empresa->update($data);

        return to_route('empresas.index')
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    /**
     * Remove the specified empresa (soft delete).
     */
    public function destroy(Empresa $empresa): RedirectResponse
    {
        // Armazena o caminho da logo antes de excluir a empresa
        $logoPath = $empresa->logo_path;
        
        $empresa->delete();
        
        // Remove a imagem após excluir a empresa
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            Storage::disk('public')->delete($logoPath);
        }

        return to_route('empresas.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }
}
```

**Padrões do Controller:**

- ✅ **Resource Controller** completo (index, create, store, show, edit, update, destroy)
- ✅ **Type hints** em todos os métodos
- ✅ **Form Requests** para validação
- ✅ **Query Builders** eficientes
- ✅ **Paginação** com query string preservada
- ✅ **Upload de arquivos** com gerenciamento de storage
- ✅ **Flash messages** para feedback
- ✅ **Case insensitive search** com `ilike` (PostgreSQL)

### 3. Form Requests (Validação)

#### EmpresaStoreRequest.php

**Arquivo:** `app/Http/Requests/EmpresaStoreRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpresaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
                Rule::unique('empresas', 'cnpj')->whereNull('deleted_at'),
            ],
            'email' => ['required', 'email', 'max:255'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'ativo' => ['boolean'],
            'data_adesao' => ['nullable', 'date'],
            'data_expiracao' => ['nullable', 'date', 'after:data_adesao'],
        ];
    }

    public function messages(): array
    {
        return [
            'razao_social.required' => 'A razão social é obrigatória.',
            'razao_social.max' => 'A razão social não pode ter mais de 255 caracteres.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'nome_fantasia.max' => 'O nome fantasia não pode ter mais de 255 caracteres.',
            'cnpj.regex' => 'O CNPJ deve estar no formato XX.XXX.XXX/XXXX-XX.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'data_expiracao.after' => 'A data de expiração deve ser posterior à data de adesão.',
            'logo.file' => 'O logo deve ser um arquivo.',
            'logo.mimes' => 'A logo deve ser um arquivo do tipo: jpeg, png, jpg, gif ou webp.',
            'logo.max' => 'A logo não pode ser maior que 2MB.',
        ];
    }
}
```

#### EmpresaUpdateRequest.php

**Arquivo:** `app/Http/Requests/EmpresaUpdateRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpresaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = $this->route('empresa');

        return [
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
                Rule::unique('empresas', 'cnpj')
                    ->ignore($empresaId)
                    ->whereNull('deleted_at'),
            ],
            'email' => ['required', 'email', 'max:255'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'ativo' => ['boolean'],
            'data_adesao' => ['nullable', 'date'],
            'data_expiracao' => ['nullable', 'date', 'after:data_adesao'],
        ];
    }

    public function messages(): array
    {
        return [
            'razao_social.required' => 'A razão social é obrigatória.',
            'razao_social.max' => 'A razão social não pode ter mais de 255 caracteres.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'nome_fantasia.max' => 'O nome fantasia não pode ter mais de 255 caracteres.',
            'cnpj.regex' => 'O CNPJ deve estar no formato XX.XXX.XXX/XXXX-XX.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'data_expiracao.after' => 'A data de expiração deve ser posterior à data de adesão.',
            'logo.file' => 'O logo deve ser um arquivo.',
            'logo.mimes' => 'A logo deve ser um arquivo do tipo: jpeg, png, jpg, gif ou webp.',
            'logo.max' => 'A logo não pode ser maior que 2MB.',
        ];
    }
}
```

**Diferenças entre Store e Update:**

- ✅ Update usa `ignore($empresaId)` na validação de CNPJ único
- ✅ Ambos respeitam soft deletes com `whereNull('deleted_at')`
- ✅ Mensagens de erro personalizadas em português

### 4. Rotas (web.php)

**Arquivo:** `routes/web.php`

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('empresas', EmpresaController::class);
});
```

**Rotas geradas automaticamente:**

| Método | URI                        | Nome              | Action    |
|--------|----------------------------|-------------------|-----------|
| GET    | /empresas                  | empresas.index    | index     |
| GET    | /empresas/create           | empresas.create   | create    |
| POST   | /empresas                  | empresas.store    | store     |
| GET    | /empresas/{empresa}        | empresas.show     | show      |
| GET    | /empresas/{empresa}/edit   | empresas.edit     | edit      |
| PUT    | /empresas/{empresa}        | empresas.update   | update    |
| DELETE | /empresas/{empresa}        | empresas.destroy  | destroy   |

---

## 🎨 Frontend - Vue + Inertia

### 1. Página de Listagem (Index.vue)

**Arquivo:** `resources/js/pages/empresas/Index.vue`

**Estrutura:**

```vue
<script setup lang="ts">
// Imports
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { index as empresasIndex, create as empresasCreate } from '@/routes/empresas';
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

// Interfaces TypeScript
interface Empresa {
    id: number;
    uuid: string;
    razao_social: string;
    nome_fantasia: string;
    cnpj: string | null;
    email: string;
    logo_path: string | null;
    logo_url: string | null;
    telefone: string | null;
    ativo: boolean;
    data_adesao: string;
    data_expiracao: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginatedEmpresas {
    data: Empresa[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

interface Props {
    empresas: PaginatedEmpresas;
    filters: {
        search?: string;
        status?: string;
    };
}

// State
const props = defineProps<Props>();
const page = usePage();
const toast = useToast();
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');
const showDeleteDialog = ref(false);
const empresaToDelete = ref<Empresa | null>(null);

// Watchers para filtros em tempo real
watch([search, status], () => {
    router.get(
        empresasIndex().url,
        {
            search: search.value || undefined,
            status: status.value !== 'all' ? status.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, { debounce: 300 });

// Métodos auxiliares
const formatDate = (date: string | null) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString('pt-BR');
};

const getExpirationStatus = (date: string | null) => {
    if (!date) return 'none';
    
    const expirationDate = new Date(date);
    const today = new Date();
    const diffTime = expirationDate.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) return 'expired';
    if (diffDays <= 30) return 'near';
    return 'ok';
};
</script>

<template>
    <Head title="Empresas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header -->
        <!-- Filters Card -->
        <!-- Table Card -->
        <!-- Pagination -->
        <!-- Delete Dialog -->
    </AppLayout>
</template>
```

**Características principais:**

- ✅ **TypeScript completo** com interfaces
- ✅ **Busca em tempo real** com debounce de 300ms
- ✅ **Filtros preservados** na URL (query string)
- ✅ **Paginação** integrada
- ✅ **Dialog de confirmação** para exclusão
- ✅ **Status visual** de expiração com cores
- ✅ **Flash messages** com toast
- ✅ **Responsive design**

### 2. Página de Criação (Create.vue)

**Arquivo:** `resources/js/pages/empresas/Create.vue`

```vue
<script setup lang="ts">
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { index as empresasIndex } from '@/routes/empresas';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import EmpresaForm from '@/components/empresas/EmpresaForm.vue';
import FormActions from '@/components/empresas/FormActions.vue';

const toast = useToast();

const form = useForm({
    razao_social: '',
    nome_fantasia: '',
    cnpj: '',
    email: '',
    logo: null as File | null,
    telefone: '',
    ativo: 1,
    data_adesao: new Date().toISOString().split('T')[0],
    data_expiracao: '',
});

const handleSubmit = () => {
    form.post(EmpresaController.store().url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Empresa cadastrada com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao cadastrar empresa', 'Verifique os campos e tente novamente.');
        },
    });
};
</script>

<template>
    <Head title="Nova Empresa" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <EmpresaForm 
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :isCreate="true"
                />
                
                <FormActions 
                    :processing="form.processing"
                    :isCreate="true"
                />
            </form>
        </div>
    </AppLayout>
</template>
```

**Características:**

- ✅ **useForm** do Inertia para gerenciamento de estado
- ✅ **forceFormData** para suporte a upload de arquivos
- ✅ **Valores padrão** (ativo: true, data_adesao: hoje)
- ✅ **Componentes reutilizáveis** (EmpresaForm, FormActions)

### 3. Página de Edição (Edit.vue)

**Arquivo:** `resources/js/pages/empresas/Edit.vue`

```vue
<script setup lang="ts">
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { index as empresasIndex } from '@/routes/empresas';
import { Form, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import EmpresaForm from '@/components/empresas/EmpresaForm.vue';
import FormActions from '@/components/empresas/FormActions.vue';

interface Empresa {
    id: number;
    uuid: string;
    razao_social: string;
    nome_fantasia: string;
    cnpj: string | null;
    email: string;
    telefone: string | null;
    ativo: boolean;
    data_adesao: string;
    data_expiracao: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    empresa: Empresa;
}

const props = defineProps<Props>();
const toast = useToast();
</script>

<template>
    <Head :title="`Editar ${empresa.razao_social}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Info Card (UUID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">UUID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ empresa.uuid }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(empresa.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="EmpresaController.update.form(empresa)"
                enctype="multipart/form-data"
                :options="{ preserveScroll: true }"
                @success="toast.success('Empresa atualizada com sucesso!')"
                @error="toast.error('Erro ao atualizar empresa')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <EmpresaForm 
                    :empresa="empresa"
                    :errors="errors"
                    :processing="processing"
                />
                
                <FormActions 
                    :processing="processing"
                    :recentlySuccessful="recentlySuccessful"
                />
            </Form>
        </div>
    </AppLayout>
</template>
```

**Características:**

- ✅ **Form component** do Inertia com Wayfinder
- ✅ **Info Card** com UUID e data de cadastro
- ✅ **Slots** para acesso a errors e processing
- ✅ **Feedback visual** de salvamento recente

### 4. Componente de Formulário (EmpresaForm.vue)

**Arquivo:** `resources/js/components/empresas/EmpresaForm.vue`

Este componente é reutilizado em Create e Edit.

**Características principais:**

```vue
<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';

// Props
interface Props {
    empresa?: Empresa;
    form?: any;
    errors: Record<string, string>;
    processing: boolean;
    isCreate?: boolean;
}

// Formatação de CNPJ
const formatCNPJ = (value: string) => {
    const digits = value.replace(/\D/g, '');
    if (digits.length <= 2) return digits;
    if (digits.length <= 5) return `${digits.slice(0, 2)}.${digits.slice(2)}`;
    if (digits.length <= 8) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5)}`;
    if (digits.length <= 12) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8)}`;
    return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12, 14)}`;
};

// Formatação de telefone
const formatPhone = (value: string) => {
    const digits = value.replace(/\D/g, '');
    if (digits.length <= 2) return digits;
    if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
};

// Upload de logo com preview
const handleLogoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        logoFile.value = file;
        
        if (props.form) {
            props.form.logo = file;
        }
        
        logoPreview.value = URL.createObjectURL(file);
    }
};
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle>Dados da Empresa</CardTitle>
            <CardDescription>
                {{ isCreate ? 'Informe os dados cadastrais da empresa' : 'Atualize os dados cadastrais da empresa' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Campos do formulário -->
        </CardContent>
    </Card>
</template>
```

**Funcionalidades:**

- ✅ **Formatação automática** de CNPJ e telefone
- ✅ **Upload de imagem** com preview
- ✅ **Switch** para ativo/inativo
- ✅ **Validação visual** de erros
- ✅ **Suporte a useForm e Form component**
- ✅ **Responsivo**

### 5. Componente de Actions (FormActions.vue)

**Arquivo:** `resources/js/components/empresas/FormActions.vue`

```vue
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Link } from '@inertiajs/vue3';
import { index as empresasIndex } from '@/routes/empresas';

interface Props {
    processing: boolean;
    isCreate?: boolean;
    recentlySuccessful?: boolean;
}
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardContent class="pt-6">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:items-center">
                <Link :href="empresasIndex().url">
                    <Button type="button" variant="outline" :disabled="processing">
                        Cancelar
                    </Button>
                </Link>
                
                <Transition v-if="!isCreate">
                    <p v-show="recentlySuccessful" class="text-sm font-medium text-green-600">
                        ✓ Salvo com sucesso
                    </p>
                </Transition>

                <Button type="submit" :disabled="processing">
                    <span v-if="!processing">
                        {{ isCreate ? 'Cadastrar Empresa' : 'Salvar Alterações' }}
                    </span>
                    <span v-else>
                        Salvando...
                    </span>
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
```

**Características:**

- ✅ **Botão de cancelar** com navegação via Inertia
- ✅ **Feedback de salvamento** com transition
- ✅ **Estados de loading** durante processamento
- ✅ **Textos dinâmicos** para criar/editar

---

## 🛣️ Sistema de Rotas (Wayfinder)

O Laravel Wayfinder gera automaticamente rotas e actions tipadas a partir das rotas do Laravel.

### Rotas Geradas

**Arquivo:** `resources/js/routes/empresas/index.ts`

```typescript
// Exemplo de uso
import { index, create, store, edit, update, destroy } from '@/routes/empresas';

// Listagem
index().url // '/empresas'

// Criação
create().url // '/empresas/create'

// Store
store().url // '/empresas'

// Edição
edit(empresa).url // '/empresas/{id}/edit'

// Update
update(empresa).url // '/empresas/{id}'

// Delete
destroy(empresa).url // '/empresas/{id}'
```

### Actions Geradas

**Arquivo:** `resources/js/actions/App/Http/Controllers/EmpresaController.ts`

```typescript
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';

// Uso em componentes
<Link :href="EmpresaController.edit(empresa).url">
    Editar
</Link>

// Com Form component
<Form v-bind="EmpresaController.update.form(empresa)">
    <!-- campos -->
</Form>

// Com Form component para delete
<Form v-bind="EmpresaController.destroy.form(empresa)">
    <Button type="submit">Excluir</Button>
</Form>
```

**Vantagens:**

- ✅ **Type-safe** - Detecta erros em tempo de desenvolvimento
- ✅ **Autocomplete** - IDE sugere métodos e parâmetros
- ✅ **Sincronizado** - Gerado automaticamente das rotas Laravel
- ✅ **Documentação integrada** - JSDoc com links para o controller

---

## 🎨 Componentes e UI

### Biblioteca de Componentes

Usamos **shadcn/ui** - componentes baseados em Radix UI + TailwindCSS:

#### Componentes utilizados no CRUD:

1. **Card** - Containers com sombra e borda
2. **Table** - Tabelas responsivas
3. **Button** - Botões com variantes (default, outline, ghost, destructive)
4. **Input** - Campos de texto
5. **Label** - Labels de formulário
6. **Badge** - Tags de status
7. **Dialog** - Modais de confirmação
8. **Switch** - Toggle switch
9. **Breadcrumb** - Navegação de página

### Layout

**AppLayout** - Layout principal da aplicação com:
- Header
- Sidebar
- Breadcrumbs
- Content area

### Ícones

Usamos **lucide-vue-next**:
- `Plus` - Adicionar
- `Search` - Busca
- `Pencil` - Editar
- `Trash2` - Excluir
- `Loader2` - Loading

---

## 📤 Upload de Arquivos

### Backend

```php
// Storage
if ($request->hasFile('logo')) {
    $data['logo_path'] = $request->file('logo')
        ->store('empresas/logos', 'public');
}

// Delete
if ($empresa->logo_path && Storage::disk('public')->exists($empresa->logo_path)) {
    Storage::disk('public')->delete($empresa->logo_path);
}
```

### Frontend

```vue
<!-- Input -->
<Input
    type="file"
    accept="image/png, image/jpeg, image/jpg, image/gif"
    @change="handleLogoUpload"
/>

<!-- Handler -->
const handleLogoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        props.form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

<!-- Preview -->
<img :src="logoPreview" alt="Logo da empresa" />
```

### Inertia

```typescript
// useForm
form.post(url, {
    forceFormData: true,  // Força envio como FormData
});

// Form component
<Form enctype="multipart/form-data">
```

**Configurações:**

- ✅ Tamanho máximo: **2MB**
- ✅ Formatos aceitos: **jpeg, png, jpg, gif, webp**
- ✅ Storage disk: **public**
- ✅ Pasta: **empresas/logos/**

---

## ✅ Validação de Dados

### Backend (Form Requests)

```php
// Regras
'cnpj' => [
    'nullable',
    'string',
    'max:18',
    'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
    Rule::unique('empresas', 'cnpj')
        ->ignore($empresaId) // Apenas no Update
        ->whereNull('deleted_at'),
],

// Mensagens personalizadas
'cnpj.regex' => 'O CNPJ deve estar no formato XX.XXX.XXX/XXXX-XX.',
'cnpj.unique' => 'Este CNPJ já está cadastrado.',
```

### Frontend

```vue
<!-- Exibição de erros -->
<InputError :message="errors.cnpj" />

<!-- Validação visual -->
<Input
    v-model="form.cnpj"
    :class="{ 'border-red-500': errors.cnpj }"
/>
```

### Tipos de Validação

1. **Campo obrigatório** - required
2. **Tamanho máximo** - max:255
3. **Formato** - regex, email
4. **Unicidade** - unique (respeitando soft deletes)
5. **Tipo de arquivo** - mimes, max (tamanho)
6. **Data** - date, after

---

## 📐 Padrões de Código

### PHP (Laravel)

```php
// ✅ Type hints em todos os métodos
public function index(Request $request): Response

// ✅ Docblocks descritivos
/**
 * Display a listing of empresas.
 */

// ✅ Query builders eficientes
$query->where('ativo', true)->latest()->paginate(15);

// ✅ Form Requests para validação
public function store(EmpresaStoreRequest $request)

// ✅ Flash messages
return to_route('empresas.index')
    ->with('success', 'Empresa cadastrada com sucesso!');
```

### TypeScript (Vue)

```typescript
// ✅ Interfaces para props e data
interface Empresa {
    id: number;
    razao_social: string;
    // ...
}

// ✅ Type hints em funções
const formatDate = (date: string | null): string | null => {
    // ...
}

// ✅ Composition API
const search = ref<string>('');

// ✅ Imports organizados
import { ref, watch, onMounted } from 'vue';
```

### Vue Templates

```vue
<!-- ✅ Estrutura organizada -->
<template>
    <Head title="..." />
    <AppLayout>
        <!-- Header -->
        <!-- Filters -->
        <!-- Content -->
        <!-- Dialogs -->
    </AppLayout>
</template>

<!-- ✅ Condicionais e loops -->
<TableRow v-for="empresa in empresas.data" :key="empresa.id">
    <!-- ... -->
</TableRow>

<!-- ✅ Event handlers -->
@click="openDeleteDialog(empresa)"
@submit.prevent="handleSubmit"
```

### CSS (TailwindCSS)

```vue
<!-- ✅ Classes utilitárias -->
<div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">

<!-- ✅ Responsive -->
<div class="grid gap-4 md:grid-cols-2">

<!-- ✅ States -->
<Button :disabled="processing" class="hover:bg-indigo-700">
```

---

## ✅ Checklist para Novos CRUDs

### Backend

- [ ] **Migration**
  - [ ] Criar migration com schema correto
  - [ ] Definir tipos de dados apropriados
  - [ ] Adicionar índices para campos consultados
  - [ ] Incluir soft deletes se necessário
  - [ ] Timestamps com timezone

- [ ] **Model**
  - [ ] Criar model Eloquent
  - [ ] Definir $fillable
  - [ ] Adicionar $casts para conversão de tipos
  - [ ] Criar accessors se necessário
  - [ ] Adicionar scopes úteis
  - [ ] Configurar $appends para accessors

- [ ] **Form Requests**
  - [ ] Criar StoreRequest
  - [ ] Criar UpdateRequest
  - [ ] Definir regras de validação
  - [ ] Adicionar mensagens personalizadas em português
  - [ ] Tratar unique com soft deletes
  - [ ] Validar uploads de arquivos

- [ ] **Controller**
  - [ ] Criar controller resource
  - [ ] Implementar index com:
    - [ ] Paginação
    - [ ] Filtros
    - [ ] Busca
    - [ ] Query string preservada
  - [ ] Implementar create
  - [ ] Implementar store com:
    - [ ] Upload de arquivos
    - [ ] Flash message
  - [ ] Implementar show
  - [ ] Implementar edit
  - [ ] Implementar update com:
    - [ ] Upload de arquivos
    - [ ] Remoção de arquivos antigos
    - [ ] Flash message
  - [ ] Implementar destroy com:
    - [ ] Soft delete
    - [ ] Remoção de arquivos
    - [ ] Flash message
  - [ ] Type hints em todos os métodos
  - [ ] Docblocks

- [ ] **Routes**
  - [ ] Adicionar Route::resource em web.php
  - [ ] Proteger com middleware de autenticação

### Frontend

- [ ] **Types/Interfaces**
  - [ ] Criar interfaces TypeScript para model
  - [ ] Criar interface para props
  - [ ] Criar interface para paginated data

- [ ] **Páginas**
  - [ ] **Index.vue**
    - [ ] Header com título e botão de criar
    - [ ] Card de filtros
    - [ ] Tabela com dados
    - [ ] Paginação
    - [ ] Dialog de confirmação de exclusão
    - [ ] Watchers para filtros em tempo real
    - [ ] Flash messages
  - [ ] **Create.vue**
    - [ ] Header
    - [ ] Formulário
    - [ ] Actions (cancelar/salvar)
    - [ ] Breadcrumbs
  - [ ] **Edit.vue**
    - [ ] Header
    - [ ] Card com info (UUID, data de cadastro)
    - [ ] Formulário
    - [ ] Actions (cancelar/salvar)
    - [ ] Feedback de salvamento recente
    - [ ] Breadcrumbs

- [ ] **Componentes**
  - [ ] Form.vue reutilizável
    - [ ] Suporte a create e edit
    - [ ] Formatação de campos (se necessário)
    - [ ] Upload com preview
    - [ ] Validação visual de erros
  - [ ] FormActions.vue
    - [ ] Botão cancelar
    - [ ] Botão salvar/criar
    - [ ] Loading states
    - [ ] Feedback visual

- [ ] **Funcionalidades**
  - [ ] Busca em tempo real com debounce
  - [ ] Filtros com query string preservada
  - [ ] Upload de arquivos com preview
  - [ ] Formatação de campos (CNPJ, telefone, CPF, etc)
  - [ ] Toast notifications
  - [ ] Loading states
  - [ ] Responsive design
  - [ ] Acessibilidade (ARIA labels)

- [ ] **Wayfinder**
  - [ ] Rotas geradas automaticamente
  - [ ] Actions geradas automaticamente
  - [ ] Imports corretos

### Testes

- [ ] Criar testes para:
  - [ ] Listagem
  - [ ] Criação
  - [ ] Edição
  - [ ] Exclusão
  - [ ] Validações
  - [ ] Upload de arquivos

### Documentação

- [ ] Atualizar DATABASE-SCHEMA.md com nova tabela
- [ ] Documentar relacionamentos (se houver)
- [ ] Adicionar comentários em código complexo

---

## 🎓 Boas Práticas Implementadas

### 1. **Separação de Responsabilidades**
- Controllers focados em orquestração
- Form Requests para validação
- Models para lógica de negócio
- Componentes reutilizáveis no frontend

### 2. **Type Safety**
- TypeScript no frontend
- Type hints no backend
- Interfaces bem definidas

### 3. **User Experience**
- Feedback visual imediato
- Loading states
- Notificações toast
- Confirmação de ações destrutivas
- Formatação automática de campos

### 4. **Performance**
- Paginação
- Índices no banco de dados
- Query string preservada
- Lazy loading de componentes

### 5. **Segurança**
- Validação no backend e frontend
- CSRF protection
- Autenticação e autorização
- Sanitização de uploads

### 6. **Manutenibilidade**
- Código organizado e comentado
- Componentes reutilizáveis
- Padrões consistentes
- Documentação completa

### 7. **Acessibilidade**
- Labels descritivos
- ARIA attributes
- Navegação por teclado
- Contraste de cores adequado

---

## 📚 Recursos Adicionais

### Documentação Oficial

- [Laravel 11](https://laravel.com/docs/11.x)
- [Vue 3](https://vuejs.org/)
- [Inertia.js](https://inertiajs.com/)
- [TypeScript](https://www.typescriptlang.org/)
- [TailwindCSS](https://tailwindcss.com/)
- [shadcn/ui](https://ui.shadcn.com/)
- [Laravel Wayfinder](https://github.com/laravel/wayfinder)

### Arquivos de Referência no Projeto

- `docs/CRUD-PATTERN.md` - Padrões gerais de CRUD
- `docs/DATABASE-SCHEMA.md` - Schema do banco de dados
- `docs/FRONTEND-ARCHITECTURE.md` - Arquitetura do frontend
- `docs/DEVELOPMENT-GUIDE.md` - Guia de desenvolvimento

---

## 🔄 Como Usar Esta Documentação

### Para Criar um Novo CRUD

1. **Leia esta documentação completa**
2. **Siga o checklist** seção por seção
3. **Copie e adapte** os arquivos do CRUD de Empresas
4. **Ajuste** para suas necessidades específicas
5. **Teste** todas as funcionalidades
6. **Documente** suas alterações

### Para IAs/Assistentes

Esta documentação foi criada especificamente para servir como referência completa para criação de novos CRUDs. Ao receber uma solicitação para criar um novo CRUD:

1. **Analise** os requisitos específicos
2. **Compare** com o padrão estabelecido aqui
3. **Replique** a estrutura e padrões
4. **Adapte** para o novo contexto
5. **Mantenha** a consistência com este documento

---

## 📝 Notas Finais

- **Mantenha a consistência:** Todos os CRUDs devem seguir o mesmo padrão
- **Atualize a documentação:** Se fizer melhorias, documente-as
- **Questione quando necessário:** Se algo não faz sentido, pergunte
- **Teste sempre:** Não confie apenas no código, teste na prática

---

**Documentação criada por:** Sistema ModaFlow  
**Data:** Outubro 2025  
**Versão:** 1.0

---

_Esta é uma documentação viva. Mantenha-a atualizada conforme o projeto evolui._

