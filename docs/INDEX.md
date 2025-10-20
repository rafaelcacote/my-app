# 📚 Índice da Documentação

Navegação visual e estruturada de toda a documentação do projeto.

---

## 🎯 Por Onde Começar?

### 👤 Novo no Projeto?
1. 📖 **[README.md](./README.md)** - Comece aqui para entender o projeto
2. 🚀 **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** - Configure seu ambiente
3. ⚡ **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** - Comandos e patterns rápidos

### 💻 Desenvolvedor Backend?
1. 🔧 **[CRUD-PATTERN.md](./CRUD-PATTERN.md)** - Aprenda os padrões CRUD
2. 🗄️ **[DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)** - Entenda o schema do banco
3. 📝 **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** - Ferramentas e debugging

### 🎨 Desenvolvedor Frontend?
1. ⚛️ **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** - Arquitetura completa
2. ⚡ **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** - Componentes e patterns
3. 🚀 **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** - Setup e ferramentas

---

## 📑 Documentos Disponíveis

### 📖 [README.md](./README.md)
**Visão Geral do Projeto**

- 📚 Índice completo da documentação
- 🛠️ Stack tecnológico
- 🎯 Características principais
- 📁 Estrutura do projeto
- 🚀 Quick start
- 🎨 Convenções e padrões

**Ideal para:** Entender o projeto como um todo

---

### 🔧 [CRUD-PATTERN.md](./CRUD-PATTERN.md)
**Padrão CRUD do Sistema**

#### Conteúdo:
- ✅ Estrutura geral de CRUD
- 📦 Models e convenções
- 🎮 Controllers e métodos padrão
- 🛣️ Routes e nomenclatura
- ✍️ Form Requests
- ✔️ Validações padrão
- 🔗 Relacionamentos
- 📋 Exemplo completo de CRUD
- ✨ Boas práticas

**Ideal para:** Implementar novos recursos backend

---

### 📋 [CRUD-EMPRESAS-REFERENCE.md](./CRUD-EMPRESAS-REFERENCE.md)
**Documentação de Referência: CRUD de Empresas**

#### Conteúdo:
- 🎯 Visão geral do CRUD completo implementado
- 🔧 Stack tecnológica detalhada
- 🏗️ Arquitetura do sistema com diagramas
- 💾 Estrutura completa de banco de dados
- ⚙️ Backend - Laravel (Model, Controller, Requests)
- 🎨 Frontend - Vue + Inertia (Pages, Components)
- 🛣️ Sistema de Rotas (Wayfinder)
- 📤 Upload de arquivos com preview
- ✅ Validação completa de dados
- 📐 Padrões de código estabelecidos
- ✅ **Checklist completo para novos CRUDs**

**Ideal para:** Criar novos CRUDs seguindo o padrão estabelecido

**⭐ RECOMENDADO:** Esta é a documentação mais completa e detalhada para servir como template para todos os próximos CRUDs do sistema.

**Principais Seções:**
```
Arquitetura
├── Fluxo de dados completo
├── Estrutura de diretórios
└── Integração backend/frontend

Backend Completo
├── Migration detalhada
├── Model com accessors e scopes
├── Controller resource completo
├── Form Requests (Store e Update)
└── Upload de arquivos

Frontend Completo
├── Página Index (listagem + filtros + paginação)
├── Página Create
├── Página Edit
├── Componentes reutilizáveis
└── Formatação de campos

Sistema de Rotas
├── Routes Wayfinder geradas
├── Actions tipadas
└── Exemplos de uso

Checklist Completo
├── Backend tasks
├── Frontend tasks
├── Testes
└── Documentação
```

---

### ⚛️ [FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)
**Arquitetura Frontend Completa**

#### Conteúdo:
- 🏗️ Estrutura de diretórios
- 📄 Sistema de páginas Inertia
- 🎨 Sistema de layouts
- 🧩 Componentes Vue
- 🔄 Composables
- 🛣️ Sistema de rotas (Wayfinder)
- 📘 TypeScript types
- 🎭 UI Components library
- 📡 Comunicação com backend
- 📋 Exemplo completo
- ✨ Boas práticas

**Ideal para:** Desenvolver páginas e componentes frontend

**Principais Seções:**
```
Páginas Inertia
├── Estrutura de página
├── Props do backend
└── Shared props

Layouts
├── AppLayout
├── AuthLayout
└── SettingsLayout

Componentes
├── Componentes de aplicação
├── UI components
└── Convenções

Wayfinder (Routes)
├── Type-safe routes
├── Navegação
└── Formulários

Comunicação
├── Inertia Forms
├── useForm hook
└── Router
```

---

### 🗄️ [DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)
**Schema do Banco de Dados**

