# Sistema de Contexto da Empresa para SaaS

Este documento descreve o sistema completo de contexto da empresa implementado para garantir que todos os dados sejam automaticamente vinculados à empresa do usuário logado.

## Visão Geral

O sistema funciona através de:
1. **Middleware**: Define automaticamente o contexto da empresa baseado no usuário logado
2. **Trait BelongsToEmpresa**: Adiciona automaticamente `empresa_id` aos modelos e filtra queries
3. **EmpresaHelper**: Facilita operações CRUD com verificação de empresa
4. **EmpresaContextService**: Gerencia o contexto da empresa na sessão

## Como Funciona

### 1. Middleware Automático

O middleware `SetEmpresaContext` é executado automaticamente em todas as requisições web e define o contexto da empresa baseado no usuário logado:

```php
// bootstrap/app.php
$middleware->web(append: [
    HandleAppearance::class,
    SetEmpresaContext::class, // ← Define contexto automaticamente
    HandleInertiaRequests::class,
    AddLinkHeadersForPreloadedAssets::class,
]);
```

### 2. Trait BelongsToEmpresa

Todos os modelos que pertencem a uma empresa devem usar este trait:

```php
use App\Traits\BelongsToEmpresa;

class Produto extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresa;
    
    protected $fillable = [
        'empresa_id', // ← Obrigatório
        'nome',
        'descricao',
        // ... outros campos
    ];
}
```

**O que o trait faz automaticamente:**
- Adiciona `empresa_id` na criação de registros
- Filtra todas as queries para mostrar apenas registros da empresa atual
- Fornece scopes úteis para queries específicas

### 3. EmpresaHelper - Métodos Principais

#### Criação Automática
```php
// Cria automaticamente vinculado à empresa atual
$categoria = EmpresaHelper::createForCurrentEmpresa(Categoria::class, [
    'nome' => 'Eletrônicos',
    'descricao' => 'Produtos eletrônicos',
    'ativo' => true,
]);
```

#### Busca com Verificação
```php
// Busca por ID verificando se pertence à empresa atual
$produto = EmpresaHelper::findForCurrentEmpresa(Produto::class, $id);

if (!$produto) {
    return response()->json(['message' => 'Produto não encontrado'], 404);
}
```

#### Atualização Segura
```php
// Atualiza verificando se pertence à empresa atual
$success = EmpresaHelper::updateForCurrentEmpresa($produto, [
    'nome' => 'Novo Nome',
    'preco_venda' => 99.90,
]);
```

#### Exclusão Segura
```php
// Deleta verificando se pertence à empresa atual
$success = EmpresaHelper::deleteForCurrentEmpresa($produto);
```

#### Queries Filtradas
```php
// Query que retorna apenas registros da empresa atual
$produtos = EmpresaHelper::queryForCurrentEmpresa(Produto::class)->get();
```

## Exemplos Práticos de Uso

### Controller Básico

```php
class ProdutoController extends Controller
{
    public function index()
    {
        // Automaticamente filtra por empresa_id
        $produtos = Produto::all();
        
        return response()->json($produtos);
    }
    
    public function store(Request $request)
    {
        // empresa_id é adicionado automaticamente pelo trait
        $produto = Produto::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco_venda' => $request->preco_venda,
        ]);
        
        return response()->json($produto);
    }
    
    public function show(int $id)
    {
        // Busca verificando se pertence à empresa atual
        $produto = EmpresaHelper::findForCurrentEmpresa(Produto::class, $id);
        
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }
        
        return response()->json($produto);
    }
    
    public function update(Request $request, int $id)
    {
        $produto = EmpresaHelper::findForCurrentEmpresa(Produto::class, $id);
        
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }
        
        // Atualiza com verificação de empresa
        EmpresaHelper::updateForCurrentEmpresa($produto, $request->validated());
        
        return response()->json($produto->fresh());
    }
    
    public function destroy(int $id)
    {
        $produto = EmpresaHelper::findForCurrentEmpresa(Produto::class, $id);
        
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }
        
        // Deleta com verificação de empresa
        EmpresaHelper::deleteForCurrentEmpresa($produto);
        
        return response()->json(['success' => true]);
    }
}
```

### Diferentes Formas de Fazer Queries

```php
// Método 1: Usando o trait (automático)
$produtos = Produto::all();

// Método 2: Usando o scope do trait
$produtos = Produto::forCurrentEmpresa()->get();

// Método 3: Usando o helper
$produtos = EmpresaHelper::queryForCurrentEmpresa(Produto::class)->get();

// Método 4: Query manual
$empresaId = EmpresaHelper::getCurrentEmpresaId();
$produtos = Produto::where('empresa_id', $empresaId)->get();
```

### Contexto Específico de Empresa

```php
// Executa operações em contexto de empresa específica
$resultado = EmpresaHelper::withEmpresaContext($empresaId, function () {
    $categorias = Categoria::all(); // Filtrado pela empresa do contexto
    $produtos = Produto::all(); // Filtrado pela empresa do contexto
    
    return [
        'categorias' => $categorias,
        'produtos' => $produtos,
    ];
});
```

## Modelos que Devem Usar o Sistema

Todos os modelos que pertencem a uma empresa devem:

1. **Usar o trait apropriado** (BelongsToEmpresa ou BelongsToEmpresaThroughLoja)
2. **Ter empresa_id no fillable** (ou relacionamento através de outro modelo)
3. **Ter empresa_id na migration** (ou relacionamento através de outro modelo)

