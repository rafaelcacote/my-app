# Dashboard do Vendedor

## Visão Geral

Este documento descreve as implementações realizadas para adicionar funcionalidades de vendas por vendedor no sistema, incluindo um dashboard específico e simplificado para usuários com perfil de vendedor.

## Alterações Realizadas

### 1. Dashboard Principal - Vendas por Vendedor

#### Backend (`app/Http/Controllers/DashboardController.php`)

**Novo Método:**
- `obterVendasPorVendedor()`: Retorna estatísticas de vendas agrupadas por vendedor (usuário que realizou a venda)

**Métricas Retornadas:**
- `vendedor_id`: ID do vendedor
- `vendedor_nome`: Nome do vendedor
- `quantidade_vendas`: Número de vendas realizadas
- `total_vendido`: Valor total vendido
- `ticket_medio`: Valor médio por venda

**Query SQL:**
```sql
SELECT 
    users.id as vendedor_id,
    users.name as vendedor_nome,
    COUNT(*) as quantidade_vendas,
    SUM(vendas.total) as total_vendido,
    AVG(vendas.total) as ticket_medio
FROM vendasfinanceiro.vendas
INNER JOIN shared.users ON vendas.usuario_id = users.id
WHERE vendas.empresa_id = ? 
    AND vendas.data_venda BETWEEN ? AND ?
    AND vendas.status = 'concluida'
GROUP BY users.id, users.name
ORDER BY total_vendido DESC
LIMIT 10
```

#### Frontend (`resources/js/pages/Dashboard.vue`)

**Nova Seção:**
- Card "Vendas por Vendedor" com tabela mostrando:
  - Ranking dos vendedores (badge com posição)
  - Avatar com inicial do nome
  - Nome do vendedor
  - Quantidade de vendas
  - Total vendido
  - Ticket médio

---

### 2. Dashboard Específico para Vendedores

#### Backend (`app/Http/Controllers/DashboardVendedorController.php`)

**Novo Controller** com métricas focadas no vendedor logado:

**Métricas Principais:**
- `minhasVendas`: Total de vendas do vendedor no período
- `quantidadeVendas`: Número de vendas realizadas
- `ticketMedio`: Valor médio por venda
- `comissaoEstimada`: Comissão calculada (5% sobre vendas)
- `metaMensal`: Meta de vendas (configurável, padrão R$ 10.000,00)
- `percentualMeta`: Progresso em relação à meta

**Dados Adicionais:**
- `vendasRecentes`: Últimas 10 vendas com detalhes (cliente, loja, forma pagamento)
- `produtosMaisVendidos`: Top 5 produtos vendidos pelo vendedor
- `vendasPorDia`: Vendas agrupadas por dia no período

#### Frontend (`resources/js/pages/Dashboard/Vendedor.vue`)

**Componentes Visuais:**

1. **Métricas Principais** (4 cards):
   - Minhas Vendas (total vendido)
   - Vendas Realizadas (quantidade)
   - Ticket Médio
   - Comissão Estimada (destaque em verde)

2. **Meta Mensal** (card com barra de progresso):
   - Barra de progresso visual
   - Valores: Vendido / Meta / Falta
   - Status motivacional baseado no percentual:
     - ≥100%: "Meta Atingida!" (verde)
     - ≥80%: "Quase lá!" (azul)
     - ≥50%: "No caminho!" (amarelo)
     - <50%: "Continue trabalhando!" (laranja)

3. **Minhas Vendas Recentes** (tabela):
   - Nº da venda
   - Cliente
   - Loja
   - Forma de pagamento (badge colorido)
   - Total
   - Data/hora

4. **Meus Produtos Mais Vendidos** (lista):
   - Ranking (top 5)
   - Nome do produto
   - Valor total vendido
   - Quantidade de unidades

5. **Vendas por Dia** (lista com scroll):
   - Data
   - Quantidade de vendas
   - Total vendido

---

### 3. Sistema de Permissões

#### Seeder (`database/seeders/PermissionsSeeder.php`)

**Novas Permissões:**
- `dashboard.index`: Acesso ao dashboard principal (Admin/Gerente)
- `dashboard.vendedor`: Acesso ao dashboard do vendedor

**Perfis (Roles) Criados:**

1. **Administrador**
   - Acesso total ao sistema
   - Todas as permissões

2. **Gerente**
   - Dashboard principal
   - Vendas (CRUD completo)
   - Clientes (CRUD completo)
   - Produtos (CRUD completo)
   - Categorias, Marcas, Fornecedores
   - Estoque (entrada/saída)
   - Lojas (visualização)

3. **Vendedor** (Novo)
   - Dashboard do vendedor
   - Vendas (criar, visualizar, listar)
   - Clientes (criar, visualizar, listar)
   - Produtos (visualizar, listar)
   - **NÃO pode**: editar/excluir vendas, gerenciar produtos/estoque

4. **Estoquista**
   - Dashboard principal
   - Produtos (CRUD completo)
   - Estoque (entrada/saída)
   - Fornecedores (visualização)

#### Como Executar o Seeder:

```bash
php artisan db:seed --class=PermissionsSeeder
```

---

### 4. Rotas

