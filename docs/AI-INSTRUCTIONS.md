# 🤖 Instruções para IAs - Criação de CRUDs

> **Este documento é específico para assistentes de IA que irão criar novos CRUDs no sistema ModaFlow**

---

## 📋 Objetivo

Quando um desenvolvedor solicitar a criação de um novo CRUD, você deve seguir **exatamente** o padrão estabelecido no CRUD de Empresas, garantindo consistência e qualidade em todo o sistema.

---

## 🎯 Documento de Referência Principal

**📖 [CRUD-EMPRESAS-REFERENCE.md](./CRUD-EMPRESAS-REFERENCE.md)**

Este é o documento **mais importante** e completo. Ele contém:
- Toda a estrutura implementada
- Código completo de todos os arquivos
- Padrões estabelecidos
- Checklist completo
- Boas práticas

---

## 🔄 Fluxo de Trabalho

Quando receber uma solicitação para criar um novo CRUD, siga estes passos:

### 1️⃣ Análise dos Requisitos

```
✅ Identificar:
- Nome da entidade (singular e plural)
- Campos necessários
- Validações específicas
- Relacionamentos com outras tabelas
- Campos que precisam de formatação (CPF, telefone, etc)
- Campos que requerem upload de arquivos
- Regras de negócio específicas
```

### 2️⃣ Consultar a Documentação

```
📖 Documentos a consultar (em ordem):
1. CRUD-EMPRESAS-REFERENCE.md (principal)
2. CRUD-EMPRESAS-SUMMARY.md (checklist rápido)
3. CRUD-PATTERN.md (padrões gerais)
4. DATABASE-SCHEMA.md (para migrations)
5. FRONTEND-ARCHITECTURE.md (para frontend)
```

### 3️⃣ Implementação Backend

Seguir exatamente esta ordem:

```php
1. Migration
   ✅ Copiar estrutura de: CRUD-EMPRESAS-REFERENCE.md#estrutura-de-banco-de-dados
   ✅ Adaptar campos para nova entidade
   ✅ Manter: id, uuid, timestamps, softDeletes, índices
   
2. Model
   ✅ Copiar estrutura de: CRUD-EMPRESAS-REFERENCE.md#1-model-empresaphp
   ✅ Definir $fillable
   ✅ Definir $casts
   ✅ Criar accessors se necessário
   ✅ Adicionar scopes úteis
   
3. Form Requests (Store e Update)
   ✅ Copiar de: CRUD-EMPRESAS-REFERENCE.md#3-form-requests-validação
   ✅ Adaptar regras de validação
   ✅ Manter mensagens em português
   ✅ Tratar unique com soft deletes
   
4. Controller
   ✅ Copiar de: CRUD-EMPRESAS-REFERENCE.md#2-controller-empresacontrollerphp
   ✅ Implementar TODOS os métodos: index, create, store, show, edit, update, destroy
   ✅ Manter type hints
   ✅ Manter padrões de paginação e filtros
   
5. Routes
   ✅ Adicionar Route::resource em web.php
   ✅ Proteger com middleware auth
```

### 4️⃣ Implementação Frontend

```typescript
1. Criar Interfaces TypeScript
   ✅ Interface para a entidade
   ✅ Interface para PaginatedData
   ✅ Interface para Props
   
2. Criar Página Index.vue
   ✅ Copiar de: CRUD-EMPRESAS-REFERENCE.md#1-página-de-listagem-indexvue
   ✅ Adaptar campos da tabela
   ✅ Manter: filtros, busca, paginação, dialog de exclusão
   ✅ Manter: watchers com debounce
   
3. Criar Página Create.vue
   ✅ Copiar de: CRUD-EMPRESAS-REFERENCE.md#2-página-de-criação-createvue
   ✅ Definir valores padrão apropriados
   ✅ Usar useForm do Inertia
   ✅ Manter forceFormData se houver upload
   
4. Criar Página Edit.vue
   ✅ Copiar de: CRUD-EMPRESAS-REFERENCE.md#3-página-de-edição-editvue
   ✅ Manter Card de info (UUID, data)
   ✅ Usar Form component com Wayfinder
   
5. Criar Componente Form
   ✅ Copiar de: CRUD-EMPRESAS-REFERENCE.md#4-componente-de-formulário
   ✅ Adaptar campos específicos
   ✅ Manter formatação de campos
   ✅ Manter upload com preview (se aplicável)
   
6. Criar Componente FormActions
   ✅ Pode reutilizar de empresas/FormActions.vue
   ✅ Ou copiar de: CRUD-EMPRESAS-REFERENCE.md#5-componente-de-actions
```

### 5️⃣ Gerar Rotas Tipadas

