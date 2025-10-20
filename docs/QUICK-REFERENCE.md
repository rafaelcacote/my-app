# Referência Rápida

Guia de consulta rápida com comandos e patterns mais utilizados.

## 🚀 Quick Start

```bash
# Setup inicial
composer setup

# Desenvolvimento
composer dev

# Ou separadamente
php artisan serve     # Backend: http://localhost:8000
npm run dev          # Frontend com HMR
```

## 📝 Comandos Essenciais

### Backend (Laravel)

```bash
# Migrations
php artisan make:migration create_products_table
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh

# Models
php artisan make:model Product -mf        # Com migration e factory
php artisan make:model Product -a         # All (migration, factory, seeder, controller)

# Controllers
php artisan make:controller ProductController --resource

# Form Requests
php artisan make:request ProductStoreRequest

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize (produção)
php artisan config:cache
php artisan route:cache
php artisan optimize

# Testes
php artisan test
php artisan test --filter=UserTest
php artisan test --coverage

# Queue
php artisan queue:work
php artisan queue:listen

# Tinker (REPL)
php artisan tinker

# Logs
php artisan pail
```

### Frontend (Vite)

```bash
# Desenvolvimento
npm run dev

# Build produção
npm run build

# Build com SSR
npm run build:ssr

# Lint
npm run lint

# Format
npm run format
npm run format:check
```

## 🏗️ Criar Nova Feature CRUD

### 1. Backend

```bash
# Migration + Model + Factory
php artisan make:model Product -mf

# Controller
php artisan make:controller ProductController --resource

# Form Requests
php artisan make:request ProductStoreRequest
php artisan make:request ProductUpdateRequest
```

### 2. Migration

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->boolean('is_active')->default(true);
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->timestamps();
    
    $table->index(['is_active', 'created_at']);
});
```

### 3. Model

```php
protected $fillable = ['name', 'slug', 'description', 'price', 'is_active', 'category_id'];

protected function casts(): array
{
    return [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}

public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}
```

### 4. Form Request

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'slug' => ['required', 'string', 'unique:products'],
        'description' => ['nullable', 'string'],
        'price' => ['required', 'numeric', 'min:0'],
        'is_active' => ['boolean'],
        'category_id' => ['required', 'exists:categories,id'],
    ];
}
```

### 5. Controller

```php
public function index(): Response
{
    return Inertia::render('products/Index', [
        'products' => Product::with('category')->paginate(15),
    ]);
}

public function store(ProductStoreRequest $request): RedirectResponse
{
    Product::create($request->validated());
    return to_route('products.index');
}

public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
{
    $product->update($request->validated());
    return to_route('products.edit', $product);
}

public function destroy(Product $product): RedirectResponse
{
    $product->delete();
    return to_route('products.index');
}
```

### 6. Routes

```php
Route::resource('products', ProductController::class);
```

### 7. Frontend Page

```vue
<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ProductController from '@/actions/App/Http/Controllers/ProductController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Props {
    product: Product;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.product.name,
    price: props.product.price,
});

const submit = () => {
    form.patch(ProductController.update(props.product.id).url);
};
</script>

<template>
    <Head title="Edit Product" />

    <AppLayout>
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <Label for="name">Name</Label>
                <Input id="name" v-model="form.name" />
                <p v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</p>
            </div>

            <div>
                <Label for="price">Price</Label>
                <Input id="price" v-model="form.price" type="number" step="0.01" />
                <p v-if="form.errors.price" class="text-red-500">{{ form.errors.price }}</p>
            </div>

            <Button :disabled="form.processing">Save</Button>
        </form>
    </AppLayout>
</template>
```

## 🎨 Componentes UI Comuns

### Button

```vue
<Button>Default</Button>
<Button variant="destructive">Delete</Button>
<Button variant="outline">Cancel</Button>
<Button variant="ghost">Ghost</Button>
<Button size="sm">Small</Button>
<Button size="lg">Large</Button>
<Button :disabled="loading">Save</Button>
```

### Input

```vue
<Label for="email">Email</Label>
<Input id="email" v-model="form.email" type="email" placeholder="email@example.com" />
<InputError :message="form.errors.email" />
```

### Card

```vue
<Card>
    <CardHeader>
        <CardTitle>Title</CardTitle>
        <CardDescription>Description</CardDescription>
    </CardHeader>
    <CardContent>Content</CardContent>
    <CardFooter>Footer</CardFooter>
</Card>
```

### Dialog

```vue
<Dialog v-model:open="open">
    <DialogTrigger as-child>
        <Button>Open</Button>
    </DialogTrigger>
    <DialogContent>
        <DialogHeader>
            <DialogTitle>Title</DialogTitle>
            <DialogDescription>Description</DialogDescription>
        </DialogHeader>
        <!-- Content -->
        <DialogFooter>
            <Button>Confirm</Button>
        </DialogFooter>
    </DialogContent>
</Dialog>
```

