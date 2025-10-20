# Database Schema

Este documento detalha o schema do banco de dados, incluindo estrutura de tabelas, migrations, índices, constraints e relacionamentos.

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
3. [Estrutura de Migrations](#estrutura-de-migrations)
4. [Tabelas do Sistema](#tabelas-do-sistema)
5. [Convenções de Nomenclatura](#convenções-de-nomenclatura)
6. [Tipos de Dados](#tipos-de-dados)
7. [Índices e Performance](#índices-e-performance)
8. [Constraints e Validações](#constraints-e-validações)
9. [Relacionamentos](#relacionamentos)
10. [Exemplo Completo de Migration](#exemplo-completo-de-migration)
11. [Boas Práticas](#boas-práticas)

## Visão Geral

### Banco de Dados Suportados

O sistema suporta múltiplos bancos de dados através do Laravel:

- **PostgreSQL** (Recomendado para produção)
- **MySQL/MariaDB**
- **SQLite** (Desenvolvimento)

### Configuração Atual

```php
// config/database.php
'default' => env('DB_CONNECTION', 'sqlite'),

'connections' => [
    'sqlite' => [...],
    'mysql' => [...],
    'pgsql' => [...],
]
```

## Configuração do Banco de Dados

### PostgreSQL (Produção)

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=myapp
DB_USERNAME=postgres
DB_PASSWORD=secret
```

Configuração em `config/database.php`:

```php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'utf8'),
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
],
```

### SQLite (Desenvolvimento)

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### MySQL (Alternativa)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myapp
DB_USERNAME=root
DB_PASSWORD=secret
```

## Estrutura de Migrations

### Localização

Migrations estão em: `database/migrations/`

### Nomenclatura de Migrations

Formato: `YYYY_MM_DD_HHMMSS_description.php`

```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
└── 2025_08_14_170933_add_two_factor_columns_to_users_table.php
```

### Estrutura Base de Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('table_name', function (Blueprint $table) {
            // Definir colunas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_name');
    }
};
```

## Tabelas do Sistema

### users

Tabela principal de usuários do sistema.

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();                                    // BIGINT UNSIGNED AUTO_INCREMENT
    $table->string('name');                          // VARCHAR(255)
    $table->string('email')->unique();               // VARCHAR(255) UNIQUE
    $table->timestamp('email_verified_at')->nullable(); // TIMESTAMP NULL
    $table->string('password');                      // VARCHAR(255)
    $table->rememberToken();                         // VARCHAR(100) NULL
    $table->timestamps();                            // created_at, updated_at
});
```

**Índices:**
- Primary Key: `id`
- Unique: `email`

**Colunas:**
| Coluna | Tipo | Nullable | Default | Descrição |
|--------|------|----------|---------|-----------|
| id | BIGINT | NO | AUTO | ID único do usuário |
| name | VARCHAR(255) | NO | - | Nome completo |
| email | VARCHAR(255) | NO | - | Email (único) |
| email_verified_at | TIMESTAMP | YES | NULL | Data de verificação do email |
| password | VARCHAR(255) | NO | - | Hash da senha |
| remember_token | VARCHAR(100) | YES | NULL | Token "remember me" |
| created_at | TIMESTAMP | YES | NULL | Data de criação |
| updated_at | TIMESTAMP | YES | NULL | Data de atualização |

### users (Two-Factor Authentication)

Colunas adicionais para 2FA:

```php
Schema::table('users', function (Blueprint $table) {
    $table->text('two_factor_secret')
        ->after('password')
        ->nullable();
    
    $table->text('two_factor_recovery_codes')
        ->after('two_factor_secret')
        ->nullable();
    
    $table->timestamp('two_factor_confirmed_at')
        ->after('two_factor_recovery_codes')
        ->nullable();
});
```

**Colunas 2FA:**
| Coluna | Tipo | Nullable | Descrição |
|--------|------|----------|-----------|
| two_factor_secret | TEXT | YES | Segredo 2FA criptografado |
| two_factor_recovery_codes | TEXT | YES | Códigos de recuperação |
| two_factor_confirmed_at | TIMESTAMP | YES | Data de confirmação 2FA |

### password_reset_tokens

Tabela para tokens de reset de senha.

```php
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
```

**Índices:**
- Primary Key: `email`

**Colunas:**
| Coluna | Tipo | Nullable | Descrição |
|--------|------|----------|-----------|
| email | VARCHAR(255) | NO | Email do usuário (PK) |
| token | VARCHAR(255) | NO | Token de reset |
| created_at | TIMESTAMP | YES | Data de criação |

### sessions

Tabela de sessões do Laravel.

```php
Schema::create('sessions', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->foreignId('user_id')->nullable()->index();
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->longText('payload');
    $table->integer('last_activity')->index();
});
```

**Índices:**
- Primary Key: `id`
- Index: `user_id`
- Index: `last_activity`

**Colunas:**
| Coluna | Tipo | Nullable | Descrição |
|--------|------|----------|-----------|
| id | VARCHAR(255) | NO | ID da sessão (PK) |
| user_id | BIGINT | YES | FK para users.id |
| ip_address | VARCHAR(45) | YES | IP do usuário |
| user_agent | TEXT | YES | User agent do browser |
| payload | LONGTEXT | NO | Dados da sessão |
| last_activity | INTEGER | NO | Timestamp última atividade |

### cache

Tabela de cache do Laravel.

```php
Schema::create('cache', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->mediumText('value');
    $table->integer('expiration');
});
```

**Índices:**
- Primary Key: `key`

**Colunas:**
| Coluna | Tipo | Nullable | Descrição |
|--------|------|----------|-----------|
| key | VARCHAR(255) | NO | Chave do cache (PK) |
| value | MEDIUMTEXT | NO | Valor em cache |
| expiration | INTEGER | NO | Timestamp de expiração |

### cache_locks

Tabela de locks do cache.

```php
Schema::create('cache_locks', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->string('owner');
    $table->integer('expiration');
});
```

**Índices:**
- Primary Key: `key`

**Colunas:**
| Coluna | Tipo | Nullable | Descrição |
|--------|------|----------|-----------|
| key | VARCHAR(255) | NO | Chave do lock (PK) |
| owner | VARCHAR(255) | NO | Owner do lock |
| expiration | INTEGER | NO | Timestamp de expiração |

### jobs

Tabela de jobs em fila (queues).

```php
Schema::create('jobs', function (Blueprint $table) {
    $table->id();
    $table->string('queue')->index();
    $table->longText('payload');
    $table->unsignedTinyInteger('attempts');
    $table->unsignedInteger('reserved_at')->nullable();
    $table->unsignedInteger('available_at');
    $table->unsignedInteger('created_at');
});
```

**Índices:**
- Primary Key: `id`
- Index: `queue`

### job_batches

Tabela de batches de jobs.

```php
Schema::create('job_batches', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->string('name');
    $table->integer('total_jobs');
    $table->integer('pending_jobs');
    $table->integer('failed_jobs');
    $table->longText('failed_job_ids');
    $table->mediumText('options')->nullable();
    $table->integer('cancelled_at')->nullable();
    $table->integer('created_at');
    $table->integer('finished_at')->nullable();
});
```

### failed_jobs

Tabela de jobs que falharam.

```php
Schema::create('failed_jobs', function (Blueprint $table) {
    $table->id();
    $table->string('uuid')->unique();
    $table->text('connection');
    $table->text('queue');
    $table->longText('payload');
    $table->longText('exception');
    $table->timestamp('failed_at')->useCurrent();
});
```

## Convenções de Nomenclatura

### Tabelas

- **Plural, snake_case**: `users`, `products`, `order_items`
- **Tabelas pivot**: alfabética, singular: `product_tag` (não `tag_product`)
- **Tabelas polymorphic**: sufixo `-ables`: `taggables`, `commentables`

```php
// ✅ Correto
users
products
order_items
product_tag
taggables

// ❌ Incorreto
user
Products
orderItems
tag_product
```

### Colunas

- **snake_case**: `first_name`, `created_at`, `is_active`
- **Foreign keys**: `{tabela_singular}_id`: `user_id`, `product_id`
- **Boolean**: prefixo `is_`, `has_`, `can_`: `is_active`, `has_verified`
- **Timestamps**: sufixo `_at`: `created_at`, `published_at`, `deleted_at`

```php
// ✅ Correto
$table->string('first_name');
$table->foreignId('user_id');
$table->boolean('is_active');
$table->timestamp('published_at');

// ❌ Incorreto
$table->string('firstName');
$table->foreignId('userId');
$table->boolean('active');
$table->timestamp('publish_date');
```

### Primary Keys

- **Padrão**: `id` (BIGINT UNSIGNED AUTO_INCREMENT)
- Use `$table->id()` helper

```php
// ✅ Correto
$table->id();

// ❌ Evite
$table->bigIncrements('id');
```

### Timestamps

- Use `timestamps()` para `created_at` e `updated_at`
- Use `softDeletes()` para `deleted_at`

```php
$table->timestamps();        // created_at, updated_at
$table->softDeletes();      // deleted_at
$table->timestamp('published_at')->nullable();
```

## Tipos de Dados

### Numéricos

```php
// Inteiros
$table->tinyInteger('votes');           // TINYINT (-128 to 127)
$table->smallInteger('votes');          // SMALLINT (-32K to 32K)
$table->integer('votes');               // INT (-2B to 2B)
$table->bigInteger('votes');            // BIGINT

// Unsigned
$table->unsignedBigInteger('user_id'); // BIGINT UNSIGNED
$table->foreignId('user_id');          // Alias para unsignedBigInteger

// Incrementais
$table->id();                          // BIGINT UNSIGNED AUTO_INCREMENT
$table->increments('id');              // INT UNSIGNED AUTO_INCREMENT
$table->bigIncrements('id');           // BIGINT UNSIGNED AUTO_INCREMENT

// Decimais
$table->decimal('price', 10, 2);       // DECIMAL(10,2)
$table->float('amount', 8, 2);         // FLOAT(8,2)
$table->double('amount', 8, 2);        // DOUBLE(8,2)
```

### Strings

```php
$table->char('code', 4);               // CHAR(4)
$table->string('name');                // VARCHAR(255)
$table->string('name', 100);           // VARCHAR(100)
$table->text('description');           // TEXT
$table->mediumText('description');     // MEDIUMTEXT
$table->longText('description');       // LONGTEXT
```

### Datas e Tempo

```php
$table->date('birth_date');            // DATE
$table->datetime('created_at');        // DATETIME
$table->timestamp('created_at');       // TIMESTAMP
$table->time('sunrise');               // TIME
$table->year('year');                  // YEAR

// Helpers
$table->timestamps();                  // created_at, updated_at
$table->timestampsTz();               // com timezone
$table->softDeletes();                // deleted_at
$table->softDeletesTz();              // deleted_at com timezone
```

### Booleanos

```php
$table->boolean('is_active');          // BOOLEAN (TINYINT em MySQL)
$table->boolean('is_active')->default(true);
```

### JSON

```php
$table->json('metadata');              // JSON
$table->jsonb('metadata');            // JSONB (PostgreSQL)
```

### Outros

```php
$table->uuid('id');                    // UUID
$table->ipAddress('visitor');          // IP Address
$table->macAddress('device');          // MAC Address
$table->binary('data');                // BLOB
$table->enum('status', ['active', 'inactive']);
```

## Índices e Performance

### Tipos de Índices

```php
// Primary Key
$table->id();                          // Automaticamente PK
$table->primary('id');                 // Definir PK manualmente
$table->primary(['id', 'parent_id']);  // Composite PK

// Unique
$table->string('email')->unique();     // Índice único
$table->unique('email');               // Alternativa
$table->unique(['email', 'tenant_id']); // Composite unique

// Index
$table->string('status')->index();     // Índice simples
$table->index('status');               // Alternativa
$table->index(['status', 'created_at']); // Composite index

// Foreign Key
$table->foreignId('user_id')
    ->constrained()                    // FK para users.id
    ->onDelete('cascade');             // Cascade on delete

$table->foreignId('category_id')
    ->constrained()
    ->onUpdate('cascade')
    ->onDelete('restrict');
```

### Quando Usar Índices

**USE índices para:**
- Primary keys (automático)
- Foreign keys
- Colunas usadas em WHERE clauses frequentemente
- Colunas usadas em JOIN conditions
- Colunas usadas em ORDER BY
- Colunas únicas (email, slug)

**NÃO use índices para:**
- Tabelas muito pequenas (< 100 registros)
- Colunas com muita variação (descrições, textos longos)
- Colunas raramente usadas em queries

### Exemplo de Índices Otimizados

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();               // Índice único
    $table->foreignId('category_id')                // FK + Index
        ->constrained()
        ->onDelete('cascade');
    $table->decimal('price', 10, 2);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    // Composite index para queries comuns
    $table->index(['is_active', 'created_at']);
    $table->index(['category_id', 'is_active']);
});
```

### Performance Tips

1. **Use Composite Indexes para queries comuns:**
```php
// Se você faz queries assim frequentemente:
// SELECT * FROM products WHERE category_id = ? AND is_active = 1 ORDER BY created_at
$table->index(['category_id', 'is_active', 'created_at']);
```

2. **Ordem importa em composite indexes:**
```php
// Ordem: mais seletivo primeiro
$table->index(['user_id', 'status', 'created_at']);
// user_id é mais seletivo que status
```

3. **Evite over-indexing:**
```php
// ❌ Muito índice
$table->index('name');
$table->index('description');
$table->index('price');
$table->index('stock');

// ✅ Apenas onde necessário
$table->index(['is_active', 'created_at']);
```

## Constraints e Validações

### Foreign Keys

```php
// Simples
$table->foreignId('user_id')
    ->constrained();  // Assume tabela 'users', coluna 'id'

// Customizado
$table->foreignId('author_id')
    ->constrained('users', 'id');  // Especifica tabela e coluna

// Com ações
$table->foreignId('user_id')
    ->constrained()
    ->onUpdate('cascade')
    ->onDelete('cascade');

// Opções de onDelete/onUpdate:
// - cascade: deleta/atualiza registros relacionados
// - restrict: impede delete/update se houver relacionados
// - set null: seta NULL nos relacionados
// - no action: nenhuma ação
```

### Unique Constraints

```php
// Coluna única
$table->string('email')->unique();

// Unique com nome customizado
$table->unique('email', 'users_email_unique');

// Composite unique
$table->unique(['email', 'tenant_id']);
```

### Check Constraints (PostgreSQL)

```php
use Illuminate\Support\Facades\DB;

DB::statement('ALTER TABLE products ADD CONSTRAINT check_price CHECK (price >= 0)');
```

### Default Values

```php
$table->boolean('is_active')->default(true);
$table->integer('views')->default(0);
$table->string('role')->default('user');
$table->timestamp('created_at')->useCurrent();
```

### Nullable vs Not Nullable

```php
// NOT NULL (padrão)
$table->string('name');

// NULL
$table->string('phone')->nullable();
$table->timestamp('email_verified_at')->nullable();
```

## Relacionamentos

### One to One

```php
// users table (parent)
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('email');
    $table->timestamps();
});

// profiles table (child)
Schema::create('profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')
        ->unique()                    // One-to-One: FK é unique
        ->constrained()
        ->onDelete('cascade');
    $table->text('bio')->nullable();
    $table->timestamps();
});
```

### One to Many

```php
// categories table (parent)
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