```bash
# Sempre executar após criar rotas Laravel
npm run wayfinder
```

### 6️⃣ Validação

```
✅ Checklist de Validação:
□ Migration criada e executada
□ Model com todos os casts e fillables
□ Controller com todos os 7 métodos
□ Validações em português
□ 3 páginas Vue criadas (Index, Create, Edit)
□ Componentes reutilizáveis criados
□ Interfaces TypeScript definidas
□ Rotas Wayfinder geradas
□ Upload funcionando (se aplicável)
□ Filtros e busca funcionando
□ Paginação funcionando
□ Toast notifications funcionando
```

---

## 🎨 Padrões Obrigatórios

### Nomenclatura

```
✅ Backend:
- Model: PascalCase, Singular (Produto)
- Controller: PascalCase + Controller (ProdutoController)
- Migration: snake_case, plural (create_produtos_table)
- Table: snake_case, plural (produtos)
- Routes: kebab-case, plural (produtos)

✅ Frontend:
- Pasta: kebab-case, plural (produtos)
- Páginas: PascalCase (Index.vue, Create.vue, Edit.vue)
- Componentes: PascalCase (ProdutoForm.vue)
- Interfaces: PascalCase, Singular (Produto)
```

### Estrutura de Arquivos

```
Backend:
app/Models/Produto.php
app/Http/Controllers/ProdutoController.php
app/Http/Requests/ProdutoStoreRequest.php
app/Http/Requests/ProdutoUpdateRequest.php
database/migrations/YYYY_MM_DD_HHMMSS_create_produtos_table.php

Frontend:
resources/js/pages/produtos/Index.vue
resources/js/pages/produtos/Create.vue
resources/js/pages/produtos/Edit.vue
resources/js/components/produtos/ProdutoForm.vue
resources/js/components/produtos/FormActions.vue
```

### Funcionalidades Obrigatórias

```
✅ Index:
- Paginação (15 itens)
- Busca em tempo real (debounce 300ms)
- Filtros
- Ordenação (latest)
- Dialog de confirmação de exclusão

✅ Create:
- Validação frontend e backend
- Toast de sucesso/erro
- Redirect após criar

✅ Edit:
- Card de info
- Validação
- Feedback de salvamento recente
- Upload com substituição

✅ Destroy:
- Soft delete
- Confirmação obrigatória
- Remoção de arquivos associados
```

---

## ⚠️ Erros Comuns a Evitar

### ❌ NÃO FAÇA:

```
❌ Criar páginas sem TypeScript
❌ Omitir validações no backend
❌ Esquecer soft deletes
❌ Não adicionar índices importantes
❌ Criar formulários sem feedback visual
❌ Esquecer de tratar upload de arquivos
❌ Não preservar query string em filtros
❌ Misturar padrões diferentes
❌ Criar componentes não reutilizáveis
❌ Não adicionar loading states
```

### ✅ SEMPRE FAÇA:

```
✅ Use interfaces TypeScript
✅ Adicione mensagens de validação em português
✅ Implemente soft deletes
✅ Adicione índices em campos consultados
✅ Use Form Requests para validação
✅ Preserve query string em filtros
✅ Adicione debounce em buscas (300ms)
✅ Implemente confirmação de exclusão
✅ Use Wayfinder para rotas tipadas
✅ Adicione toast notifications
✅ Mantenha consistência com o padrão
```

---

## 📚 Exemplos de Código

### Migration Template

```php
Schema::create('nome_plural', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique()->default(DB::raw('gen_random_uuid()'));
    
    // Seus campos aqui
    
    $table->boolean('ativo')->default(true);
    $table->timestampsTz();
    $table->softDeletes();
    
    // Índices
    $table->index('ativo');
});
```

### Model Template

```php
class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        // seus campos
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'ativo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
```

### Controller Template

```php
class ProdutoController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Produto::query();
        
        // Filtros e busca aqui
        
        $produtos = $query->latest()->paginate(15)->withQueryString();
        
        return Inertia::render('produtos/Index', [
            'produtos' => $produtos,
            'filters' => $request->only(['search', 'status']),
        ]);
    }
    
    // Outros métodos: create, store, show, edit, update, destroy
}
```

---

## 🔍 Checklist de Qualidade

Antes de finalizar, verifique:

