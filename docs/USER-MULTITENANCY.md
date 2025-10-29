# Sistema de Multi-Tenancy para Usuários

Este documento descreve o sistema de multi-tenancy implementado no CRUD de usuários, que garante isolamento de dados entre empresas (tenants).

## Visão Geral

O sistema implementa as seguintes regras de negócio:

### Para Usuários Normais (não super admin)
- ✅ Visualizam apenas usuários da mesma empresa (tenant_id)
- ✅ Podem cadastrar usuários apenas para sua própria empresa
- ✅ Podem editar apenas usuários da mesma empresa
- ✅ Podem excluir apenas usuários da mesma empresa
- ✅ O campo empresa é automaticamente preenchido com a empresa do usuário logado
- ✅ O seletor de empresa não é exibido (automaticamente selecionado)

### Para Super Admin (admin@admin.com)
- ✅ Visualiza todos os usuários de todas as empresas
- ✅ Pode cadastrar usuários para qualquer empresa
- ✅ Pode editar usuários de qualquer empresa
- ✅ Pode excluir usuários de qualquer empresa
- ✅ Possui filtro de empresa na listagem
- ✅ Pode selecionar qualquer empresa ao criar/editar usuários

## Implementação Técnica

### 1. Modelo User (`app/Models/User.php`)

Adicionados métodos auxiliares:

```php
/**
 * Verifica se o usuário é o super admin (admin@admin.com).
 */
public function isSuperAdmin(): bool
{
    return $this->email === 'admin@admin.com';
}

/**
 * Scope para filtrar usuários pela empresa.
 */
public function scopeByEmpresa($query, $empresaId)
{
    return $query->where('empresa_id', $empresaId);
}
```

### 2. Controller (`app/Http/Controllers/UserController.php`)

#### Método `index()` - Listagem
```php
$currentUser = auth()->user();
$query = User::with(['empresa', 'lojas']);

// Se não for super admin, filtrar apenas usuários da mesma empresa
if (!$currentUser->isSuperAdmin()) {
    $query->where('empresa_id', $currentUser->empresa_id);
}
```

#### Método `create()` - Formulário de Criação
```php
$currentUser = auth()->user();

// Buscar empresas (todas para super admin, apenas a do usuário para outros)
if ($currentUser->isSuperAdmin()) {
    $empresas = Empresa::ativas()->orderBy('razao_social')->get();
} else {
    $empresas = Empresa::where('id', $currentUser->empresa_id)->get();
}
```

#### Método `store()` - Criação de Usuário
```php
$currentUser = auth()->user();
$data = $request->validated();

// Se não for super admin, forçar empresa_id do usuário logado
if (!$currentUser->isSuperAdmin()) {
    $data['empresa_id'] = $currentUser->empresa_id;
}

// Validar que empresa_id foi fornecido
if (empty($data['empresa_id'])) {
    return back()->withErrors(['empresa_id' => 'A empresa é obrigatória.']);
}

// Se não for super admin, validar que a empresa é a mesma do usuário
if (!$currentUser->isSuperAdmin() && $data['empresa_id'] != $currentUser->empresa_id) {
    return back()->withErrors(['empresa_id' => 'Você só pode criar usuários para sua própria empresa.']);
}
```

#### Método `edit()` - Formulário de Edição
```php
$currentUser = auth()->user();

// Se não for super admin, verificar se o usuário pertence à mesma empresa
if (!$currentUser->isSuperAdmin() && $user->empresa_id != $currentUser->empresa_id) {
    abort(403, 'Você não tem permissão para editar este usuário.');
}
```

#### Método `update()` - Atualização de Usuário
```php
$currentUser = auth()->user();

// Se não for super admin, verificar se o usuário pertence à mesma empresa
if (!$currentUser->isSuperAdmin() && $user->empresa_id != $currentUser->empresa_id) {
    return back()->withErrors(['error' => 'Você não tem permissão para editar este usuário.']);
}

// Se não for super admin, forçar empresa_id do usuário logado
if (!$currentUser->isSuperAdmin()) {
    $data['empresa_id'] = $currentUser->empresa_id;
}
```

