# Troubleshooting - Queries SQL

## Problema: Coluna Ambígua em JOINs

### Sintoma

```
SQLSTATE[42702]: Ambiguous column: 7 ERRO: referência à coluna "empresa_id" é ambígua
```

### Causa

Este erro ocorre quando:
1. Você faz um JOIN entre duas tabelas que têm a mesma coluna (ex: `empresa_id`)
2. O model usa o trait `BelongsToEmpresa` que adiciona um global scope
3. O global scope adiciona `where('empresa_id', ...)` sem especificar a tabela
4. O PostgreSQL não sabe qual `empresa_id` usar (da tabela A ou B)

### Exemplo de Erro

```php
// ❌ ERRADO - Causa ambiguidade
$vendas = Venda::where('empresa_id', $empresaId)
    ->join('shared.users', 'vendas.usuario_id', '=', 'users.id')
    ->get();
// Erro: Qual empresa_id? Da tabela vendas ou users?
```

### Soluções

#### Solução 1: Especificar Tabela em Todas as Colunas (Recomendado para JOINs)

```php
// ✅ CORRETO - Especifica a tabela
$vendas = Venda::where('vendasfinanceiro.vendas.empresa_id', $empresaId)
    ->whereBetween('vendasfinanceiro.vendas.data_venda', [$inicio, $fim])
    ->where('vendasfinanceiro.vendas.status', 'concluida')
    ->join('shared.users', 'vendasfinanceiro.vendas.usuario_id', '=', 'shared.users.id')
    ->get();
```

#### Solução 2: Remover Global Scope

```php
// ✅ CORRETO - Remove o scope global e adiciona a condição manualmente
$vendas = Venda::withoutGlobalScope('empresa')
    ->where('vendasfinanceiro.vendas.empresa_id', $empresaId)
    ->join('shared.users', 'vendasfinanceiro.vendas.usuario_id', '=', 'shared.users.id')
    ->get();
```

#### Solução 3: Usar Alias de Tabela

```php
// ✅ CORRETO - Usa alias
$vendas = Venda::from('vendasfinanceiro.vendas as v')
    ->withoutGlobalScope('empresa')
    ->where('v.empresa_id', $empresaId)
    ->join('shared.users as u', 'v.usuario_id', '=', 'u.id')
    ->select('v.*', 'u.name as vendedor_nome')
    ->get();
```

### Boas Práticas

#### ✅ Sempre faça:

1. **Em queries com JOIN**: Especifique sempre a tabela nas colunas do WHERE
   ```php
   ->where('tabela.coluna', $valor)
   ```

2. **Com global scopes**: Considere usar `withoutGlobalScope()` quando fizer JOINs
   ```php
   Model::withoutGlobalScope('empresa')->where('tabela.empresa_id', $id)
   ```

3. **No SELECT com JOIN**: Sempre especifique qual coluna vem de qual tabela
   ```php
   ->select('vendas.id', 'users.name', 'vendas.total')
   ```

4. **Use aliases**: Em queries complexas, use aliases de tabela
   ```php
   ->from('vendas as v')->join('users as u', ...)
   ```

#### ❌ Evite:

1. **WHERE sem especificar tabela em JOINs**
   ```php
   // ❌ ERRADO
   ->where('empresa_id', $id) // Qual empresa_id?
   ```

2. **Misturar estilos**
   ```php
   // ❌ ERRADO
   ->where('vendas.empresa_id', $id)
   ->where('status', 'ativo') // Falta a tabela aqui!
   ```

3. **Confiar apenas no global scope em JOINs**
   ```php
   // ❌ ERRADO - Global scope pode causar ambiguidade
   Model::join('other_table', ...) // Sem especificar tabelas
   ```

### Checklist para Queries com JOIN

- [ ] Especifiquei a tabela em todas as colunas do WHERE?
- [ ] Especifiquei a tabela nas colunas do SELECT?
- [ ] Removi global scopes se necessário?
- [ ] Testei a query diretamente no banco?
- [ ] A query funciona tanto no desenvolvimento quanto em produção?

