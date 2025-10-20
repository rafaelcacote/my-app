# Guia de Desenvolvimento

Este documento fornece informações essenciais para o desenvolvimento, debugging e manutenção do sistema.

## 📋 Índice

1. [Setup do Ambiente](#setup-do-ambiente)
2. [Fluxo de Desenvolvimento](#fluxo-de-desenvolvimento)
3. [Ferramentas de Desenvolvimento](#ferramentas-de-desenvolvimento)
4. [Debugging](#debugging)
5. [Testes](#testes)
6. [Code Style e Formatação](#code-style-e-formatação)
7. [Versionamento](#versionamento)
8. [Deploy](#deploy)
9. [Troubleshooting](#troubleshooting)
10. [Boas Práticas](#boas-práticas)

## Setup do Ambiente

### Requisitos

- **PHP**: >= 8.2
- **Node.js**: >= 18.x
- **Composer**: >= 2.x
- **PostgreSQL**: >= 14.x (produção) ou SQLite (desenvolvimento)
- **Git**: >= 2.x

### Instalação Completa

#### 1. Clonar Repositório

```bash
git clone <repository-url> my-app
cd my-app
```

#### 2. Instalar Dependências

```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

#### 3. Configurar Ambiente

```bash
# Copiar .env
cp .env.example .env

# Gerar application key
php artisan key:generate
```

#### 4. Configurar Banco de Dados

**Opção A: SQLite (Desenvolvimento)**

```bash
# Criar arquivo database
touch database/database.sqlite

# .env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

**Opção B: PostgreSQL (Produção)**

```bash
# Criar banco de dados
createdb myapp

# .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=myapp
DB_USERNAME=postgres
DB_PASSWORD=secret
```

#### 5. Executar Migrations

```bash
php artisan migrate
```

#### 6. Build Assets

```bash
# Desenvolvimento
npm run dev

# Produção
npm run build
```

### Instalação Rápida (Script)

Use o script setup do composer:

```bash
composer setup
```

Isso executa:
- `composer install`
- Copia `.env.example` para `.env`
- `php artisan key:generate`
- `php artisan migrate --force`
- `npm install`
- `npm run build`

## Fluxo de Desenvolvimento

### Ambiente de Desenvolvimento

#### Opção 1: Concurrently (Recomendado)

Iniciar todos os serviços de uma vez:

```bash
composer dev
```

Isso inicia:
- **Server**: `php artisan serve` (http://localhost:8000)
- **Queue**: `php artisan queue:listen`
- **Logs**: `php artisan pail`
- **Vite**: `npm run dev` (HMR para frontend)

#### Opção 2: Separadamente

Em diferentes terminais:

```bash
# Terminal 1: Backend server
php artisan serve

# Terminal 2: Frontend dev server
npm run dev

# Terminal 3: Queue worker
php artisan queue:listen

# Terminal 4: Logs em tempo real
php artisan pail
```

### Hot Module Replacement (HMR)

O Vite fornece HMR automático durante desenvolvimento:

1. Iniciar `npm run dev`
2. Abrir navegador em `http://localhost:8000`
3. Editar arquivos `.vue`, `.ts`, `.css`
4. Mudanças aparecem instantaneamente sem reload

### Workflow de Feature

#### 1. Criar Branch

```bash
git checkout -b feature/nome-da-feature
```

#### 2. Implementar Backend

```bash
# Criar migration
php artisan make:migration create_products_table

# Criar model com factory
php artisan make:model Product -mf

# Criar controller
php artisan make:controller ProductController --resource

# Criar form request
php artisan make:request ProductStoreRequest
```

#### 3. Implementar Frontend

```bash
# Criar página
touch resources/js/pages/products/Index.vue

# Criar componente
touch resources/js/components/ProductCard.vue

# Wayfinder gerará routes automaticamente
```

#### 4. Testar

```bash
# Backend tests
php artisan test

# Frontend lint
npm run lint

# Frontend format check
npm run format:check
```

#### 5. Commit

```bash
git add .
git commit -m "feat: add product CRUD"
```

## Ferramentas de Desenvolvimento

### Laravel Pail

Logs em tempo real com syntax highlighting:

```bash
php artisan pail

# Filtrar por tipo
php artisan pail --filter=error

# Filtrar por mensagem
php artisan pail --filter="User created"
```

### Tinker (REPL)

Console interativo para Laravel:

```bash
php artisan tinker

# Exemplos
>>> User::count()
>>> User::factory()->create()
>>> DB::table('users')->get()
>>> Cache::put('key', 'value')
```

### Artisan Commands

#### Principais Comandos

```bash
# Listar todos os comandos
php artisan list

# Ajuda de um comando
php artisan help migrate

# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Otimização (produção)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Ver rotas
php artisan route:list

# Ver rotas filtradas
php artisan route:list --except-vendor

# Storage link
php artisan storage:link
```

### Vite Commands

```bash
# Dev server com HMR
npm run dev

# Build para produção
npm run build

# Build com SSR
npm run build:ssr

# Lint JavaScript/TypeScript
npm run lint

# Format código
npm run format

# Check formatting
npm run format:check
```

### Laravel Pint

Code formatter para PHP:

```bash
# Formatar todo código
./vendor/bin/pint

# Dry run (ver o que mudaria)
./vendor/bin/pint --test

# Formatar arquivo específico
./vendor/bin/pint app/Models/User.php
```

### Wayfinder

Gerar type-safe routes:

```bash
# Wayfinder roda automaticamente com Vite
npm run dev

# Ou manualmente
php artisan wayfinder:generate
```

## Debugging

### Laravel Debugbar (Opcional)

Instalar para desenvolvimento:

```bash
composer require barryvdh/laravel-debugbar --dev
```

Fornece:
- Query time e count
- Route info
- Views rendered
- Memory usage
- Messages e logs

### Dump and Die

```php
// Dump variable e continue
dump($variable);

// Dump e parar execução
dd($variable);

// Dump múltiplas variáveis
dd($var1, $var2, $var3);

// Ray (se instalado)
ray($variable);
```

### Query Debugging

```php
// Log queries
DB::enableQueryLog();
// ... execute queries
dd(DB::getQueryLog());

// Query específica
DB::listen(function ($query) {
    dump($query->sql);
    dump($query->bindings);
    dump($query->time);
});
```

### Vue Devtools

Instalar extensão do navegador: **Vue.js devtools**

Fornece:
- Component tree
- Component props e state
- Events timeline
- Routing info

### Browser DevTools

```javascript
// No console do navegador

// Ver props da página atual
$page.props

// Ver usuário autenticado
$page.props.auth.user

// Ver todas as props compartilhadas
$page.props

// Inertia router
Inertia.visit('/url')
```

### Logging

#### Backend (Laravel)

```php
use Illuminate\Support\Facades\Log;

// Níveis de log
Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);

// Com contexto
Log::info('User created', ['user_id' => $user->id]);

// Channels específicos
Log::channel('slack')->error('Something went wrong!');
```

Logs em: `storage/logs/laravel.log`

#### Frontend (Console)

```typescript
// Development only
console.log('Debug info', data);
console.error('Error', error);
console.warn('Warning', warning);

// Production - use error tracking service
// Sentry, Bugsnag, etc.
```

### XDebug (PHP Debugging)

#### Instalar XDebug

```bash
# Ubuntu/Debian
sudo apt install php8.2-xdebug

# Mac (Homebrew)
pecl install xdebug
```

#### Configurar

```ini
; php.ini ou xdebug.ini
zend_extension=xdebug.so
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_port=9003
```

#### VSCode Setup

`.vscode/launch.json`:

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for XDebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/app": "${workspaceFolder}"
            }
        }
    ]
}
```

## Testes

### PHP Tests (Pest)

#### Executar Testes

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter=UserTest

# Com coverage
php artisan test --coverage

# Parallel execution
php artisan test --parallel
```

#### Estrutura de Teste

```php
<?php

use App\Models\User;
use function Pest\Laravel\{actingAs, get, post};

test('user can view dashboard', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => 
            $page->component('Dashboard')
        );
});

test('user can update profile', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->patch('/settings/profile', [
            'name' => 'New Name',
            'email' => 'new@email.com',
        ])
        ->assertRedirect('/settings/profile');

    expect($user->fresh())
        ->name->toBe('New Name')
        ->email->toBe('new@email.com');
});
```

#### Test Helpers

```php
use function Pest\Laravel\{
    actingAs,      // Act as user
    get,           // GET request
    post,          // POST request
    put,           // PUT request
    patch,         // PATCH request
    delete,        // DELETE request
    assertDatabaseHas,
    assertDatabaseMissing,
};
```

### Frontend Tests

#### Instalar Vitest (Opcional)

```bash
npm install -D vitest @vue/test-utils
```

#### Exemplo de Teste

```typescript
import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';
import Button from '@/components/ui/button/Button.vue';

describe('Button', () => {
    it('renders correctly', () => {
        const wrapper = mount(Button, {
            slots: {
                default: 'Click me'
            }
        });

        expect(wrapper.text()).toBe('Click me');
    });

    it('emits click event', async () => {
        const wrapper = mount(Button);
        
        await wrapper.trigger('click');
        
        expect(wrapper.emitted('click')).toBeTruthy();
    });
});
```

## Code Style e Formatação

### PHP (Laravel Pint)

Configuração: `pint.json` (se existir) ou padrão Laravel

```bash
# Formatar todo código
./vendor/bin/pint

# Check apenas
./vendor/bin/pint --test

# Arquivo específico
./vendor/bin/pint app/Models/User.php
```

### JavaScript/TypeScript (ESLint + Prettier)

#### ESLint

```bash
# Lint
npm run lint

# Lint e fix
npm run lint -- --fix
```

Configuração: `eslint.config.js`

#### Prettier

```bash
# Format
npm run format

# Check
npm run format:check

# Specific files
npx prettier --write resources/js/pages/**/*.vue
```

Configuração: `.prettierrc` ou `package.json`

### EditorConfig

O projeto inclui `.editorconfig`:

```ini
root = true

[*]
charset = utf-8
indent_style = space
indent_size = 4
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true

[*.{js,ts,vue,json,yml,yaml}]
indent_size = 2
```

## Versionamento

### Git Workflow

#### Branches

```
main/master     - Produção
develop         - Desenvolvimento
feature/*       - Features
bugfix/*        - Bug fixes
hotfix/*        - Hotfixes urgentes
release/*       - Preparação de release
```

#### Commit Messages

Seguir **Conventional Commits**:

```bash
# Features
git commit -m "feat: add user profile page"
git commit -m "feat(auth): add two-factor authentication"

# Fixes
git commit -m "fix: resolve login redirect issue"
git commit -m "fix(validation): correct email validation"

# Outros tipos
git commit -m "docs: update API documentation"
git commit -m "style: format code with prettier"
git commit -m "refactor: simplify user service"
git commit -m "test: add profile update tests"
git commit -m "chore: update dependencies"
```

**Tipos:**
- `feat`: Nova feature
- `fix`: Bug fix
- `docs`: Documentação
- `style`: Formatação de código
- `refactor`: Refatoração de código
- `test`: Testes
- `chore`: Tarefas de build, configs, etc.
- `perf`: Melhorias de performance

### Semantic Versioning

Formato: `MAJOR.MINOR.PATCH` (ex: `1.2.3`)

- **MAJOR**: Breaking changes
- **MINOR**: Nova funcionalidade (backward compatible)
- **PATCH**: Bug fixes

## Deploy

### Build para Produção

```bash
# 1. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci

# 2. Build assets
npm run build

# 3. Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 4. Run migrations
php artisan migrate --force
```

### Environment Variables

Configurar `.env` de produção:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379
```

### Server Requirements

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/my-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Queue Worker (Supervisor)

`/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/my-app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/my-app/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Recarregar supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Troubleshooting

### Problemas Comuns

#### "Class not found"

```bash
# Regenerar autoload
composer dump-autoload
```

#### Permissões de Storage

```bash
# Dar permissões corretas
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### CSS/JS não carregam

```bash
# Rebuild assets
npm run build

# Limpar cache
php artisan cache:clear
php artisan view:clear
```

#### Migration Error: "Table already exists"

```bash
# Rollback e re-run
php artisan migrate:rollback
php artisan migrate

# Ou fresh (CUIDADO: deleta dados)
php artisan migrate:fresh
```

#### "419 Page Expired" em Formulários

Adicionar `@csrf` em forms:

```blade
<form method="POST">
    @csrf
    <!-- ... -->
</form>
```

Ou verificar se token CSRF está correto.

#### Inertia Version Mismatch

```bash
# Limpar cache de assets
rm -rf public/build

# Rebuild
npm run build

# Ou apenas dev
npm run dev
```

#### PostgreSQL Connection Refused

```bash
# Verificar se PostgreSQL está rodando
sudo systemctl status postgresql

# Iniciar se necessário
sudo systemctl start postgresql

# Verificar configuração
psql -U postgres -h localhost
```

### Debug Mode

Em desenvolvimento, habilitar debug:

```env
APP_DEBUG=true
```

**NUNCA** em produção:

```env
APP_DEBUG=false
```

### Logs

Verificar logs:

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

## Boas Práticas

### 1. Use Environment Variables

```php
// ✅ Bom
$apiKey = env('API_KEY');
$appName = config('app.name');

// ❌ Evite hardcoded
$apiKey = 'sk_live_abc123';
```

### 2. Validate All Input

```php
// ✅ Sempre valide
$request->validate([
    'email' => 'required|email',
]);

// ❌ Nunca confie em input
User::create($request->all());
```

### 3. Use Transactions para Operações Críticas

```php
DB::transaction(function () {
    $order = Order::create([...]);
    $order->items()->createMany([...]);
    $user->decrement('balance', $order->total);
});
```

### 4. Eager Load Relationships

```php
// ✅ Bom
$posts = Post::with('author', 'comments')->get();

// ❌ N+1 problem
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->author->name; // Query por iteração
}
```

### 5. Cache Queries Pesadas

```php
$stats = Cache::remember('dashboard_stats', 3600, function () {
    return DB::table('orders')
        ->selectRaw('COUNT(*) as total, SUM(amount) as revenue')
        ->first();
});
```

### 6. Use Queue para Tarefas Longas

```php
// ✅ Bom
SendEmailJob::dispatch($user);

// ❌ Evite processar síncrono
Mail::to($user)->send(new WelcomeEmail());
```

### 7. Type Hint Everything

```php
// ✅ Bom
public function store(ProductRequest $request): RedirectResponse
{
    // ...
}

// ❌ Evite
public function store($request)
{
    // ...
}
```

### 8. Mantenha Controllers Limpos

```php
// ✅ Bom - Lógica em Service/Action
public function store(ProductRequest $request): RedirectResponse
{
    $this->productService->create($request->validated());
    return to_route('products.index');
}

// ❌ Evite lógica complexa no controller
public function store(Request $request)
{
    // 100 linhas de lógica de negócio...
}
```

### 9. Use Form Requests

```php
// ✅ Bom
public function store(ProductStoreRequest $request)

// ❌ Evite validação inline complexa
public function store(Request $request)
{
    $request->validate([
        // 20 linhas de validação...
    ]);
}
```

### 10. Documente Código Complexo

```php
/**
 * Calculate shipping cost based on weight, destination and carrier.
 * 
 * Uses tiered pricing: 0-1kg, 1-5kg, 5-10kg, 10kg+
 * Express shipping adds 50% surcharge.
 */
public function calculateShipping(Order $order): float
{
    // ...
}
```

## Recursos Adicionais

### Documentação Oficial

- **Laravel**: https://laravel.com/docs
- **Vue 3**: https://vuejs.org/guide/
- **Inertia.js**: https://inertiajs.com/
- **TypeScript**: https://www.typescriptlang.org/docs/
- **Tailwind CSS**: https://tailwindcss.com/docs

### Packages Úteis

- **Laravel Debugbar**: https://github.com/barryvdh/laravel-debugbar
- **Laravel IDE Helper**: https://github.com/barryvdh/laravel-ide-helper
- **Ray**: https://myray.app/
- **Telescope**: https://laravel.com/docs/telescope

### Comunidade

- **Laravel News**: https://laravel-news.com/
- **Laracasts**: https://laracasts.com/
- **Vue.js Forum**: https://forum.vuejs.org/
- **Stack Overflow**: Tag `laravel` e `vue.js`

## Conclusão

Este guia fornece as ferramentas e práticas essenciais para desenvolvimento eficiente no sistema. Principais takeaways:

- Configure ambiente corretamente desde o início
- Use ferramentas de debugging apropriadas
- Escreva testes para features importantes
- Mantenha code style consistente
- Siga boas práticas de versionamento
- Documente decisões complexas

Para dúvidas específicas, consulte a documentação oficial das tecnologias ou os outros documentos nesta pasta `docs/`.

