# Sistema de Contexto de Empresa - SaaS Multi-Tenant

Este sistema implementa um contexto automático de empresa para garantir que todos os dados sejam filtrados pela empresa do usuário logado.

## 🏗️ Arquitetura

### 1. EmpresaContextService
**Arquivo:** `app/Services/EmpresaContextService.php`

Gerencia o contexto da empresa na sessão:
- Define automaticamente a empresa baseada no usuário logado
- Armazena o ID da empresa na sessão
- Valida se o usuário tem acesso à empresa

### 2. Middleware SetEmpresaContext
**Arquivo:** `app/Http/Middleware/SetEmpresaContext.php`

Executa automaticamente em todas as requisições autenticadas:
- Define o contexto da empresa baseado no usuário logado
- Registrado no `bootstrap/app.php`

### 3. Trait BelongsToEmpresa
**Arquivo:** `app/Traits/BelongsToEmpresa.php`

Adiciona funcionalidades automáticas aos modelos:
- **Global Scope:** Filtra automaticamente todas as consultas por `empresa_id`
- **Auto-fill:** Adiciona `empresa_id` automaticamente na criação
- **Relacionamento:** Define o relacionamento com `Empresa`
- **Scopes:** Métodos para filtrar por empresa específica

### 4. Helper EmpresaHelper
**Arquivo:** `app/Helpers/EmpresaHelper.php`

Facilita o uso do contexto nos controllers:
- `getCurrentEmpresaId()`: Obtém o ID da empresa atual
- `getCurrentEmpresa()`: Obtém a empresa atual
- `hasContext()`: Verifica se há contexto ativo
- `refreshContext()`: Força atualização do contexto

## 🚀 Como Usar

### 1. Aplicar o Trait aos Modelos

```php
<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class MeuModelo extends Model
{
    use BelongsToEmpresa;
    
    protected $fillable = [
        'empresa_id', // Campo obrigatório
        'nome',
        'descricao',
    ];
}
```

### 2. Usar nos Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Helpers\EmpresaHelper;
use App\Models\MeuModelo;

class MeuController extends Controller
{
    public function index()
    {
        // Automaticamente filtra pela empresa do usuário
        $dados = MeuModelo::all();
        
        // Ou explicitamente
        $dados = MeuModelo::forCurrentEmpresa()->get();
        
        // Para empresa específica
        $dados = MeuModelo::forEmpresa(1)->get();
        
        return response()->json($dados);
    }
    
    public function store(Request $request)
    {
        // empresa_id é adicionado automaticamente
        $modelo = MeuModelo::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            // empresa_id é preenchido automaticamente
        ]);
        
        return response()->json($modelo);
    }
}
```

### 3. Verificar Contexto

```php
use App\Helpers\EmpresaHelper;

// Verificar se há contexto
if (EmpresaHelper::hasContext()) {
    $empresaId = EmpresaHelper::getCurrentEmpresaId();
    $empresa = EmpresaHelper::getCurrentEmpresa();
}

// Forçar atualização do contexto
EmpresaHelper::refreshContext();
```

## 🔧 Funcionalidades Automáticas

### Global Scope
Todas as consultas são automaticamente filtradas:

```php
// Esta consulta já filtra pela empresa do usuário
$lojas = Loja::all();

// Equivale a:
$lojas = Loja::where('empresa_id', $empresaIdAtual)->get();
```

### Auto-fill na Criação
O `empresa_id` é preenchido automaticamente:

```php
// empresa_id é adicionado automaticamente
$loja = Loja::create([
    'nome' => 'Nova Loja',
    'cnpj' => '12345678000199',
]);
```

### Scopes Disponíveis

```php
// Para empresa específica
$lojas = Loja::forEmpresa(1)->get();

// Para empresa atual (redundante, mas explícito)
$lojas = Loja::forCurrentEmpresa()->get();

// Verificar se pertence à empresa atual
$loja = Loja::find(1);
if ($loja->belongsToCurrentEmpresa()) {
    // A loja pertence à empresa atual
}
```

## 📋 Checklist para Novos Modelos

1. ✅ Adicionar `empresa_id` na tabela
2. ✅ Adicionar `empresa_id` no `$fillable`
3. ✅ Usar o trait `BelongsToEmpresa`
4. ✅ Testar criação e consulta

## 🔒 Segurança

- **Validação de Acesso:** O sistema verifica se o usuário tem acesso à empresa
- **Isolamento de Dados:** Cada empresa só vê seus próprios dados
- **Contexto Automático:** Não é possível "esquecer" de filtrar por empresa

## 🎯 Benefícios

1. **Automático:** Não precisa lembrar de filtrar por empresa
2. **Seguro:** Impossível acessar dados de outras empresas
3. **Consistente:** Funciona igual em todos os modelos
4. **Flexível:** Permite override quando necessário
5. **Transparente:** Funciona sem modificar a lógica existente

## 📝 Exemplo Completo

Veja o arquivo `app/Http/Controllers/ExemploController.php` para exemplos práticos de uso.
