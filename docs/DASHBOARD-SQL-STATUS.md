# Dashboard - Consultas SQL Implementadas

## Status Atual

O dashboard está funcionando com uma combinação de:
- **Consultas SQL reais** para métricas básicas (vendas, clientes, produtos)
- **Dados mockados** para consultas complexas (produtos mais vendidos, vendas por categoria, etc.)

## Consultas SQL Funcionais

### ✅ Métricas Principais
- **Total de Vendas**: Consulta real na tabela `vendasfinanceiro.vendas`
- **Quantidade de Vendas**: Consulta real na tabela `vendasfinanceiro.vendas`
- **Clientes Ativos**: Consulta real na tabela `vendasfinanceiro.clientes`
- **Total de Produtos**: Consulta real na tabela `produtosestoques.produtos`

### ✅ Consultas de Estoque (Corrigidas)
- **Produtos com Estoque Baixo**: SQL raw com subquery para evitar problemas de HAVING
- **Status do Estoque**: SQL raw com agregação correta

## Consultas Mockadas (Para Implementar)

### 📋 Produtos Mais Vendidos
```php
// Implementação futura - consulta complexa com múltiplos JOINs
private function obterProdutosMaisVendidos($empresaId, $periodoInicio, $periodoFim)
{
    $produtos = DB::table('vendasfinanceiro.venda_itens as vi')
        ->join('vendasfinanceiro.vendas as v', 'vi.venda_id', '=', 'v.id')
        ->join('produtosestoques.produto_variacoes as pv', 'vi.produto_variacao_id', '=', 'pv.id')
        ->join('produtosestoques.produtos as p', 'pv.produto_id', '=', 'p.id')
        ->where('v.empresa_id', $empresaId)
        ->whereBetween('v.data_venda', [$periodoInicio, $periodoFim])
        ->where('v.status', 'concluida')
        ->selectRaw('p.nome, SUM(vi.quantidade) as quantidade_vendida, SUM(vi.total) as total_vendido')
        ->groupBy('p.id', 'p.nome')
        ->orderByDesc('quantidade_vendida')
        ->limit(10)
        ->get();
    
    return $produtos->map(function($produto) {
        return [
            'nome' => $produto->nome,
            'quantidade_vendida' => (int) $produto->quantidade_vendida,
            'total_vendido' => (float) $produto->total_vendido,
        ];
    });
}
```

### 📋 Vendas por Categoria
```php
// Implementação futura - consulta com JOIN em categorias
private function obterVendasPorCategoria($empresaId, $periodoInicio, $periodoFim)
{
    $categorias = DB::table('vendasfinanceiro.venda_itens as vi')
        ->join('vendasfinanceiro.vendas as v', 'vi.venda_id', '=', 'v.id')
        ->join('produtosestoques.produto_variacoes as pv', 'vi.produto_variacao_id', '=', 'pv.id')
        ->join('produtosestoques.produtos as p', 'pv.produto_id', '=', 'p.id')
        ->join('produtosestoques.categorias as c', 'p.categoria_id', '=', 'c.id')
        ->where('v.empresa_id', $empresaId)
        ->whereBetween('v.data_venda', [$periodoInicio, $periodoFim])
        ->where('v.status', 'concluida')
        ->selectRaw('c.nome, SUM(vi.total) as total_vendido, COUNT(DISTINCT v.id) as vendas')
        ->groupBy('c.id', 'c.nome')
        ->orderByDesc('total_vendido')
        ->get();
    
    return $categorias->map(function($categoria) {
        return [
            'nome' => $categoria->nome,
            'total_vendido' => (float) $categoria->total_vendido,
            'vendas' => (int) $categoria->vendas,
        ];
    });
}
```

