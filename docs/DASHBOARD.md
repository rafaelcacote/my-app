# Dashboard do Sistema de Loja de Roupas

## Visão Geral

Este dashboard foi desenvolvido especificamente para uma loja de roupas, oferecendo uma visão completa e elegante das métricas de negócio mais importantes. O dashboard utiliza componentes modernos do shadcn-vue e gráficos interativos com Chart.js.

## Funcionalidades

### 📊 Métricas Principais

- **Total de Vendas**: Valor total das vendas no período selecionado
- **Vendas Realizadas**: Quantidade de vendas concluídas
- **Ticket Médio**: Valor médio por venda
- **Clientes Ativos**: Número de clientes cadastrados no sistema

### 📈 Métricas Secundárias

- **Produtos com Estoque Baixo**: Produtos que necessitam reposição
- **Total de Produtos**: Quantidade de produtos cadastrados
- **Margem de Lucro Média**: Margem média dos produtos ativos

### 📊 Gráficos Interativos

1. **Vendas por Período**: Gráfico de linha mostrando a evolução das vendas ao longo do tempo
2. **Status do Estoque**: Gráfico de rosca com distribuição dos produtos por nível de estoque
3. **Produtos Mais Vendidos**: Gráfico de barras com top 10 produtos
4. **Vendas por Categoria**: Gráfico de rosca com distribuição por categoria

### 📋 Tabelas de Dados

- **Produtos Mais Vendidos**: Ranking dos produtos com maior quantidade vendida
- **Clientes Mais Ativos**: Ranking dos clientes com maior volume de compras
- **Vendas por Forma de Pagamento**: Distribuição por método de pagamento
- **Vendas por Loja**: Performance por unidade

### 🔍 Filtros e Períodos

- **Filtro de Período**: Permite selecionar períodos personalizados ou usar períodos rápidos (7, 30, 90 dias, 1 ano)
- **Comparação com Período Anterior**: Mostra tendências de crescimento/declínio

## Tecnologias Utilizadas

### Backend (Laravel)
- **Controller**: `DashboardController` com métodos otimizados para consultas
- **Models**: Utiliza os modelos existentes (Venda, Cliente, Produto, etc.)
- **Queries**: Consultas SQL otimizadas com agregações e joins

### Frontend (Vue.js + TypeScript)
- **Framework**: Vue 3 com Composition API
- **UI Components**: shadcn-vue (Card, Badge, Button, Table, etc.)
- **Gráficos**: Chart.js com vue-chartjs
- **Ícones**: Lucide Vue Next
- **Styling**: Tailwind CSS com estilos customizados

### Componentes Criados

#### Componentes de Gráficos
- `VendasPorPeriodoChart.vue`: Gráfico de linha com duas escalas
- `ProdutosMaisVendidosChart.vue`: Gráfico de barras colorido
- `StatusEstoqueChart.vue`: Gráfico de rosca com cores semânticas
- `VendasPorCategoriaChart.vue`: Gráfico de rosca com tooltips detalhados

#### Componentes de Dashboard
- `MetricCard.vue`: Card reutilizável para métricas
- `PeriodoFilter.vue`: Filtro de período com períodos rápidos
- `DashboardSkeleton.vue`: Loading state para melhor UX

## Estrutura de Dados

### Métricas Principais
```typescript
interface Metricas {
    totalVendas: number;
    quantidadeVendas: number;
    ticketMedio: number;
    clientesAtivos: number;
    produtosEstoqueBaixo: number;
    totalProdutos: number;
    margemLucroMedia: number;
}
```

### Dados dos Gráficos
```typescript
interface VendasPorPeriodo {
    data: string;
    total: number;
    quantidade: number;
}

interface ProdutosMaisVendidos {
    nome: string;
    quantidade_vendida: number;
    total_vendido: number;
}
```

## Design e UX

### Características Visuais
- **Design Moderno**: Cards com bordas arredondadas e sombras sutis
- **Cores Semânticas**: Verde para positivo, vermelho para negativo, amarelo para alertas
- **Animações**: Transições suaves e animações de entrada
- **Responsivo**: Layout adaptável para diferentes tamanhos de tela

### Acessibilidade
- **Contraste**: Cores com bom contraste para leitura
- **Foco**: Estados de foco visíveis para navegação por teclado
- **Tooltips**: Informações detalhadas nos gráficos

### Performance
- **Lazy Loading**: Componentes carregados sob demanda
- **Otimização de Queries**: Consultas SQL otimizadas no backend
- **Caching**: Possibilidade de implementar cache para métricas

## Configuração e Uso

### Instalação de Dependências
```bash
npm install chart.js vue-chartjs
```

### Rotas
```php
Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
```

### Controller
```php
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Lógica para obter métricas e dados
        return inertia('Dashboard/Index', $data);
    }
}
```

## Personalização

### Cores e Temas
As cores podem ser personalizadas através das variáveis CSS em `dashboard.css`:
- Cores dos gráficos
- Cores dos badges de status
- Cores das animações

### Métricas Adicionais
Para adicionar novas métricas:
1. Adicione a lógica no `DashboardController`
2. Crie o componente `MetricCard` correspondente
3. Atualize a interface TypeScript

### Gráficos Personalizados
Para criar novos gráficos:
1. Crie um novo componente em `components/charts/`
2. Configure Chart.js com os dados necessários
3. Adicione o componente na página Dashboard

## Melhorias Futuras

- [ ] Cache de métricas para melhor performance
- [ ] Exportação de relatórios em PDF/Excel
- [ ] Notificações em tempo real
- [ ] Dashboard personalizável por usuário
- [ ] Integração com APIs externas (previsão do tempo, feriados)
- [ ] Modo offline com dados em cache
- [ ] Análise de tendências com IA

## Suporte

Para dúvidas ou problemas:
1. Verifique os logs do Laravel
2. Consulte a documentação do Chart.js
3. Verifique a configuração do banco de dados
4. Teste as consultas SQL diretamente

---

**Desenvolvido com ❤️ para otimizar o gerenciamento de lojas de roupas**