// products table (child)
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('category_id')  // One-to-Many: FK não é unique
        ->constrained()
        ->onDelete('cascade');
    $table->timestamps();
});
```

### Many to Many

```php
// products table
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

// tags table
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

// product_tag pivot table (alfabética, singular)
Schema::create('product_tag', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')
        ->constrained()
        ->onDelete('cascade');
    $table->foreignId('tag_id')
        ->constrained()
        ->onDelete('cascade');
    
    // Opcional: campos extras na pivot
    $table->integer('order')->default(0);
    $table->timestamps();
    
    // Unique constraint para evitar duplicatas
    $table->unique(['product_id', 'tag_id']);
});
```

### Polymorphic (One to Many)

```php
// posts table
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->timestamps();
});

// videos table
Schema::create('videos', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->timestamps();
});

// comments table (polymorphic)
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->text('content');
    $table->morphs('commentable');  // cria commentable_id e commentable_type
    $table->timestamps();
    
    // Ou manualmente:
    // $table->unsignedBigInteger('commentable_id');
    // $table->string('commentable_type');
    // $table->index(['commentable_id', 'commentable_type']);
});
```

### Has Many Through

```php
// countries table
Schema::create('countries', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

// users table
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('country_id')->constrained();
    $table->string('name');
    $table->timestamps();
});

