# My App - Laravel 12 + Vue 3 + Inertia

Sistema web moderno com CRUD de Empresas usando Laravel 12, Vue 3, Inertia.js e PostgreSQL.

## 🚀 Stack Tecnológica

### Backend
- **Laravel 12** - Framework PHP
- **PostgreSQL** - Banco de dados relacional
- **Inertia.js Laravel** - Bridge Laravel + Vue
- **Laravel Fortify** - Autenticação e 2FA
- **Laravel Wayfinder** - Type-safe routes

### Frontend
- **Vue 3** - Framework JavaScript (Composition API)
- **TypeScript** - Type safety
- **Tailwind CSS 4** - Utility-first CSS
- **Reka UI** - Headless UI components
- **Lucide Icons** - Ícones modernos

## 📦 Instalação

### Requisitos
- PHP >= 8.2
- Node.js >= 18.x
- PostgreSQL >= 14.x
- Composer >= 2.x

### Setup

```bash
# 1. Clone o repositório
git clone https://github.com/SEU-USUARIO/my-app.git
cd my-app

# 2. Instale dependências
composer install
npm install

# 3. Configure ambiente
cp .env.example .env
php artisan key:generate

# 4. Configure banco de dados no .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=seu_banco
DB_USERNAME=postgres
DB_PASSWORD=sua_senha

# 5. Execute migrations
php artisan migrate

# 6. Build assets
npm run build

# 7. Inicie o servidor
php artisan serve
```

Acesse: http://localhost:8000

## 🛠️ Desenvolvimento

```bash
# Iniciar todos os serviços
composer dev

# Ou separadamente:
php artisan serve     # Backend (http://localhost:8000)
npm run dev          # Frontend com HMR
php artisan queue:listen  # Queue worker
php artisan pail     # Logs em tempo real
```

## 🎯 Funcionalidades

### ✅ Implementadas
- 🔐 Sistema de autenticação completo
- 🔑 Autenticação de dois fatores (2FA)
- 👤 Gerenciamento de perfil
- 🏢 **CRUD de Empresas**
  - Listagem com filtros e busca
  - Cadastro de empresas
  - Edição de empresas
  - Exclusão (soft delete)
  - Validação de CNPJ
  - Máscaras de input (CNPJ, telefone)
  - Toast notifications
  - Dialog de confirmação

### 🌙 Recursos Extras
- Dark mode
- Responsive design
- Toast notifications
- Validação client-side e server-side
- Type-safe routes (Wayfinder)

## 📁 Estrutura do Projeto

```
my-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── EmpresaController.php
│   │   │   └── ...
│   │   └── Requests/
│   │       ├── EmpresaStoreRequest.php
│   │       └── EmpresaUpdateRequest.php
│   └── Models/
│       └── Empresa.php
├── database/
│   └── migrations/
│       └── 2025_10_13_183601_create_empresas_table.php
├── resources/
│   └── js/
│       ├── pages/empresas/
│       │   ├── Index.vue
│       │   ├── Create.vue
│       │   └── Edit.vue
│       ├── components/
│       └── composables/
│           └── useToast.ts
├── routes/
│   └── web.php
└── docs/              # 📚 Documentação completa
    ├── README.md
    ├── CRUD-PATTERN.md
    ├── FRONTEND-ARCHITECTURE.md
    ├── DATABASE-SCHEMA.md
    └── DEVELOPMENT-GUIDE.md
```

## 📚 Documentação

A documentação completa está disponível na pasta `docs/`:

- **[docs/README.md](./docs/README.md)** - Visão geral e índice
- **[docs/CRUD-PATTERN.md](./docs/CRUD-PATTERN.md)** - Padrões CRUD
- **[docs/FRONTEND-ARCHITECTURE.md](./docs/FRONTEND-ARCHITECTURE.md)** - Arquitetura frontend
- **[docs/DATABASE-SCHEMA.md](./docs/DATABASE-SCHEMA.md)** - Schema do banco
- **[docs/DEVELOPMENT-GUIDE.md](./docs/DEVELOPMENT-GUIDE.md)** - Guia de desenvolvimento

## 🧪 Testes

```bash
# Executar testes
composer test

# Com coverage
php artisan test --coverage
```

## 🚀 Deploy

```bash
# Build para produção
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Otimizar Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Executar migrations
php artisan migrate --force
```

## 🔐 Segurança

- ⚠️ **NUNCA** commite o arquivo `.env`
- 🔑 Use variáveis de ambiente para dados sensíveis
- 🔒 Mantenha dependências atualizadas
- 🛡️ Ative 2FA em produção

## 📝 Licença

Este projeto é privado e proprietário.

## 👤 Autor

Rafael Barbosa

---

**⚡ Stack:** Laravel 12 • Vue 3 • Inertia.js • PostgreSQL • TypeScript • Tailwind CSS

