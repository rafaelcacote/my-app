<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use App\Models\MovimentacaoEstoque;
use App\Models\EntradaMercadoria;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Loja;
use App\Helpers\EmpresaHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $this->checkPermission('dashboard.index', 'Você não tem permissão para acessar o dashboard.');
        
        $empresaId = EmpresaHelper::getCurrentEmpresaId();
        
        // Se não há empresa no contexto, retorna dados vazios
        if (!$empresaId) {
            return inertia('Dashboard/Index', [
                'metricas' => [
                    'totalVendas' => 0,
                    'quantidadeVendas' => 0,
                    'ticketMedio' => 0,
                    'clientesAtivos' => 0,
                    'produtosEstoqueBaixo' => 0,
                    'totalProdutos' => 0,
                    'margemLucroMedia' => 0,
                ],
                'vendasPorPeriodo' => [],
                'produtosMaisVendidos' => [],
                'vendasPorCategoria' => [],
                'vendasPorFormaPagamento' => [],
                'statusEstoque' => [
                    'sem_estoque' => 0,
                    'estoque_baixo' => 0,
                    'estoque_medio' => 0,
                    'estoque_alto' => 0,
                ],
                'clientesAtivos' => [],
                'vendasPorLoja' => [],
                'tendencias' => [
                    'variacao_vendas' => 0,
                    'variacao_quantidade' => 0,
                    'vendas_atual' => 0,
                    'vendas_anterior' => 0,
                    'quantidade_atual' => 0,
                    'quantidade_anterior' => 0,
                ],
                'periodo' => [
                    'inicio' => Carbon::now()->subDays(30)->startOfDay(),
                    'fim' => Carbon::now()->endOfDay(),
                ]
            ]);
        }
        
        // Período padrão: últimos 30 dias
        $periodoInicio = $request->get('periodo_inicio', Carbon::now()->subDays(30)->startOfDay());
        $periodoFim = $request->get('periodo_fim', Carbon::now()->endOfDay());
        
        // Métricas principais
        $metricas = $this->obterMetricasPrincipais($empresaId, $periodoInicio, $periodoFim);
        
        // Vendas por período
        $vendasPorPeriodo = $this->obterVendasPorPeriodo($empresaId, $periodoInicio, $periodoFim);
        
        // Produtos mais vendidos
        $produtosMaisVendidos = $this->obterProdutosMaisVendidos($empresaId, $periodoInicio, $periodoFim);
        
        // Vendas por categoria
        $vendasPorCategoria = $this->obterVendasPorCategoria($empresaId, $periodoInicio, $periodoFim);
        
        // Vendas por forma de pagamento
        $vendasPorFormaPagamento = $this->obterVendasPorFormaPagamento($empresaId, $periodoInicio, $periodoFim);
        
        // Status do estoque
        $statusEstoque = $this->obterStatusEstoque($empresaId);
        
        // Clientes ativos
        $clientesAtivos = $this->obterClientesAtivos($empresaId, $periodoInicio, $periodoFim);
        
        // Vendas por loja
        $vendasPorLoja = $this->obterVendasPorLoja($empresaId, $periodoInicio, $periodoFim);
        
        // Tendências de vendas (comparação com período anterior)
        $tendencias = $this->obterTendencias($empresaId, $periodoInicio, $periodoFim);
        
        // Vendas por vendedor
        $vendasPorVendedor = $this->obterVendasPorVendedor($empresaId, $periodoInicio, $periodoFim);
        
        return inertia('Dashboard/Index', [
            'metricas' => $metricas,
            'vendasPorPeriodo' => $vendasPorPeriodo,
            'produtosMaisVendidos' => $produtosMaisVendidos,
            'vendasPorCategoria' => $vendasPorCategoria,
            'vendasPorFormaPagamento' => $vendasPorFormaPagamento,
            'statusEstoque' => $statusEstoque,
            'clientesAtivos' => $clientesAtivos,
            'vendasPorLoja' => $vendasPorLoja,
            'vendasPorVendedor' => $vendasPorVendedor,
            'tendencias' => $tendencias,
            'periodo' => [
                'inicio' => $periodoInicio,
                'fim' => $periodoFim,
            ]
        ]);
    }
    
    private function obterMetricasPrincipais($empresaId, $periodoInicio, $periodoFim)
    {
        // Total de vendas no período
        $totalVendas = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$periodoInicio, $periodoFim])
            ->where('status', 'concluida')
            ->sum('total');
        
        // Quantidade de vendas
        $quantidadeVendas = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$periodoInicio, $periodoFim])
            ->where('status', 'concluida')
            ->count();
        
        // Ticket médio
        $ticketMedio = $quantidadeVendas > 0 ? $totalVendas / $quantidadeVendas : 0;
        
        // Total de clientes ativos
        $clientesAtivos = Cliente::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->count();
        
        // Produtos com estoque baixo - consulta corrigida
        try {
            $produtosEstoqueBaixo = DB::select("
                SELECT COUNT(*) as total
                FROM (
                    SELECT pv.id
                    FROM produtosestoques.produto_variacoes pv
                    INNER JOIN produtosestoques.produtos p ON pv.produto_id = p.id
                    INNER JOIN produtosestoques.movimentacoes_estoque me ON pv.id = me.produto_variacao_id
                    WHERE p.empresa_id = ? AND p.controle_estoque = true
                    GROUP BY pv.id
                    HAVING SUM(CASE WHEN me.tipo = 'entrada' THEN me.quantidade ELSE -me.quantidade END) <= 5
                ) as subquery
            ", [$empresaId]);
            
            $produtosEstoqueBaixo = $produtosEstoqueBaixo[0]->total ?? 0;
        } catch (\Exception $e) {
            $produtosEstoqueBaixo = 0; // Fallback em caso de erro
        }
        
        // Total de produtos ativos
        $totalProdutos = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->count();
        
        // Margem de lucro média
        $margemLucroMedia = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->avg('margem_lucro');
        
        return [
            'totalVendas' => $totalVendas,
            'quantidadeVendas' => $quantidadeVendas,
            'ticketMedio' => $ticketMedio,
            'clientesAtivos' => $clientesAtivos,
            'produtosEstoqueBaixo' => $produtosEstoqueBaixo,
            'totalProdutos' => $totalProdutos,
            'margemLucroMedia' => $margemLucroMedia ?? 0,
        ];
    }
    
    private function obterVendasPorPeriodo($empresaId, $periodoInicio, $periodoFim)
    {
        $vendas = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$periodoInicio, $periodoFim])
            ->where('status', 'concluida')
            ->selectRaw('DATE(data_venda) as data, SUM(total) as total, COUNT(*) as quantidade')
            ->groupBy('data')
            ->orderBy('data')
            ->get();
        
        return $vendas->map(function($venda) {
            return [
                'data' => $venda->data,
                'total' => (float) $venda->total,
                'quantidade' => (int) $venda->quantidade,
            ];
        });
    }
    
    private function obterProdutosMaisVendidos($empresaId, $periodoInicio, $periodoFim)
    {
        // Versão simplificada - retorna dados mockados por enquanto
        return [
            [
                'nome' => 'Camiseta Básica',
                'quantidade_vendida' => 25,
                'total_vendido' => 1250.00,
            ],
            [
                'nome' => 'Calça Jeans',
                'quantidade_vendida' => 18,
                'total_vendido' => 1800.00,
            ],
            [
                'nome' => 'Vestido Floral',
                'quantidade_vendida' => 12,
                'total_vendido' => 960.00,
            ],
        ];
    }
    
    private function obterVendasPorCategoria($empresaId, $periodoInicio, $periodoFim)
    {
        // Versão simplificada - retorna dados mockados por enquanto
        return [
            [
                'nome' => 'Camisetas',
                'total_vendido' => 2500.00,
                'vendas' => 15,
            ],
            [
                'nome' => 'Calças',
                'total_vendido' => 3200.00,
                'vendas' => 12,
            ],
            [
                'nome' => 'Vestidos',
                'total_vendido' => 1800.00,
                'vendas' => 8,
            ],
        ];
    }
    
    private function obterVendasPorFormaPagamento($empresaId, $periodoInicio, $periodoFim)
    {
        $formasPagamento = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$periodoInicio, $periodoFim])
            ->where('status', 'concluida')
            ->selectRaw('forma_pagamento, SUM(total) as total, COUNT(*) as quantidade')
            ->groupBy('forma_pagamento')
            ->orderByDesc('total')
            ->get();
        
        return $formasPagamento->map(function($forma) {
            return [
                'forma_pagamento' => $forma->forma_pagamento,
                'total' => (float) $forma->total,
                'quantidade' => (int) $forma->quantidade,
            ];
        });
    }
    
    private function obterStatusEstoque($empresaId)
    {
        try {
            $produtosComEstoque = DB::select("
                SELECT 
                    pv.id,
                    SUM(CASE WHEN me.tipo = 'entrada' THEN me.quantidade ELSE -me.quantidade END) as estoque_atual
                FROM produtosestoques.produto_variacoes pv
                INNER JOIN produtosestoques.produtos p ON pv.produto_id = p.id
                INNER JOIN produtosestoques.movimentacoes_estoque me ON pv.id = me.produto_variacao_id
                WHERE p.empresa_id = ? AND p.controle_estoque = true
                GROUP BY pv.id
            ", [$empresaId]);
            
            $resultado = [
                'sem_estoque' => 0,
                'estoque_baixo' => 0,
                'estoque_medio' => 0,
                'estoque_alto' => 0,
            ];
            
            foreach ($produtosComEstoque as $produto) {
                $estoque = (int) $produto->estoque_atual;
                
                if ($estoque <= 0) {
                    $resultado['sem_estoque']++;
                } elseif ($estoque <= 5) {
                    $resultado['estoque_baixo']++;
                } elseif ($estoque <= 20) {
                    $resultado['estoque_medio']++;
                } else {
                    $resultado['estoque_alto']++;
                }
            }
            
            return $resultado;
        } catch (\Exception $e) {
            // Fallback em caso de erro
            return [
                'sem_estoque' => 0,
                'estoque_baixo' => 0,
                'estoque_medio' => 0,
                'estoque_alto' => 0,
            ];
        }
    }
    
    private function obterClientesAtivos($empresaId, $periodoInicio, $periodoFim)
    {
        // Versão simplificada - retorna dados mockados por enquanto
        return [
            [
                'nome' => 'Maria Silva',
                'quantidade_vendas' => 5,
                'total_gasto' => 1250.00,
            ],
            [
                'nome' => 'João Santos',
                'quantidade_vendas' => 3,
                'total_gasto' => 890.00,
            ],
            [
                'nome' => 'Ana Costa',
                'quantidade_vendas' => 4,
                'total_gasto' => 1100.00,
            ],
        ];
    }
    
    private function obterVendasPorLoja($empresaId, $periodoInicio, $periodoFim)
    {
        // Versão simplificada - retorna dados mockados por enquanto
        return [
            [
                'nome' => 'Loja Centro',
                'total' => 4500.00,
                'quantidade' => 25,
            ],
            [
                'nome' => 'Loja Shopping',
                'total' => 3200.00,
                'quantidade' => 18,
            ],
        ];
    }
    
    private function obterTendencias($empresaId, $periodoInicio, $periodoFim)
    {
        $diasPeriodo = Carbon::parse($periodoInicio)->diffInDays(Carbon::parse($periodoFim));
        $periodoAnteriorInicio = Carbon::parse($periodoInicio)->subDays($diasPeriodo);
        $periodoAnteriorFim = Carbon::parse($periodoInicio)->subDay();
        
        // Vendas período atual
        $vendasAtual = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$periodoInicio, $periodoFim])
            ->where('status', 'concluida')
            ->sum('total');
        
        // Vendas período anterior
        $vendasAnterior = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$periodoAnteriorInicio, $periodoAnteriorFim])
            ->where('status', 'concluida')
            ->sum('total');
        
        // Quantidade de vendas período atual
        $quantidadeAtual = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$periodoInicio, $periodoFim])
            ->where('status', 'concluida')
            ->count();
        
        // Quantidade de vendas período anterior
        $quantidadeAnterior = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$periodoAnteriorInicio, $periodoAnteriorFim])
            ->where('status', 'concluida')
            ->count();
        
        $variacaoVendas = $vendasAnterior > 0 ? (($vendasAtual - $vendasAnterior) / $vendasAnterior) * 100 : 0;
        $variacaoQuantidade = $quantidadeAnterior > 0 ? (($quantidadeAtual - $quantidadeAnterior) / $quantidadeAnterior) * 100 : 0;
        
        return [
            'variacao_vendas' => $variacaoVendas,
            'variacao_quantidade' => $variacaoQuantidade,
            'vendas_atual' => $vendasAtual,
            'vendas_anterior' => $vendasAnterior,
            'quantidade_atual' => $quantidadeAtual,
            'quantidade_anterior' => $quantidadeAnterior,
        ];
    }
    
    private function obterVendasPorVendedor($empresaId, $periodoInicio, $periodoFim)
    {
        // Removemos o global scope para evitar ambiguidade de empresa_id
        $vendas = Venda::withoutGlobalScope('empresa')
            ->where('vendasfinanceiro.vendas.empresa_id', $empresaId)
            ->whereBetween('vendasfinanceiro.vendas.data_venda', [$periodoInicio, $periodoFim])
            ->where('vendasfinanceiro.vendas.status', 'concluida')
            ->join('shared.users', 'vendasfinanceiro.vendas.usuario_id', '=', 'shared.users.id')
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
}
