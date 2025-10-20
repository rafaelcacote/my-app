# 📋 Sumário Executivo: CRUD de Empresas

> **Referência rápida para criação de novos CRUDs**

---

## 🎯 Visão Geral

O CRUD de Empresas é o **padrão de referência** para todos os CRUDs do sistema ModaFlow. Este documento serve como um guia rápido com links para a documentação completa.

📖 **[Documentação Completa →](./CRUD-EMPRESAS-REFERENCE.md)**

---

## 🏗️ Arquitetura

```
Backend (Laravel)          Frontend (Vue + Inertia)
├── Migration             ├── Pages
├── Model                 │   ├── Index.vue (listagem)
├── Controller            │   ├── Create.vue
├── Form Requests         │   └── Edit.vue
└── Routes                └── Components
                              ├── EmpresaForm.vue
                              └── FormActions.vue
```

---

## ✅ Checklist Rápido

### Backend
- [ ] Migration com schema correto
- [ ] Model com casts, accessors e scopes
- [ ] Controller resource completo
- [ ] StoreRequest e UpdateRequest
- [ ] Route::resource em web.php

### Frontend
- [ ] Interfaces TypeScript
- [ ] Página Index com filtros e paginação
- [ ] Página Create
- [ ] Página Edit
- [ ] Componente Form reutilizável
- [ ] Componente FormActions

---

## 📁 Arquivos do CRUD de Empresas

### Backend
| Arquivo | Caminho | Descrição |
|---------|---------|-----------|
| Migration | `database/migrations/2025_10_13_183601_create_empresas_table.php` | Schema do banco |
| Model | `app/Models/Empresa.php` | Model Eloquent |
| Controller | `app/Http/Controllers/EmpresaController.php` | Controller resource |
| StoreRequest | `app/Http/Requests/EmpresaStoreRequest.php` | Validação para criação |
| UpdateRequest | `app/Http/Requests/EmpresaUpdateRequest.php` | Validação para atualização |
| Routes | `routes/web.php` (linha 17) | Definição de rotas |

### Frontend
| Arquivo | Caminho | Descrição |
|---------|---------|-----------|
| Index | `resources/js/pages/empresas/Index.vue` | Listagem com filtros |
| Create | `resources/js/pages/empresas/Create.vue` | Formulário de criação |
| Edit | `resources/js/pages/empresas/Edit.vue` | Formulário de edição |
| EmpresaForm | `resources/js/components/empresas/EmpresaForm.vue` | Form reutilizável |
| FormActions | `resources/js/components/empresas/FormActions.vue` | Botões de ação |
| Routes | `resources/js/routes/empresas/index.ts` | Rotas geradas (Wayfinder) |
| Actions | `resources/js/actions/App/Http/Controllers/EmpresaController.ts` | Actions geradas (Wayfinder) |

---

## 🚀 Como Criar um Novo CRUD

### Passo 1: Backend

```bash
# 1. Criar migration
php artisan make:migration create_nome_table

# 2. Criar model
php artisan make:model Nome

# 3. Criar controller resource
php artisan make:controller NomeController --resource

# 4. Criar form requests
php artisan make:request NomeStoreRequest
php artisan make:request NomeUpdateRequest
```

### Passo 2: Frontend

```bash
# Criar estrutura de pastas
mkdir resources/js/pages/nome
mkdir resources/js/components/nome

# Criar arquivos
touch resources/js/pages/nome/Index.vue
touch resources/js/pages/nome/Create.vue
touch resources/js/pages/nome/Edit.vue
touch resources/js/components/nome/NomeForm.vue
touch resources/js/components/nome/FormActions.vue
```

### Passo 3: Rotas

```php
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('nome', NomeController::class);
});
```

### Passo 4: Gerar Rotas Tipadas

```bash
# Gerar rotas Wayfinder
npm run wayfinder
```

---

## 🔑 Funcionalidades Implementadas

### Listagem (Index)
- ✅ Paginação (15 itens/página)
- ✅ Busca em tempo real (debounce 300ms)
- ✅ Filtros por status
- ✅ Query string preservada
- ✅ Ordenação
- ✅ Dialog de confirmação para exclusão

### Criação (Create)
- ✅ Formulário completo
- ✅ Validação frontend e backend
- ✅ Upload de arquivo com preview
- ✅ Formatação automática de campos
- ✅ Toast notifications
- ✅ Valores padrão

### Edição (Edit)
- ✅ Carregamento de dados
- ✅ Atualização parcial
- ✅ Upload com substituição
- ✅ Feedback de salvamento
- ✅ Info card (UUID, data de cadastro)