// posts table
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('title');
    $table->timestamps();
});

// Country -> User -> Post
// Permite: $country->posts
```

## Exemplo Completo de Migration

Aqui está um exemplo completo de um sistema de blog:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('slug');
        });

        // Posts
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('restrict');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para performance
            $table->index('slug');
            $table->index(['status', 'published_at']);
            $table->index(['user_id', 'status']);
            $table->index(['category_id', 'status']);
        });

        // Tags
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Post-Tag Pivot
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('tag_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['post_id', 'tag_id']);
        });

        // Comments
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignId('parent_id')  // Para respostas
                ->nullable()
                ->constrained('comments')
                ->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            
            $table->index(['post_id', 'is_approved']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
    }
};
```

## Boas Práticas

### 1. Sempre Defina up() e down()

```php
// ✅ Correto
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        // ...
    });
}

public function down(): void
{
    Schema::dropIfExists('products');
}
```

### 2. Use Type Hints

```php
// ✅ Correto
public function up(): void

// ❌ Evite
public function up()
```

### 3. Ordem de Migrations Importa

```php
// ❌ Erro: users ainda não existe
2024_01_01_000000_create_posts_table.php
2024_01_01_000001_create_users_table.php

// ✅ Correto: users criada primeiro
2024_01_01_000000_create_users_table.php
2024_01_01_000001_create_posts_table.php
```

