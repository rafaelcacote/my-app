# Padrão CRUD do Sistema

Este documento detalha os padrões e convenções para implementação de operações CRUD (Create, Read, Update, Delete) no sistema.

## 📋 Índice

1. [Estrutura Geral](#estrutura-geral)
2. [Models](#models)
3. [Controllers](#controllers)
4. [Routes](#routes)
5. [Form Requests](#form-requests)
6. [Validações](#validações)
7. [Relacionamentos](#relacionamentos)
8. [Exemplo Completo](#exemplo-completo)

## Estrutura Geral

### Arquitetura MVC com Inertia

```
Backend (Laravel)           Frontend (Vue 3)
├── Model                  ├── Page Component
├── Controller             ├── Actions (Wayfinder)
├── Form Request           └── Types
└── Routes
```

### Fluxo de Dados

```
User Action → Route → Controller → Model → Database
                ↓           ↓
            Validation   Inertia Response
                ↓
            Vue Page Component
```

## Models

### Estrutura Base

Os models seguem o padrão Eloquent do Laravel, localizados em `app/Models/`.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

### Convenções de Models

#### Nomenclatura
- **Nome**: Singular, PascalCase (ex: `User`, `Product`, `OrderItem`)
- **Tabela**: Automaticamente plural (ex: `users`, `products`, `order_items`)
- **Namespace**: `App\Models`

#### Propriedades Essenciais

**$fillable**: Lista de campos que podem ser preenchidos via mass assignment
```php
protected $fillable = ['name', 'email', 'description'];
```

**$hidden**: Campos que não devem aparecer em JSON (senhas, tokens)
```php
protected $hidden = ['password', 'remember_token'];
```

**$casts**: Conversão automática de tipos
```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'metadata' => 'array',
    ];
}
```

#### Traits Comuns

- `HasFactory` - Suporte a factories para testes
- `Notifiable` - Habilita notificações
- `SoftDeletes` - Soft delete (deleção lógica)
- `TwoFactorAuthenticatable` - Autenticação 2FA (Laravel Fortify)

### Timestamps Automáticos

Por padrão, Laravel gerencia automaticamente `created_at` e `updated_at`:

```php
// Habilitar (padrão)
public $timestamps = true;

// Desabilitar se necessário
public $timestamps = false;
```

## Controllers

### Estrutura Base

Controllers são organizados em `app/Http/Controllers/` por funcionalidade.

```php
<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

### Convenções de Controllers

#### Nomenclatura
- **Nome**: Singular, sufixo "Controller" (ex: `ProfileController`, `ProductController`)
- **Namespace**: Organizado por contexto
  - `App\Http\Controllers\Auth` - Autenticação
  - `App\Http\Controllers\Settings` - Configurações
  - `App\Http\Controllers` - Controllers gerais

#### Métodos CRUD Padrão

| Método | Ação | Retorno | Descrição |
|--------|------|---------|-----------|
| `index()` | GET | `Inertia\Response` | Lista todos os recursos |
| `create()` | GET | `Inertia\Response` | Exibe formulário de criação |
| `store()` | POST | `RedirectResponse` | Salva novo recurso |
| `show()` | GET | `Inertia\Response` | Exibe um recurso específico |
| `edit()` | GET | `Inertia\Response` | Exibe formulário de edição |
| `update()` | PUT/PATCH | `RedirectResponse` | Atualiza recurso existente |
| `destroy()` | DELETE | `RedirectResponse` | Remove recurso |

#### Type Hints e Return Types

Sempre use type hints e declare return types:

```php
// ✅ Correto
public function edit(Request $request): Response
{
    return Inertia::render('settings/Profile');
}

// ❌ Incorreto
public function edit($request)
{
    return Inertia::render('settings/Profile');
}
```

#### Inertia Responses

Para páginas que renderizam componentes Vue:

```php
return Inertia::render('ComponentName', [
    'prop1' => $value1,
    'prop2' => $value2,
]);
```

#### Redirects

Use helpers do Laravel para redirects:

```php
// Para rota nomeada
return to_route('profile.edit');

// Para URL específica
return redirect('/dashboard');

// Com flash message
return back()->with('success', 'Profile updated!');
```

### DocBlocks

Sempre documente métodos públicos:

```php
/**
 * Update the user's profile information.
 */
public function update(ProfileUpdateRequest $request): RedirectResponse
{
    // ...
}
```

## Routes

### Organização de Rotas

Rotas são organizadas em arquivos separados em `routes/`:

- `web.php` - Rotas principais da aplicação
- `auth.php` - Rotas de autenticação
- `settings.php` - Rotas de configurações
- `api.php` - Rotas de API (se necessário)

### Estrutura de Rotas

#### Rotas Básicas

```php
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

// GET route
Route::get('settings/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

// POST route
Route::patch('settings/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

// DELETE route
Route::delete('settings/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');
```

#### Resource Routes

Para CRUD completo, use resource routes:

```php
Route::resource('products', ProductController::class);

// Gera automaticamente:
// GET    /products              -> index
// GET    /products/create       -> create
// POST   /products              -> store
// GET    /products/{product}    -> show
// GET    /products/{product}/edit -> edit
// PUT    /products/{product}    -> update
// DELETE /products/{product}    -> destroy
```

#### Route Groups

Agrupe rotas com middleware ou prefixo comum:

```php
Route::middleware('auth')->group(function () {
    Route::get('settings/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    Route::patch('settings/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});
```

#### Middleware

Aplicar middleware para proteção de rotas:

```php
// Requer autenticação
Route::middleware('auth')->group(function () {
    // Rotas protegidas
});

// Apenas guests (não autenticados)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])
        ->name('login');
});

// Múltiplos middlewares
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
```

### Nomenclatura de Rotas

#### Convenções de Nomes

- Use **kebab-case** para URLs: `/settings/profile`, `/two-factor-auth`
- Use **dot notation** para nomes de rotas: `profile.edit`, `products.index`
- Seja descritivo e consistente

#### Padrões de Nomes

| Ação | Pattern | Exemplo |
|------|---------|---------|
| Listar | `{resource}.index` | `products.index` |
| Criar | `{resource}.create` | `products.create` |
| Salvar | `{resource}.store` | `products.store` |
| Ver | `{resource}.show` | `products.show` |
| Editar | `{resource}.edit` | `products.edit` |
| Atualizar | `{resource}.update` | `products.update` |
| Deletar | `{resource}.destroy` | `products.destroy` |

## Form Requests

### Estrutura

Form Requests encapsulam lógica de validação em classes dedicadas.

Localização: `app/Http/Requests/`

```php
<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
```

### Quando Usar Form Requests

**Use Form Request quando:**
- Validação complexa com múltiplas regras
- Reutilização de validação em vários lugares
- Necessidade de autorização customizada
- Validação com lógica de negócio

**Use validação inline quando:**
- Validação simples e única
- 1-3 campos apenas
- Validação pontual

### Autorização

Adicione lógica de autorização:

```php
/**
 * Determine if the user is authorized to make this request.
 */
public function authorize(): bool
{
    // Verificar se user pode editar o recurso
    return $this->user()->can('update', $this->product);
}
```

### Mensagens Customizadas

```php
/**
 * Get custom validation messages.
 */
public function messages(): array
{
    return [
        'email.required' => 'O email é obrigatório',
        'email.unique' => 'Este email já está em uso',
    ];
}
```

## Validações

### Regras Comuns

#### Campos de Texto

```php
'name' => ['required', 'string', 'max:255'],
'description' => ['nullable', 'string', 'max:1000'],
'slug' => ['required', 'string', 'alpha_dash', 'unique:products'],
```

#### Email

```php
'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
```

#### Password

```php
use Illuminate\Validation\Rules\Password;

'password' => ['required', 'confirmed', Password::defaults()],
'current_password' => ['required', 'current_password'],
```

#### Números

```php
'age' => ['required', 'integer', 'min:18', 'max:100'],
'price' => ['required', 'numeric', 'min:0'],
'quantity' => ['required', 'integer', 'between:1,999'],
```

#### Booleanos

```php
'is_active' => ['required', 'boolean'],
'terms_accepted' => ['accepted'], // deve ser true, 'yes', 1, 'on'
```

#### Datas

```php
'birth_date' => ['required', 'date', 'before:today'],
'event_date' => ['required', 'date', 'after:tomorrow'],
'deadline' => ['required', 'date_format:Y-m-d H:i:s'],
```

#### Arrays e Files

```php
'tags' => ['required', 'array', 'min:1', 'max:5'],
'tags.*' => ['string', 'max:50'],

'avatar' => ['nullable', 'image', 'max:2048'], // 2MB
'document' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB
```

### Regras Condicionais

```php
use Illuminate\Validation\Rule;

// Unique ignorando registro atual
Rule::unique('users', 'email')->ignore($this->user()->id)

// Existe em outra tabela
Rule::exists('categories', 'id')

// In (enum)
Rule::in(['active', 'inactive', 'pending'])

// Validação condicional
'phone' => [
    'nullable',
    Rule::requiredIf($this->contact_preference === 'phone')
]
```

### Validação Customizada

```php
use Illuminate\Validation\Validator;

$request->validate([
    'email' => ['required', 'email'],
], [], [], function (Validator $validator) {
    $validator->after(function ($validator) {
        if ($this->somethingElseIsInvalid()) {
            $validator->errors()->add('field', 'Something is wrong!');
        }
    });
});
```

## Relacionamentos

### Tipos de Relacionamentos

#### One to One (Um para Um)

```php
// User model
public function profile(): HasOne
{
    return $this->hasOne(Profile::class);
}

// Profile model
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

// Uso
$user->profile;
$profile->user;
```

#### One to Many (Um para Muitos)

```php
// Post model
public function comments(): HasMany
{
    return $this->hasMany(Comment::class);
}

// Comment model
public function post(): BelongsTo
{
    return $this->belongsTo(Post::class);
}

// Uso
$post->comments;
$comment->post;
```

#### Many to Many (Muitos para Muitos)

```php
// User model
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class);
}

// Role model
public function users(): BelongsToMany
{
    return $this->belongsToMany(User::class);
}

// Uso
$user->roles;
$role->users;
```

#### Has Many Through

```php
// Country model
public function posts(): HasManyThrough
{
    return $this->hasManyThrough(Post::class, User::class);
}

// Country -> User -> Post
```

### Eager Loading

Evite N+1 queries usando eager loading:

```php
// ❌ N+1 problem
$users = User::all();
foreach ($users as $user) {
    echo $user->profile->bio; // Query para cada user
}

// ✅ Eager loading
$users = User::with('profile')->get();
foreach ($users as $user) {
    echo $user->profile->bio; // Apenas 2 queries total
}
```

### Constraints em Relacionamentos

```php
public function activeComments(): HasMany
{
    return $this->hasMany(Comment::class)
        ->where('is_approved', true)
        ->orderBy('created_at', 'desc');
}
```

## Exemplo Completo

Aqui está um exemplo completo de implementação CRUD para um recurso "Product".

### Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['is_active', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

### Form Request

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'alpha_dash',
                'max:255',
                Rule::unique('products', 'slug'),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }
}

class ProductUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'alpha_dash',
                'max:255',
                Rule::unique('products', 'slug')->ignore($this->product),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }
}
```

### Controller

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(): Response
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(15);

        return Inertia::render('products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): Response
    {
        return Inertia::render('products/Create', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductStoreRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return to_route('products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): Response
    {
        $product->load('category');

        return Inertia::render('products/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the product.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('products/Edit', [
            'product' => $product,
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(
        ProductUpdateRequest $request,
        Product $product
    ): RedirectResponse {
        $product->update($request->validated());

        return to_route('products.edit', $product)
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return to_route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
```

### Routes

```php
<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class);
});
```

### Frontend Integration

O Wayfinder gerará automaticamente as actions TypeScript:

```typescript
// resources/js/actions/App/Http/Controllers/ProductController.ts
import ProductController from '@/actions/App/Http/Controllers/ProductController';

// Usar no componente Vue
ProductController.index()      // GET /products
ProductController.create()     // GET /products/create
ProductController.store()      // POST /products
ProductController.show(1)      // GET /products/1
ProductController.edit(1)      // GET /products/1/edit
ProductController.update(1)    // PUT /products/1
ProductController.destroy(1)   // DELETE /products/1
```

## Checklist de Implementação CRUD

Ao implementar um novo recurso CRUD, siga este checklist:

### Backend
- [ ] Criar migration com campos e constraints apropriados
- [ ] Criar model com `$fillable`, `$casts` e relacionamentos
- [ ] Criar Form Request(s) com validação
- [ ] Criar controller com métodos CRUD
- [ ] Adicionar rotas no arquivo apropriado
- [ ] Executar migration: `php artisan migrate`
- [ ] (Opcional) Criar factory e seeder

### Frontend
- [ ] Criar páginas Inertia (Index, Create, Edit, Show)
- [ ] Criar componentes de formulário reutilizáveis
- [ ] Adicionar types TypeScript para o recurso
- [ ] Implementar feedback visual (loading, success, errors)
- [ ] Adicionar navegação e breadcrumbs

### Testes
- [ ] Criar testes de feature para CRUD
- [ ] Testar validações
- [ ] Testar autorização (se aplicável)

## Boas Práticas

### 1. Use Type Hints e Return Types
```php
// ✅ Bom
public function update(ProductRequest $request, Product $product): RedirectResponse

// ❌ Ruim
public function update($request, $product)
```

### 2. Sempre Valide Input
```php
// ✅ Bom
$request->validate([...]);

// ❌ Ruim - nunca confie em input do usuário
Product::create($request->all());
```

### 3. Use Route Model Binding
```php
// ✅ Bom
public function show(Product $product): Response

// ❌ Ruim
public function show($id): Response
{
    $product = Product::findOrFail($id);
}
```

### 4. Evite N+1 Queries
```php
// ✅ Bom
$products = Product::with('category')->get();

// ❌ Ruim
$products = Product::all();
```

### 5. Use Transações para Operações Múltiplas
```php
DB::transaction(function () {
    $order = Order::create([...]);
    $order->items()->createMany([...]);
    $order->user->decrement('balance', $order->total);
});
```

### 6. Retorne Feedback Adequado
```php
return to_route('products.index')
    ->with('success', 'Product created successfully');
```

### 7. Documente Código Complexo
```php
/**
 * Calculate discount based on user tier and product category.
 * 
 * Premium users get 20% off electronics, 10% off others.
 * Regular users get 5% off electronics only.
 */
private function calculateDiscount(User $user, Product $product): float
{
    // ...
}
```

## Conclusão

Este padrão CRUD fornece uma base sólida e consistente para desenvolvimento de recursos no sistema. Seguindo essas convenções, você garante:

- Código consistente e manutenível
- Fácil onboarding de novos desenvolvedores
- Redução de bugs através de validação adequada
- Performance otimizada com eager loading
- Type safety com TypeScript e PHP

Sempre consulte os exemplos existentes no código (como `ProfileController` e `PasswordController`) como referência ao implementar novos recursos.