#### Método `show()` - Visualização
```php
$currentUser = auth()->user();

// Se não for super admin, verificar se o usuário pertence à mesma empresa
if (!$currentUser->isSuperAdmin() && $user->empresa_id != $currentUser->empresa_id) {
    abort(403, 'Você não tem permissão para visualizar este usuário.');
}
```

#### Método `destroy()` - Exclusão
```php
$currentUser = auth()->user();

// Não permitir exclusão do próprio usuário
if ($user->id === $currentUser->id) {
    return back()->withErrors(['error' => 'Você não pode excluir seu próprio usuário.']);
}

// Se não for super admin, verificar se o usuário pertence à mesma empresa
if (!$currentUser->isSuperAdmin() && $user->empresa_id != $currentUser->empresa_id) {
    return back()->withErrors(['error' => 'Você não tem permissão para excluir este usuário.']);
}
```

### 3. Frontend - Componentes Vue

#### `Index.vue` - Listagem
- Recebe `isSuperAdmin` do backend
- Exibe filtro de empresa apenas para super admin
- Ajusta grid de filtros baseado em `isSuperAdmin` (2 ou 3 colunas)

```vue
<div :class="isSuperAdmin ? 'grid gap-4 md:grid-cols-3' : 'grid gap-4 md:grid-cols-2'">
    <!-- Search -->
    <!-- Status -->
    <!-- Empresa (apenas para super admin) -->
    <div v-if="isSuperAdmin" class="space-y-2">
        <!-- ... -->
    </div>
</div>
```

#### `Create.vue` e `Edit.vue` - Formulários
- Recebem `isSuperAdmin` do backend
- Passam para o componente `UserForm`

```vue
<UserForm 
    :form="form"
    :empresas="empresas"
    :roles="roles"
    :isSuperAdmin="isSuperAdmin"
/>
```

#### `UserForm.vue` - Formulário de Dados
- Recebe prop `isSuperAdmin`
- Se não for super admin:
  - Campo de busca de empresa é ocultado
  - Empresa é selecionada automaticamente se houver apenas uma
  - Botão "Alterar" empresa é ocultado
- Se for super admin:
  - Campo de busca completo é exibido
  - Pode selecionar qualquer empresa

```vue
<!-- Campo de busca (apenas para super admin) -->
<div v-if="isSuperAdmin" class="relative">
    <Input
        v-model="empresaSearch"
        placeholder="Digite para buscar empresa..."
    />
</div>

<!-- Botão alterar (apenas para super admin) -->
<Button
    v-if="isSuperAdmin"
    type="button"
    @click="selecionarEmpresa('')"
>
    Alterar
</Button>
```

Auto-seleção de empresa:
```javascript
onMounted(() => {
    // ... código existente ...
    
    // Se não for super admin e houver apenas uma empresa, selecionar automaticamente
    if (!props.isSuperAdmin && props.empresas.length === 1) {
        selecionarEmpresa(props.empresas[0].id.toString(), true);
    }
});
```

## Fluxo de Dados

### Criação de Usuário (Não Super Admin)
1. Usuário acessa `/users/create`
2. Backend retorna apenas a empresa do usuário logado em `empresas`
3. Frontend seleciona automaticamente essa empresa
4. Usuário preenche dados e submete
5. Backend força `empresa_id` para a empresa do usuário logado
6. Validações impedem mudança de empresa

### Criação de Usuário (Super Admin)
1. Usuário acessa `/users/create`
2. Backend retorna todas as empresas ativas
3. Frontend exibe campo de busca de empresas
4. Super admin seleciona empresa desejada
5. Backend aceita qualquer empresa válida

### Listagem de Usuários (Não Super Admin)
1. Usuário acessa `/users`
2. Backend filtra automaticamente por `empresa_id`
3. Frontend não exibe filtro de empresa
4. Apenas usuários da mesma empresa são listados

### Listagem de Usuários (Super Admin)
1. Usuário acessa `/users`
2. Backend retorna todos os usuários
3. Frontend exibe filtro de empresa
4. Super admin pode filtrar por empresa específica

## Validações de Segurança

### Backend (Laravel)
1. ✅ Verificação de `isSuperAdmin()` em todos os métodos
2. ✅ Filtragem automática por `empresa_id` em queries
3. ✅ Validação de pertencimento antes de editar/excluir
4. ✅ Forçar `empresa_id` em criação/atualização
5. ✅ Retorno HTTP 403 para acessos não autorizados