### 4. Use Helpers do Blueprint

```php
// ✅ Bom
$table->id();
$table->foreignId('user_id')->constrained();
$table->timestamps();
$table->softDeletes();

// ❌ Evite
$table->bigIncrements('id');
$table->unsignedBigInteger('user_id');
$table->foreign('user_id')->references('id')->on('users');
$table->timestamp('created_at')->nullable();
$table->timestamp('updated_at')->nullable();
```

### 5. Índices para Performance

```php
// ✅ Bom - Índices para queries comuns
$table->index(['status', 'created_at']);
$table->index('user_id');
$table->unique('email');

// ❌ Evite - Muitos índices sem necessidade
$table->index('name');
$table->index('description');
$table->index('any_column');
```

### 6. Sempre Use onDelete() em Foreign Keys

```php
// ✅ Bom
$table->foreignId('user_id')
    ->constrained()
    ->onDelete('cascade');  // Define comportamento

// ❌ Evite
$table->foreignId('user_id')
    ->constrained();  // Comportamento indefinido
```

### 7. Use Migrations Separadas para Alterações

```php
// ❌ Evite modificar migrations antigas
// 2024_01_01_000000_create_users_table.php

// ✅ Crie nova migration
// 2024_01_15_000000_add_phone_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable()->after('email');
});
```