#### Conteúdo:
- 🔍 Visão geral
- ⚙️ Configuração (PostgreSQL, MySQL, SQLite)
- 📝 Estrutura de migrations
- 📊 Tabelas do sistema
- 🏷️ Convenções de nomenclatura
- 🔢 Tipos de dados
- ⚡ Índices e performance
- 🔒 Constraints e validações
- 🔗 Relacionamentos
- 📋 Exemplo completo
- ✨ Boas práticas

**Ideal para:** Criar migrations e entender o modelo de dados

**Principais Seções:**
```
Tabelas
├── users
├── password_reset_tokens
├── sessions
├── cache
└── jobs

Migrations
├── Nomenclatura
├── Estrutura base
└── up() e down()

Tipos de Dados
├── Numéricos
├── Strings
├── Datas
├── Booleanos
└── JSON

Relacionamentos
├── One to One
├── One to Many
├── Many to Many
└── Polymorphic

Índices
├── Primary Key
├── Foreign Key
├── Unique
└── Composite
```

---

### 🚀 [DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)
**Guia de Desenvolvimento Completo**

#### Conteúdo:
- 🛠️ Setup do ambiente
- 🔄 Fluxo de desenvolvimento
- 🧰 Ferramentas de desenvolvimento
- 🐛 Debugging
- 🧪 Testes
- 💅 Code style e formatação
- 📦 Versionamento
- 🚀 Deploy
- 🔧 Troubleshooting
- ✨ Boas práticas

**Ideal para:** Setup inicial e resolução de problemas

**Principais Seções:**
```
Setup
├── Requisitos
├── Instalação completa
└── Script de setup

Desenvolvimento
├── Ambiente local
├── HMR (Hot Module Replacement)
└── Workflow de feature

Ferramentas
├── Laravel Pail
├── Tinker
├── Artisan
└── Vite

Debugging
├── Laravel Debugbar
├── dd(), dump(), ray()
├── Query debugging
└── Vue Devtools

Testes
├── PHP (Pest)
└── Frontend (Vitest)

Deploy
├── Build para produção
├── Nginx config
└── Queue worker
```

---

### ⚡ [QUICK-REFERENCE.md](./QUICK-REFERENCE.md)
**Referência Rápida**

#### Conteúdo:
- 🚀 Quick start
- 📝 Comandos essenciais
- 🏗️ Criar CRUD completo
- 🎨 Componentes UI comuns
- 🔄 Inertia patterns
- 🗄️ Database patterns
- 🧪 Testing patterns
- 🔧 Validação comum
- 🎯 TypeScript types
- 🐛 Debug helpers

**Ideal para:** Consulta rápida durante desenvolvimento

**Principais Seções:**
```
Comandos
├── Laravel (artisan)
├── Vite (npm)
└── Git

CRUD Rápido
├── Backend (migration, model, controller)
├── Frontend (page, components)
└── Routes

Componentes UI
├── Button
├── Input
├── Card
└── Dialog

Patterns
├── Inertia (navegação, formulários)
├── Database (queries, relacionamentos)
├── Validação
└── Testing
```

---

## 🗺️ Mapa de Navegação

### Por Tarefa:

#### Criar Nova Feature CRUD
1. **[CRUD-EMPRESAS-REFERENCE.md](./CRUD-EMPRESAS-REFERENCE.md)** → ⭐ Referência completa (RECOMENDADO)
2. **[CRUD-PATTERN.md](./CRUD-PATTERN.md)** → Entender padrões gerais
3. **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** → Comandos rápidos
4. **[DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)** → Criar migration
5. **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** → Criar página

#### Resolver Problema/Bug
1. **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** → Troubleshooting
2. **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** → Debug helpers

#### Entender Código Existente
1. **[README.md](./README.md)** → Visão geral
2. **[CRUD-PATTERN.md](./CRUD-PATTERN.md)** → Backend patterns
3. **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** → Frontend patterns

#### Setup Ambiente Novo
1. **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** → Setup completo
2. **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** → Comandos rápidos

---

## 📊 Visão por Tecnologia

### Laravel (Backend)
- **[CRUD-PATTERN.md](./CRUD-PATTERN.md)** - Models, Controllers, Routes
- **[DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)** - Migrations, Schema
- **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** - Artisan, Debugging

### Vue 3 (Frontend)
- **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** - Componentes, Composables
- **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** - UI Components

### Inertia.js
- **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** - Pages, Forms, Router
- **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** - Patterns

### PostgreSQL
- **[DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)** - Schema, Migrations, Tipos

### TypeScript
- **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** - Types, Interfaces
- **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** - Type patterns

---

## 🎓 Níveis de Aprofundamento