## 🔄 Inertia Patterns

### Navegação

```vue
<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import ProductController from '@/actions/App/Http/Controllers/ProductController';

// Link component
<Link :href="ProductController.show(1).url">View</Link>

// Programaticamente
router.visit(ProductController.show(1).url);
</script>
```

### Formulários

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
});

const submit = () => {
    form.post('/url', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => console.error(form.errors),
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <input v-model="form.name" />
        <p v-if="form.errors.name">{{ form.errors.name }}</p>
        <button :disabled="form.processing">Submit</button>
    </form>
</template>
```

### Form Component (Declarativo)

```vue
<Form v-bind="ProfileController.update.form()" v-slot="{ errors, processing }">
    <Input name="name" :error="errors.name" />
    <Button :disabled="processing">Save</Button>
</Form>
```

## 🗄️ Database Patterns

### Relacionamentos

```php
// One to One
public function profile(): HasOne
{
    return $this->hasOne(Profile::class);
}

// One to Many
public function posts(): HasMany
{
    return $this->hasMany(Post::class);
}

// Belongs To
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

// Many to Many
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class);
}

// Polymorphic
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}
```

### Queries Comuns

```php
// Find
Product::find(1);
Product::findOrFail(1);

// Where
Product::where('is_active', true)->get();
Product::where('price', '>', 100)->latest()->get();

// Eager loading
Product::with('category', 'tags')->get();

// Pagination
Product::paginate(15);
Product::simplePaginate(15);

// Aggregates
Product::count();
Product::sum('price');
Product::avg('price');
Product::max('price');

// Create
Product::create(['name' => 'Test', 'price' => 99.99]);

// Update
$product->update(['price' => 89.99]);

// Delete
$product->delete();
Product::destroy(1);
Product::destroy([1, 2, 3]);
```

## 🧪 Testing Patterns

```php
use function Pest\Laravel\{actingAs, get, post, patch, delete};

test('user can create product', function () {
    $user = User::factory()->create();
    
    actingAs($user)
        ->post('/products', [
            'name' => 'Test Product',
            'price' => 99.99,
        ])
        ->assertRedirect('/products');
    
    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});

test('validation works', function () {
    $user = User::factory()->create();
    
    actingAs($user)
        ->post('/products', [])
        ->assertSessionHasErrors(['name', 'price']);
});
```

## 🔧 Validação Comum

```php
// String
'name' => ['required', 'string', 'max:255']

// Email
'email' => ['required', 'email', 'unique:users']

// Number
'price' => ['required', 'numeric', 'min:0']
'quantity' => ['required', 'integer', 'between:1,100']

// Boolean
'is_active' => ['boolean']

// Date
'birth_date' => ['required', 'date', 'before:today']

// File
'avatar' => ['nullable', 'image', 'max:2048']

// Array
'tags' => ['required', 'array', 'min:1']
'tags.*' => ['string', 'max:50']

// Exists
'category_id' => ['required', 'exists:categories,id']

// Unique (ignorando atual)
Rule::unique('users', 'email')->ignore($user->id)
```

## 🎯 TypeScript Types

```typescript
// User type
export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

// Page props
interface Props {
    user: User;
    status?: string;
}

const props = defineProps<Props>();

// Emits
const emit = defineEmits<{
    update: [value: string];
    delete: [id: number];
}>();

// Composable
export function useCounter(initial = 0) {
    const count = ref(initial);
    const increment = () => count.value++;
    return { count, increment };
}
```

## 🐛 Debug Helpers

```php
// Backend
dump($variable);           // Dump e continue
dd($variable);            // Dump e morra
ray($variable);           // Ray (se instalado)

// Query debugging
DB::enableQueryLog();
dd(DB::getQueryLog());

// Log
Log::info('Message', ['context' => $data]);
```

```javascript
// Frontend
console.log($page.props);           // Todas as props
console.log($page.props.auth.user); // User autenticado
```

## 📦 Git Commits

```bash
feat: add user profile page
fix: resolve login redirect issue
docs: update API documentation
style: format code with prettier
refactor: simplify user service
test: add profile update tests
chore: update dependencies
perf: optimize database queries
```

## 🚀 Deploy Checklist

```bash
# 1. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci

# 2. Build
npm run build

# 3. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 4. Migrate
php artisan migrate --force

# 5. Verify
php artisan about
```

## 📚 Documentação Completa

Para informações detalhadas, consulte:

- **[README.md](./README.md)** - Visão geral e índice
- **[CRUD-PATTERN.md](./CRUD-PATTERN.md)** - Padrões CRUD backend
- **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** - Arquitetura frontend
- **[DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)** - Schema do banco
- **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** - Guia de desenvolvimento

## 🔗 Links Úteis

- Laravel Docs: https://laravel.com/docs
- Vue 3 Docs: https://vuejs.org
- Inertia Docs: https://inertiajs.com
- Tailwind Docs: https://tailwindcss.com