### Modelos com Relacionamento Direto (empresa_id):
- ✅ Produto
- ✅ Categoria
- ✅ Marca
- ✅ Cor
- ✅ Tamanho
- ✅ Loja

### Modelos com Relacionamento Indireto (através de loja):
- ✅ MovimentacaoEstoque (através de loja_id)

### Modelos com Relacionamento Indireto (através de produto):
- ✅ ProdutoVariacao (através de produto_id)

### Modelos que NÃO precisam (são globais):
- User (já tem empresa_id)
- Empresa (é a própria empresa)
- Estado (dados globais)
- Municipio (dados globais)
- Endereco (pode ser compartilhado)

## Verificações de Segurança

O sistema garante que:

1. **Criação**: `empresa_id` é sempre adicionado automaticamente
2. **Leitura**: Apenas registros da empresa atual são retornados
3. **Atualização**: Só permite atualizar registros da empresa atual
4. **Exclusão**: Só permite deletar registros da empresa atual

## Tratamento de Erros

```php
try {
    $produto = EmpresaHelper::createForCurrentEmpresa(Produto::class, $data);
} catch (\Exception $e) {
    // Erro: "Nenhuma empresa encontrada no contexto atual"
    return response()->json(['error' => $e->getMessage()], 400);
}

try {
    EmpresaHelper::updateForCurrentEmpresa($produto, $data);
} catch (\Exception $e) {
    // Erro: "Este registro não pertence à empresa atual"
    return response()->json(['error' => $e->getMessage()], 403);
}
```

## Benefícios do Sistema

1. **Automático**: Não precisa lembrar de adicionar `empresa_id` manualmente
2. **Seguro**: Impossível acessar dados de outras empresas
3. **Consistente**: Todos os modelos seguem o mesmo padrão
4. **Flexível**: Permite operações em contexto específico quando necessário
5. **Transparente**: Funciona automaticamente sem código adicional

## Relacionamentos Indiretos

Alguns modelos podem não ter `empresa_id` diretamente, mas se relacionam com a empresa através de outros modelos. Por exemplo:

- **MovimentacaoEstoque** → **Loja** → **Empresa**

Para esses casos, use o trait `BelongsToEmpresaThroughLoja`:

```php
use App\Traits\BelongsToEmpresaThroughLoja;

class MovimentacaoEstoque extends Model
{
    use HasFactory, BelongsToEmpresaThroughLoja;
    
    protected $fillable = [
        'loja_id', // ← Relacionamento através da loja
        'produto_variacao_id',
        'tipo',
        'quantidade',
        // ... outros campos
    ];
    
    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }
}
```

**O que o trait BelongsToEmpresaThroughLoja faz:**
- Filtra queries através do relacionamento `loja.empresa_id`
- Fornece scopes para queries específicas
- Permite verificar se o modelo pertence à empresa atual
- Funciona automaticamente com o EmpresaHelper

### Exemplo de Uso com Relacionamento Indireto

```php
// Listagem automática (filtra por empresa através da loja)
$movimentacoes = MovimentacaoEstoque::all();

// Busca segura
$movimentacao = EmpresaHelper::findForCurrentEmpresa(MovimentacaoEstoque::class, $id);

// Query específica
$movimentacoes = MovimentacaoEstoque::forCurrentEmpresa()->get();
```

### Relacionamento Através de Produto

Alguns modelos se relacionam com empresa através do produto:

- **ProdutoVariacao** → **Produto** → **Empresa**

Para esses casos, use o trait `BelongsToEmpresaThroughProduto`:

```php
use App\Traits\BelongsToEmpresaThroughProduto;

class ProdutoVariacao extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresaThroughProduto;
    
    protected $fillable = [
        'produto_id', // ← Relacionamento através do produto
        'tamanho_id',
        'cor_id',
        'sku_variacao',
        'preco_adicional',
        'ativo',
    ];
    
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
```

**O que o trait BelongsToEmpresaThroughProduto faz:**
- Filtra queries através do relacionamento `produto.empresa_id`
- Fornece scopes para queries específicas
- Permite verificar se o modelo pertence à empresa atual
- Funciona automaticamente com o EmpresaHelper

### Exemplo de Uso com Relacionamento Através de Produto

```php
// Listagem automática (filtra por empresa através do produto)
$variacoes = ProdutoVariacao::all();

// Busca segura
$variacao = EmpresaHelper::findForCurrentEmpresa(ProdutoVariacao::class, $id);

// Query específica
$variacoes = ProdutoVariacao::forCurrentEmpresa()->get();
```

## Configuração Inicial

Para usar o sistema em um novo modelo:

1. **Adicione o trait**:
```php
use App\Traits\BelongsToEmpresa;

class MeuModelo extends Model
{
    use HasFactory, BelongsToEmpresa;
}
```

2. **Adicione empresa_id ao fillable**:
```php
protected $fillable = [
    'empresa_id',
    // ... outros campos
];
```

3. **Adicione empresa_id à migration**:
```php
$table->foreignId('empresa_id')->constrained('multitenancy.empresas');
```

4. **Use nos controllers**:
```php
// Criação automática
$modelo = MeuModelo::create($data);

// Busca segura
$modelo = EmpresaHelper::findForCurrentEmpresa(MeuModelo::class, $id);
```

## Conclusão

Este sistema garante que todos os dados sejam automaticamente vinculados à empresa do usuário logado, proporcionando:

- **Isolamento completo** entre empresas
- **Segurança** contra acesso não autorizado
- **Facilidade de uso** para desenvolvedores
- **Consistência** em todo o sistema

O sistema funciona automaticamente uma vez configurado, não requerendo código adicional na maioria dos casos.