### Frontend (Vue)
1. ✅ Campo de empresa ajustado baseado em `isSuperAdmin`
2. ✅ Auto-seleção de empresa quando aplicável
3. ✅ Filtros condicionais na listagem
4. ✅ Interface adapta-se ao tipo de usuário

## Casos de Teste

### Teste 1: Usuário Normal - Listagem
- ✅ Deve ver apenas usuários da sua empresa
- ✅ Não deve ver filtro de empresa
- ✅ Busca deve funcionar apenas em usuários da empresa

### Teste 2: Usuário Normal - Criação
- ✅ Empresa deve ser auto-selecionada
- ✅ Não deve poder alterar empresa
- ✅ Backend deve forçar empresa_id

### Teste 3: Usuário Normal - Edição
- ✅ Só deve conseguir editar usuários da mesma empresa
- ✅ Não deve poder alterar empresa do usuário
- ✅ Acesso a usuários de outras empresas deve retornar 403

### Teste 4: Usuário Normal - Exclusão
- ✅ Só deve conseguir excluir usuários da mesma empresa
- ✅ Tentativa de excluir usuário de outra empresa deve falhar

### Teste 5: Super Admin - Listagem
- ✅ Deve ver todos os usuários
- ✅ Deve ter filtro de empresa
- ✅ Filtro deve funcionar corretamente

### Teste 6: Super Admin - Criação
- ✅ Deve poder selecionar qualquer empresa
- ✅ Campo de busca deve estar disponível
- ✅ Deve poder criar usuário para qualquer empresa

### Teste 7: Super Admin - Edição
- ✅ Deve poder editar usuários de qualquer empresa
- ✅ Deve poder alterar empresa do usuário
- ✅ Deve ter acesso completo

### Teste 8: Super Admin - Exclusão
- ✅ Deve poder excluir usuários de qualquer empresa
- ✅ Não deve poder excluir a si mesmo

## Identificação do Super Admin

O super admin é identificado pelo email: `admin@admin.com`

Para adicionar novos super admins ou mudar a lógica, altere o método:
```php
// app/Models/User.php
public function isSuperAdmin(): bool
{
    return $this->email === 'admin@admin.com';
    
    // Alternativas:
    // return $this->hasRole('super-admin');
    // return in_array($this->email, config('app.super_admins'));
    // return $this->tipo === 'super_admin';
}
```

## Benefícios da Implementação

1. **Segurança**: Isolamento completo de dados entre empresas
2. **Simplicidade**: Interface adapta-se automaticamente ao tipo de usuário
3. **Flexibilidade**: Super admin mantém controle total
4. **UX**: Usuários normais não veem opções desnecessárias
5. **Manutenibilidade**: Lógica centralizada e reutilizável
6. **Escalabilidade**: Preparado para crescimento multi-tenant

## Arquivos Modificados

### Backend
- `app/Models/User.php` - Adicionados métodos `isSuperAdmin()` e `scopeByEmpresa()`
- `app/Http/Controllers/UserController.php` - Implementada lógica de multi-tenancy

### Frontend
- `resources/js/pages/users/Index.vue` - Filtro condicional de empresa
- `resources/js/pages/users/Create.vue` - Passa `isSuperAdmin` para formulário
- `resources/js/pages/users/Edit.vue` - Passa `isSuperAdmin` para formulário
- `resources/js/components/users/UserForm.vue` - Interface adaptativa baseada em `isSuperAdmin`

## Manutenção Futura

Para manter ou expandir este sistema:

1. **Adicionar novos campos**: Verificar se precisam de restrições por empresa
2. **Novos tipos de usuário**: Considerar se precisam de regras específicas
3. **Auditoria**: Considerar adicionar logs de ações entre empresas
4. **Testes**: Manter testes automatizados para validar isolamento
5. **Documentação**: Atualizar este documento com mudanças

## Conclusão

O sistema de multi-tenancy implementado garante que:

- Usuários normais operam apenas dentro do contexto de sua empresa
- O super admin mantém controle total sobre todas as empresas
- A interface se adapta automaticamente ao tipo de usuário
- Todas as operações são validadas tanto no frontend quanto no backend
- O isolamento de dados é garantido em todas as camadas da aplicação