### 🌱 Iniciante
1. **[README.md](./README.md)** - Entenda o projeto
2. **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)** - Comandos básicos
3. **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** - Setup ambiente

### 🌿 Intermediário
1. **[CRUD-PATTERN.md](./CRUD-PATTERN.md)** - Implemente features
2. **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** - Crie componentes
3. **[DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)** - Modele dados

### 🌳 Avançado
- Todos os documentos para referência profunda
- Seções de "Boas Práticas" em cada documento
- Exemplos completos e patterns avançados

---

## 🔍 Busca Rápida

### Quero aprender sobre...

**CRUD Completo (Exemplo Real)**
→ [CRUD-EMPRESAS-REFERENCE.md](./CRUD-EMPRESAS-REFERENCE.md) ⭐ RECOMENDADO

**Models e Eloquent**
→ [CRUD-PATTERN.md - Models](./CRUD-PATTERN.md#models)
→ [CRUD-EMPRESAS-REFERENCE.md - Model](./CRUD-EMPRESAS-REFERENCE.md#1-model-empresaphp)

**Controllers e Actions**
→ [CRUD-PATTERN.md - Controllers](./CRUD-PATTERN.md#controllers)
→ [CRUD-EMPRESAS-REFERENCE.md - Controller](./CRUD-EMPRESAS-REFERENCE.md#2-controller-empresacontrollerphp)

**Validação de Dados**
→ [CRUD-PATTERN.md - Validações](./CRUD-PATTERN.md#validações)
→ [CRUD-EMPRESAS-REFERENCE.md - Form Requests](./CRUD-EMPRESAS-REFERENCE.md#3-form-requests-validação)

**Componentes Vue**
→ [FRONTEND-ARCHITECTURE.md - Componentes](./FRONTEND-ARCHITECTURE.md#componentes)
→ [CRUD-EMPRESAS-REFERENCE.md - Componentes](./CRUD-EMPRESAS-REFERENCE.md#4-componente-de-formulário-empresaformvue)

**Upload de Arquivos**
→ [CRUD-EMPRESAS-REFERENCE.md - Upload](./CRUD-EMPRESAS-REFERENCE.md#-upload-de-arquivos)

**Composables**
→ [FRONTEND-ARCHITECTURE.md - Composables](./FRONTEND-ARCHITECTURE.md#composables)

**Wayfinder/Rotas**
→ [FRONTEND-ARCHITECTURE.md - Wayfinder](./FRONTEND-ARCHITECTURE.md#sistema-de-rotas-wayfinder)

**Migrations**
→ [DATABASE-SCHEMA.md - Migrations](./DATABASE-SCHEMA.md#estrutura-de-migrations)

**Relacionamentos**
→ [DATABASE-SCHEMA.md - Relacionamentos](./DATABASE-SCHEMA.md#relacionamentos)

**Debugging**
→ [DEVELOPMENT-GUIDE.md - Debugging](./DEVELOPMENT-GUIDE.md#debugging)

**Testes**
→ [DEVELOPMENT-GUIDE.md - Testes](./DEVELOPMENT-GUIDE.md#testes)

**Deploy**
→ [DEVELOPMENT-GUIDE.md - Deploy](./DEVELOPMENT-GUIDE.md#deploy)

---

## 📌 Dicas de Uso

### Para Máxima Produtividade:

1. **Bookmark** este arquivo (INDEX.md) para navegação rápida
2. **Mantenha aberto** [QUICK-REFERENCE.md](./QUICK-REFERENCE.md) durante desenvolvimento
3. **Consulte** documentos específicos quando precisar de detalhes
4. **Siga os exemplos** completos ao implementar novas features
5. **Leia "Boas Práticas"** em cada documento

### Durante Code Review:

Use a documentação como referência para:
- Verificar conformidade com padrões
- Sugerir melhorias baseadas em best practices
- Educar novos membros do time

### Atualizando Documentação:

Quando adicionar novas features ou padrões:
1. Atualize o documento relevante
2. Adicione exemplo se necessário
3. Atualize este índice se criar novo documento

---

## 🤝 Contribuindo

Encontrou algo que pode ser melhorado na documentação?

1. Abra uma issue ou PR
2. Siga o padrão de formatação existente
3. Adicione exemplos práticos
4. Atualize o índice se necessário

---

## 📝 Notas Finais

Esta documentação é **viva** e deve evoluir com o projeto. 

- ✅ **Use** como referência padrão
- 📖 **Consulte** quando em dúvida
- 🔄 **Atualize** quando o projeto mudar
- 🤝 **Compartilhe** com novos membros

**Documentação bem mantida = Time mais produtivo** 🚀

---

> Para começar agora, vá para: **[README.md](./README.md)** ou **[QUICK-REFERENCE.md](./QUICK-REFERENCE.md)**