```
✅ Código:
□ Type hints em todos os métodos PHP
□ Interfaces TypeScript em todos os componentes Vue
□ Comentários em lógica complexa
□ Nomes descritivos de variáveis

✅ Funcionalidades:
□ Todas as operações CRUD funcionando
□ Validações funcionando
□ Filtros e busca funcionando
□ Upload funcionando (se aplicável)
□ Mensagens de feedback funcionando

✅ UX:
□ Loading states em operações assíncronas
□ Confirmação em ações destrutivas
□ Toast notifications claras
□ Formulários com validação visual
□ Responsive design

✅ Performance:
□ Índices em campos consultados
□ Paginação implementada
□ Queries otimizadas
□ Debounce em buscas

✅ Segurança:
□ Validação no backend
□ Proteção de rotas com middleware
□ Upload validado (tipo e tamanho)
□ CSRF protection (automático no Laravel)
```

---

## 💬 Comunicação com o Desenvolvedor

### Ao Iniciar

```
"Vou criar o CRUD de [ENTIDADE] seguindo exatamente o padrão 
estabelecido no CRUD de Empresas. Vou implementar:

Backend:
- Migration
- Model com casts e scopes
- Controller resource completo
- Form Requests (Store e Update)

Frontend:
- 3 páginas (Index, Create, Edit)
- Componentes reutilizáveis
- Interfaces TypeScript
- Filtros e paginação

Posso prosseguir?"
```

### Durante a Implementação

```
Informar o que está sendo feito:
"Criando migration com os campos X, Y, Z..."
"Implementando Controller com filtros..."
"Criando página Index com busca e paginação..."
```

### Ao Finalizar

```
"CRUD de [ENTIDADE] criado com sucesso! ✅

Arquivos criados:
- [lista de arquivos]

Funcionalidades implementadas:
- Listagem com paginação e filtros
- Criação com validação
- Edição
- Exclusão com confirmação

Para testar:
1. php artisan migrate
2. npm run wayfinder
3. Acessar /[entidade]

Deseja que eu adicione alguma funcionalidade específica?"
```

---

## 🚨 Situações Especiais

### Upload de Arquivos

```
Se o CRUD precisar de upload:
✅ Adicionar campo file no request
✅ Validar: mimes, max:2048
✅ Usar Storage::disk('public')->store()
✅ Criar accessor para URL pública
✅ Implementar preview no frontend
✅ Remover arquivo antigo ao atualizar
✅ Remover arquivo ao excluir
```

### Relacionamentos

```
Se houver relacionamentos:
✅ Definir no Model (belongsTo, hasMany, etc)
✅ Eager load quando necessário (with())
✅ Incluir foreign keys na migration
✅ Adicionar cascade ou restrict conforme regra de negócio
```

### Campos Calculados

```
Se houver campos calculados:
✅ Criar accessor no Model
✅ Adicionar ao $appends se deve sempre aparecer
✅ Documentar a lógica de cálculo
```

---

## 📖 Documentação de Referência

### Ordem de Consulta

1. **[CRUD-EMPRESAS-REFERENCE.md](./CRUD-EMPRESAS-REFERENCE.md)** - Documentação completa (USE PRIMEIRO)
2. **[CRUD-EMPRESAS-SUMMARY.md](./CRUD-EMPRESAS-SUMMARY.md)** - Checklist rápido
3. **[CRUD-PATTERN.md](./CRUD-PATTERN.md)** - Padrões gerais
4. **[DATABASE-SCHEMA.md](./DATABASE-SCHEMA.md)** - Para migrations
5. **[FRONTEND-ARCHITECTURE.md](./FRONTEND-ARCHITECTURE.md)** - Para frontend
6. **[DEVELOPMENT-GUIDE.md](./DEVELOPMENT-GUIDE.md)** - Para setup e debug

---

## 🎯 Objetivo Final

**Garantir que todos os CRUDs do sistema tenham:**
- ✅ Mesma estrutura
- ✅ Mesma qualidade
- ✅ Mesma UX
- ✅ Mesma segurança
- ✅ Mesma performance
- ✅ Mesma documentação

---

## 📝 Notas Finais

**Lembre-se:**
- Esta não é uma sugestão, é um **padrão obrigatório**
- **Não improvise**, siga o template
- **Não simplifique**, implemente completo
- **Não pule etapas**, siga o checklist
- **Não invente soluções**, use o padrão estabelecido

**Em caso de dúvida:**
- Consulte **[CRUD-EMPRESAS-REFERENCE.md](./CRUD-EMPRESAS-REFERENCE.md)**
- Siga **exatamente** o exemplo de Empresas
- Pergunte ao desenvolvedor se algo não estiver claro

---

**Criado para:** Assistentes de IA  
**Objetivo:** Manter consistência e qualidade nos CRUDs  
**Documento Base:** CRUD-EMPRESAS-REFERENCE.md

---

> **IMPORTANTE:** Este documento deve ser seguido RIGOROSAMENTE para garantir consistência no sistema! 🎯