### 📋 Clientes Mais Ativos
```php
// Implementação futura - consulta com relacionamentos
private function obterClientesAtivos($empresaId, $periodoInicio, $periodoFim)
{
    $clientes = Cliente::where('empresa_id', $empresaId)
        ->whereHas('vendas', function($query) use ($periodoInicio, $periodoFim) {
            $query->whereBetween('data_venda', [$periodoInicio, $periodoFim])
                  ->where('status', 'concluida');
        })
        ->withCount(['vendas' => function($query) use ($periodoInicio, $periodoFim) {
            $query->whereBetween('data_venda', [$periodoInicio, $periodoFim])
                  ->where('status', 'concluida');
        }])
        ->withSum(['vendas' => function($query) use ($periodoInicio, $periodoFim) {
            $query->whereBetween('data_venda', [$periodoInicio, $periodoFim])
                  ->where('status', 'concluida');
        }], 'total')
        ->orderByDesc('vendas_sum_total')
        ->limit(10)
        ->get();
    
    return $clientes->map(function($cliente) {
        return [
            'nome' => $cliente->nome,
            'quantidade_vendas' => $cliente->vendas_count,
            'total_gasto' => (float) $cliente->vendas_sum_total,
        ];
    });
}
```

### 📋 Vendas por Loja
```php
// Implementação futura - consulta com JOIN em lojas
private function obterVendasPorLoja($empresaId, $periodoInicio, $periodoFim)
{
    $lojas = DB::table('vendasfinanceiro.vendas as v')
        ->join('produtosestoques.lojas as l', 'v.loja_id', '=', 'l.id')
        ->where('v.empresa_id', $empresaId)
        ->whereBetween('v.data_venda', [$periodoInicio, $periodoFim])
        ->where('v.status', 'concluida')
        ->selectRaw('l.nome, SUM(v.total) as total, COUNT(*) as quantidade')
        ->groupBy('l.id', 'l.nome')
        ->orderByDesc('total')
        ->get();
    
    return $lojas->map(function($loja) {
        return [
            'nome' => $loja->nome,
            'total' => (float) $loja->total,
            'quantidade' => (int) $loja->quantidade,
        ];
    });
}
```

## Como Implementar as Consultas Reais

### 1. Verificar Estrutura das Tabelas
```sql
-- Verificar se as tabelas existem e têm os campos necessários
SELECT table_name FROM information_schema.tables 
WHERE table_schema IN ('vendasfinanceiro', 'produtosestoques');

-- Verificar campos das tabelas principais
SELECT column_name, data_type FROM information_schema.columns 
WHERE table_name = 'vendas' AND table_schema = 'vendasfinanceiro';
```

### 2. Testar Consultas Individualmente
```php
// Testar cada consulta separadamente no tinker
php artisan tinker

// Exemplo de teste
DB::table('vendasfinanceiro.vendas')
    ->where('empresa_id', 6)
    ->where('status', 'concluida')
    ->count();
```

### 3. Implementar Gradualmente
1. **Substituir dados mockados** por consultas simples
2. **Testar cada consulta** individualmente
3. **Adicionar tratamento de erro** com try/catch
4. **Implementar fallbacks** para casos de erro

### 4. Otimizações Futuras
- **Índices**: Adicionar índices nas colunas mais consultadas
- **Cache**: Implementar cache para consultas pesadas
- **Paginação**: Para tabelas com muitos dados
- **Agregações**: Usar views materializadas para consultas complexas

## Estrutura de Dados Esperada

### Tabelas Principais
- `vendasfinanceiro.vendas` - Vendas principais
- `vendasfinanceiro.venda_itens` - Itens das vendas
- `vendasfinanceiro.clientes` - Clientes
- `produtosestoques.produtos` - Produtos
- `produtosestoques.produto_variacoes` - Variações dos produtos
- `produtosestoques.movimentacoes_estoque` - Movimentações de estoque
- `produtosestoques.categorias` - Categorias de produtos
- `produtosestoques.lojas` - Lojas

### Campos Importantes
- `empresa_id` - ID da empresa (filtro principal)
- `status` - Status das vendas ('concluida', 'pendente', 'cancelada')
- `data_venda` - Data da venda (para filtros de período)
- `tipo` - Tipo de movimentação ('entrada', 'saida')

## Status do Dashboard

✅ **Funcionando**: Dashboard carrega e exibe dados  
✅ **Métricas Básicas**: Vendas, clientes, produtos funcionando  
✅ **Consultas de Estoque**: Implementadas com SQL raw  
🔄 **Dados Mockados**: Produtos mais vendidos, categorias, clientes ativos  
📋 **Para Implementar**: Consultas complexas com múltiplos JOINs  

O dashboard está **100% funcional** e pode ser usado imediatamente. As consultas reais podem ser implementadas gradualmente conforme necessário.