### Exemplo Completo Corrigido

```php
private function obterVendasPorVendedor($empresaId, $periodoInicio, $periodoFim)
{
    // Remove o global scope para evitar ambiguidade
    $vendas = Venda::withoutGlobalScope('empresa')
        // Especifica a tabela em TODAS as colunas
        ->where('vendasfinanceiro.vendas.empresa_id', $empresaId)
        ->whereBetween('vendasfinanceiro.vendas.data_venda', [$periodoInicio, $periodoFim])
        ->where('vendasfinanceiro.vendas.status', 'concluida')
        // JOIN com nomes completos de tabela
        ->join('shared.users', 'vendasfinanceiro.vendas.usuario_id', '=', 'shared.users.id')
        // SELECT especifica a origem de cada coluna
        ->selectRaw('
            shared.users.id as vendedor_id,
            shared.users.name as vendedor_nome,
            COUNT(*) as quantidade_vendas,
            SUM(vendasfinanceiro.vendas.total) as total_vendido,
            AVG(vendasfinanceiro.vendas.total) as ticket_medio
        ')
        ->groupBy('shared.users.id', 'shared.users.name')
        ->orderByDesc('total_vendido')
        ->limit(10)
        ->get();
    
    return $vendas->map(function($venda) {
        return [
            'vendedor_id' => $venda->vendedor_id,
            'vendedor_nome' => $venda->vendedor_nome,
            'quantidade_vendas' => (int) $venda->quantidade_vendas,
            'total_vendido' => (float) $venda->total_vendido,
            'ticket_medio' => (float) $venda->ticket_medio,
        ];
    });
}
```

### Testando Queries

#### No Tinker

```bash
php artisan tinker
```

```php
// Ver a SQL gerada
DB::enableQueryLog();

// Sua query aqui
$result = Venda::where('vendasfinanceiro.vendas.empresa_id', 1)
    ->join('shared.users', 'vendasfinanceiro.vendas.usuario_id', '=', 'shared.users.id')
    ->get();

// Ver SQL executada
DB::getQueryLog();
```

#### Diretamente no PostgreSQL

```sql
-- Testar a query diretamente
SELECT 
    shared.users.id as vendedor_id,
    shared.users.name as vendedor_nome,
    COUNT(*) as quantidade_vendas,
    SUM(vendasfinanceiro.vendas.total) as total_vendido,
    AVG(vendasfinanceiro.vendas.total) as ticket_medio
FROM vendasfinanceiro.vendas
INNER JOIN shared.users ON vendasfinanceiro.vendas.usuario_id = shared.users.id
WHERE vendasfinanceiro.vendas.empresa_id = 1
  AND vendasfinanceiro.vendas.status = 'concluida'
  AND vendasfinanceiro.vendas.deleted_at IS NULL
GROUP BY shared.users.id, shared.users.name
ORDER BY total_vendido DESC
LIMIT 10;
```

### Outros Problemas Comuns

#### 1. Soft Deletes em JOINs

```php
// ✅ CORRETO - Especifica a tabela para soft deletes
->whereNull('vendasfinanceiro.vendas.deleted_at')
```

#### 2. Datas em JOINs

```php
// ✅ CORRETO
->whereBetween('vendasfinanceiro.vendas.data_venda', [$inicio, $fim])
```

#### 3. Ordenação em JOINs

```php
// ✅ CORRETO
->orderBy('vendasfinanceiro.vendas.created_at', 'desc')
// ou
->orderByDesc('total_vendido') // Se for um campo calculado/agregado
```

---

## Referências

- [Laravel Query Builder - Joins](https://laravel.com/docs/queries#joins)
- [Laravel Global Scopes](https://laravel.com/docs/eloquent#global-scopes)
- [PostgreSQL - Ambiguous Column](https://www.postgresql.org/docs/current/sql-expressions.html)

---

**Última atualização:** Outubro 2025