### 8. Prefira nullable() a default('')

```php
// ✅ Bom
$table->string('phone')->nullable();
$table->text('bio')->nullable();

// ❌ Evite
$table->string('phone')->default('');
$table->text('bio')->default('');
```

### 9. Use Enums para Valores Fixos

```php
// ✅ Bom
$table->enum('status', ['pending', 'active', 'inactive'])
    ->default('pending');

// ❌ Evite
$table->string('status');  // Qualquer valor possível
```

### 10. Documente Migrations Complexas

```php
/**
 * Add composite index for optimized user activity queries.
 * 
 * This migration adds a composite index on (user_id, created_at)
 * to optimize the user activity timeline query which is called
 * on every dashboard page load.
 */
public function up(): void
{
    Schema::table('activities', function (Blueprint $table) {
        $table->index(['user_id', 'created_at'], 'idx_user_activity');
    });
}
```

### 11. Rollback Deve Reverter Completamente

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->after('email');
        $table->boolean('is_verified')->default(false);
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone', 'is_verified']);  // Reverte tudo
    });
}
```

## Comandos Úteis

### Criar Migration

```bash
# Nova tabela
php artisan make:migration create_products_table

# Modificar tabela existente
php artisan make:migration add_status_to_products_table --table=products

# Com model
php artisan make:model Product -m
```

### Executar Migrations

```bash
# Rodar todas pendentes
php artisan migrate

# Rollback última batch
php artisan migrate:rollback

# Rollback tudo
php artisan migrate:reset

# Rollback e re-run
php artisan migrate:refresh

# Fresh (drop all + migrate)
php artisan migrate:fresh

# Com seed
php artisan migrate:fresh --seed
```

### Outras Operações

```bash
# Ver status
php artisan migrate:status

# Forçar em produção
php artisan migrate --force

# Step específico
php artisan migrate --step=1
php artisan migrate:rollback --step=1
```

## PostgreSQL Específico

### Tipos Extras

```php
// UUID
$table->uuid('id')->primary();

// JSONB (mais rápido que JSON)
$table->jsonb('metadata');

// Arrays
DB::statement('ALTER TABLE products ADD COLUMN tags TEXT[]');

// Full-text search
DB::statement('ALTER TABLE posts ADD COLUMN search_vector tsvector');
DB::statement('CREATE INDEX posts_search_idx ON posts USING gin(search_vector)');
```

### Constraints

```php
// Check constraint
DB::statement('
    ALTER TABLE products 
    ADD CONSTRAINT check_price_positive 
    CHECK (price >= 0)
');

// Exclusion constraint
DB::statement('
    CREATE EXTENSION IF NOT EXISTS btree_gist;
    ALTER TABLE bookings 
    ADD CONSTRAINT no_overlapping_bookings 
    EXCLUDE USING gist (room_id WITH =, daterange(start_date, end_date) WITH &&)
');
```

## Conclusão

O schema do banco de dados é a fundação do sistema. Principais pontos:

- **Use PostgreSQL em produção** para features avançadas
- **Siga convenções de nomenclatura** para consistência
- **Crie índices estrategicamente** para performance
- **Defina foreign keys e constraints** para integridade
- **Migrations devem ser reversíveis** com down() completo
- **Documente decisões complexas** em comentários

Seguindo essas práticas, você terá um banco de dados robusto, performático e manutenível.

