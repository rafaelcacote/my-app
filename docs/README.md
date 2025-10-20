# Documentação do Sistema Laravel 12 + Vue 3 + Inertia + PostgreSQL

Este projeto utiliza uma stack moderna e robusta para desenvolvimento web fullstack, combinando Laravel 12 no backend com Vue 3 e Inertia.js no frontend, com suporte a PostgreSQL como banco de dados.

## 📚 Índice da Documentação

1. **[⭐ CRUD de Empresas - Referência Completa](./CRUD-EMPRESAS-REFERENCE.md)** ⭐ **NOVO!**
   - Documentação completa do CRUD implementado
   - Serve como template para todos os novos CRUDs
   - Arquitetura backend e frontend detalhada
   - Checklist completo para implementação
   - Padrões de código e boas práticas
   - Upload de arquivos e validação completa

2. **[Padrão CRUD do Sistema](./CRUD-PATTERN.md)**
   - Estrutura de Models, Controllers e Routes
   - Convenções de nomenclatura
   - Validações padrão
   - Relacionamentos de Models
   - Exemplos práticos de implementação

3. **[Arquitetura Frontend](./FRONTEND-ARCHITECTURE.md)**
   - Estrutura de componentes Vue
   - Sistema de Layouts
   - Composables e reutilização de lógica
   - Integração com Inertia.js
   - Sistema de rotas com Wayfinder
   - UI Components (shadcn/ui)

4. **[Database Schema](./DATABASE-SCHEMA.md)**
   - Estrutura de tabelas e migrations
   - Índices e constraints
   - Relacionamentos entre tabelas
   - Convenções de nomenclatura

5. **[Guia de Desenvolvimento](./DEVELOPMENT-GUIDE.md)**
   - Setup e configuração do ambiente
   - Fluxo de trabalho de desenvolvimento
   - Boas práticas e padrões
   - Debugging e testes

6. **[Referência Rápida](./QUICK-REFERENCE.md)**
   - Comandos essenciais
   - Patterns e snippets rápidos
   - Debug helpers

7. **[Índice Visual](./INDEX.md)**
   - Navegação completa por toda a documentação
   - Busca rápida por tópicos
   - Mapa de navegação por tarefas

## 🛠️ Stack Tecnológico

### Backend
- **Laravel 12** - Framework PHP
- **Inertia.js Laravel Adapter** - Bridge entre Laravel e Vue
- **Laravel Fortify** - Autenticação e segurança
- **Laravel Wayfinder** - Type-safe route helpers

### Frontend
- **Vue 3** - Framework JavaScript progressivo
- **TypeScript** - Tipagem estática
- **Inertia.js Vue3 Adapter** - SPA-like experience
- **Tailwind CSS 4** - Framework CSS utility-first
- **Reka UI** - Headless UI components
- **Lucide Icons** - Biblioteca de ícones

### Database
- **PostgreSQL** - Banco de dados relacional (produção)
- **SQLite** - Banco de dados para desenvolvimento

### Ferramentas de Desenvolvimento
- **Vite** - Build tool e dev server
- **Laravel Pint** - Code formatting PHP
- **ESLint** - Linting JavaScript/TypeScript
- **Prettier** - Code formatting frontend
- **Pest** - Testing framework PHP

## 🎯 Características Principais

### Sistema de Autenticação Completo
- Login e Registro
- Recuperação de senha
- Verificação de email
- Autenticação de dois fatores (2FA)
- Gerenciamento de perfil

### Arquitetura Moderna
- **SPA Experience**: Navegação sem reload usando Inertia.js
- **Type Safety**: TypeScript no frontend e typed routes com Wayfinder
- **Component Library**: UI components baseados em shadcn/ui
- **Responsive Design**: Interface adaptável a todos os dispositivos
- **Dark Mode**: Suporte nativo a tema claro/escuro

### Sistema de Formulários
- Validação client-side e server-side
- Feedback visual de erros
- Type-safe form actions com Wayfinder

## 📁 Estrutura do Projeto

```
my-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controllers da aplicação
│   │   ├── Middleware/           # Middlewares customizados
│   │   └── Requests/             # Form requests com validação
│   ├── Models/                   # Eloquent models
│   └── Providers/                # Service providers
├── database/
│   ├── factories/                # Model factories para testes
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   ├── js/
│   │   ├── actions/              # Wayfinder generated routes
│   │   ├── components/           # Componentes Vue reutilizáveis
│   │   ├── composables/          # Composables Vue
│   │   ├── layouts/              # Layouts da aplicação
│   │   ├── pages/                # Páginas Inertia
│   │   └── types/                # TypeScript type definitions
│   └── css/                      # Estilos globais
├── routes/
│   ├── web.php                   # Rotas web principais
│   ├── auth.php                  # Rotas de autenticação
│   └── settings.php              # Rotas de configurações
└── docs/                         # Documentação (este diretório)
```

## 🚀 Quick Start

### Instalação

```bash
# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Rodar migrations
php artisan migrate

# Build assets
npm run build
```

### Desenvolvimento

```bash
# Iniciar servidor de desenvolvimento (tudo de uma vez)
composer dev

# Ou separadamente:
php artisan serve          # Backend server
npm run dev               # Frontend dev server com HMR
php artisan queue:listen  # Queue worker
php artisan pail          # Logs em tempo real
```

### Testes

```bash
composer test              # Rodar todos os testes
php artisan test          # Rodar testes específicos
npm run lint              # Linting frontend
npm run format            # Formatação de código
```

## 🎨 Convenções e Padrões

### Nomenclatura
- **Controllers**: Singular, sufixo "Controller" (ex: `ProfileController`)
- **Models**: Singular, PascalCase (ex: `User`)
- **Tabelas**: Plural, snake_case (ex: `users`, `password_reset_tokens`)
- **Migrations**: Descritivas, prefixo de data (ex: `2025_08_14_170933_add_two_factor_columns_to_users_table.php`)
- **Routes**: kebab-case (ex: `/settings/profile`)
- **Componentes Vue**: PascalCase (ex: `ProfileSettings.vue`)

### Estrutura de Arquivos
- Um controller por arquivo
- Um model por arquivo
- Componentes agrupados por funcionalidade
- Layouts separados por contexto (app, auth, settings)

### Validação
- Form Requests para validações complexas
- Regras inline para validações simples
- Sempre validar no backend, opcionalmente no frontend

## 📖 Leitura Recomendada

Para entender completamente o sistema, recomendamos ler a documentação na seguinte ordem:

1. **CRUD-PATTERN.md** - Entenda como criar recursos no backend
2. **FRONTEND-ARCHITECTURE.md** - Aprenda a estrutura do frontend
3. **DATABASE-SCHEMA.md** - Compreenda o modelo de dados
4. **DEVELOPMENT-GUIDE.md** - Práticas de desenvolvimento

## 🤝 Contribuindo

Ao adicionar novas funcionalidades ao sistema:

1. Siga os padrões estabelecidos nesta documentação
2. Mantenha a consistência com o código existente
3. Adicione validações apropriadas
4. Escreva testes para novas funcionalidades
5. Atualize esta documentação quando necessário

## 📝 Notas

- Esta documentação serve como referência padrão para todas as implementações futuras
- Sempre consulte os exemplos existentes no código antes de criar novos recursos
- Em caso de dúvida, siga o padrão estabelecido pelos controllers e componentes existentes