### Exclusão (Destroy)
- ✅ Soft delete
- ✅ Confirmação visual
- ✅ Remoção de arquivos associados

---

## 📦 Recursos Especiais

### Upload de Arquivos
- Tamanho máximo: 2MB
- Formatos: jpeg, png, jpg, gif, webp
- Preview em tempo real
- Remoção automática ao excluir/atualizar

### Formatação de Campos
- CNPJ: `XX.XXX.XXX/XXXX-XX`
- Telefone: `(XX) XXXXX-XXXX`
- Data: `DD/MM/YYYY`

### Validações
- Frontend: Visual (bordas vermelhas)
- Backend: Form Requests
- Mensagens: Personalizadas em português

---

## 🎨 Componentes UI

### Utilizados
- Card (containers)
- Table (listagem)
- Button (ações)
- Input (formulário)
- Label (labels)
- Badge (status)
- Dialog (modais)
- Switch (toggle)

### Ícones (lucide-vue-next)
- Plus (adicionar)
- Search (busca)
- Pencil (editar)
- Trash2 (excluir)
- Loader2 (loading)

---

## 🔗 Links Úteis

### Documentação
- 📋 [Documentação Completa](./CRUD-EMPRESAS-REFERENCE.md)
- 🔧 [CRUD Pattern](./CRUD-PATTERN.md)
- ⚛️ [Frontend Architecture](./FRONTEND-ARCHITECTURE.md)
- 🗄️ [Database Schema](./DATABASE-SCHEMA.md)
- 🚀 [Development Guide](./DEVELOPMENT-GUIDE.md)

### Exemplos de Código
- [Model Empresa](./CRUD-EMPRESAS-REFERENCE.md#1-model-empresaphp)
- [Controller](./CRUD-EMPRESAS-REFERENCE.md#2-controller-empresacontrollerphp)
- [Validações](./CRUD-EMPRESAS-REFERENCE.md#3-form-requests-validação)
- [Página Index](./CRUD-EMPRESAS-REFERENCE.md#1-página-de-listagem-indexvue)
- [Componente Form](./CRUD-EMPRESAS-REFERENCE.md#4-componente-de-formulário-empresaformvue)

---

## 💡 Dicas Importantes

### Backend
- Use **Type hints** em todos os métodos
- Implemente **Form Requests** para validação
- Adicione **Soft Deletes** quando apropriado
- Crie **Query Scopes** para filtros comuns
- Use **Accessors** para formatação de dados

### Frontend
- Defina **Interfaces TypeScript** para todos os dados
- Use **Wayfinder** para rotas tipadas
- Implemente **debounce** em buscas (300ms)
- Preserve **query string** em filtros
- Adicione **loading states** durante processamento

### Geral
- Siga o **padrão estabelecido**
- Mantenha **consistência**
- Documente **alterações significativas**
- Teste **todas as funcionalidades**

---

## 🎯 Para IAs/Assistentes

Ao criar um novo CRUD, siga esta ordem:

1. **Analise** os requisitos específicos
2. **Copie** a estrutura do CRUD de Empresas
3. **Adapte** para o novo contexto
4. **Mantenha** os padrões estabelecidos
5. **Valide** com o checklist completo

📖 **Consulte sempre:** [CRUD-EMPRESAS-REFERENCE.md](./CRUD-EMPRESAS-REFERENCE.md)

---

## 📝 Convenções de Nomenclatura

### Backend
```
Model:          Empresa (singular, PascalCase)
Controller:     EmpresaController
Form Request:   EmpresaStoreRequest, EmpresaUpdateRequest
Migration:      create_empresas_table
Table:          empresas (plural, snake_case)
Route:          empresas (plural, kebab-case)
```

### Frontend
```
Pasta:          empresas (plural, kebab-case)
Páginas:        Index.vue, Create.vue, Edit.vue (PascalCase)
Componentes:    EmpresaForm.vue (PascalCase)
Interface:      Empresa (singular, PascalCase)
Route import:   empresas (plural, camelCase)
```

---

## 🚦 Status do Projeto

- ✅ Migration criada
- ✅ Model implementado
- ✅ Controller resource completo
- ✅ Validações implementadas
- ✅ Frontend completo
- ✅ Upload de arquivos funcionando
- ✅ Testes manuais realizados
- ✅ Documentação completa

---

**Criado por:** Sistema ModaFlow  
**Data:** Outubro 2025  
**Versão:** 1.0

> Esta documentação é um guia vivo. Mantenha-a atualizada! 🚀