#### Novas Rotas (`routes/web.php`)

```php
// Dashboard do vendedor
Route::get('dashboard/vendedor', [DashboardVendedorController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.vendedor');
```

#### Redirecionamento Inteligente

A rota `/` agora redireciona automaticamente:
- **Vendedor** (sem outros perfis) → `/dashboard/vendedor`
- **Outros usuários** → `/dashboard`

```php
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('Vendedor') && !auth()->user()->hasAnyRole(['Administrador', 'Gerente'])) {
            return redirect()->route('dashboard.vendedor');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');
```

---

### 5. Componentes UI Adicionados

#### Progress Bar (`resources/js/components/ui/progress/`)

Componente criado usando Radix Vue para exibir a barra de progresso da meta mensal.

**Uso:**
```vue
<Progress :model-value="percentualMeta" class="h-3" />
```

---

## Sugestões de Melhorias Futuras

### Para o Dashboard do Vendedor

1. **Gamificação:**
   - Sistema de badges/conquistas
   - Rankings entre vendedores
   - Desafios semanais/mensais

2. **Metas Configuráveis:**
   - Permitir que o gerente configure metas individuais por vendedor
   - Metas por produto/categoria
   - Metas de ticket médio

3. **Notificações:**
   - Alerta quando estiver próximo da meta
   - Notificação de vendas concluídas
   - Alertas de produtos em falta

4. **Comparações:**
   - Comparar desempenho com período anterior
   - Comparar com média da equipe
   - Gráfico de evolução ao longo do tempo

5. **Comissões:**
   - Sistema de cálculo de comissões configurável
   - Diferentes percentuais por produto/categoria
   - Histórico de comissões recebidas

6. **Relatórios:**
   - Exportar relatórios em PDF
   - Envio automático de resumo por email
   - Relatórios de performance

7. **Análise de Clientes:**
   - Clientes frequentes do vendedor
   - Sugestões de follow-up
   - Oportunidades de venda cruzada

8. **Treinamento:**
   - Dicas de vendas
   - Informações sobre produtos
   - Técnicas de atendimento

---

## Como Usar

### 1. Criar um Usuário Vendedor

```php
// Criar usuário
$user = User::create([
    'name' => 'João Vendedor',
    'email' => 'joao@loja.com',
    'password' => bcrypt('senha123'),
    'empresa_id' => 1,
    'ativo' => true,
]);

// Atribuir role de vendedor
$user->assignRole('Vendedor');
```

### 2. Acessar o Dashboard

- **Vendedor:** Ao fazer login, será redirecionado automaticamente para `/dashboard/vendedor`
- **Outros perfis:** Podem acessar o dashboard principal em `/dashboard`

### 3. Verificar Permissões

```php
// No controller ou blade
if (auth()->user()->can('dashboard.vendedor')) {
    // Usuário pode acessar dashboard do vendedor
}

// No Vue
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const permissions = page.props.auth.user.permissions;

if (permissions.includes('dashboard.vendedor')) {
    // Usuário pode acessar
}
```

---

## Estrutura de Arquivos Criados/Modificados

```
app/
├── Http/
│   └── Controllers/
│       ├── DashboardController.php (modificado)
│       └── DashboardVendedorController.php (novo)
│
database/
└── seeders/
    └── PermissionsSeeder.php (novo)
│
resources/
└── js/
    ├── components/
    │   └── ui/
    │       └── progress/ (novo)
    │           ├── Progress.vue
    │           └── index.ts
    └── pages/
        ├── Dashboard.vue (modificado)
        └── Dashboard/
            └── Vendedor.vue (novo)
│
routes/
└── web.php (modificado)
│
docs/
└── DASHBOARD-VENDEDOR.md (este arquivo)
```

---

## Troubleshooting

### Problema: Vendedor não vê o dashboard

**Solução:**
1. Verificar se o usuário tem o role "Vendedor"
2. Executar o seeder de permissões
3. Limpar cache de permissões: `php artisan permission:cache-reset`

### Problema: Erro "Permission denied"

**Solução:**
1. Verificar se a permissão `dashboard.vendedor` existe
2. Verificar se o role tem a permissão atribuída
3. Executar: `php artisan db:seed --class=PermissionsSeeder`

### Problema: Dados não aparecem no dashboard

**Solução:**
1. Verificar se há vendas registradas com `usuario_id`
2. Verificar se o período está correto
3. Verificar se as vendas têm status 'concluida'
4. Verificar logs: `storage/logs/laravel.log`

---

## Testes Recomendados

1. **Teste de Permissões:**
   - Criar usuário com role "Vendedor"
   - Tentar acessar `/dashboard` (deve ser bloqueado ou redirecionado)
   - Acessar `/dashboard/vendedor` (deve funcionar)

2. **Teste de Dados:**
   - Criar vendas para diferentes vendedores
   - Verificar se cada vendedor vê apenas suas vendas
   - Verificar cálculos de comissão e meta

3. **Teste de UI:**
   - Verificar responsividade em mobile
   - Testar com diferentes quantidades de vendas
   - Testar com vendas zeradas

---

**Desenvolvido para otimizar o acompanhamento de vendas e motivar vendedores!**

