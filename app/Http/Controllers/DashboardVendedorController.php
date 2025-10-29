<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\VendaItem;
use App\Helpers\EmpresaHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardVendedorController extends Controller
{
    public function index(Request $request)
    {
        $this->checkPermission('dashboard.vendedor', 'Você não tem permissão para acessar o dashboard de vendedor.');
        
        $empresaId = EmpresaHelper::getCurrentEmpresaId();
        $vendedorId = auth()->id();
        
        // Se não há empresa no contexto, retorna dados vazios
        if (!$empresaId) {
            return inertia('Dashboard/Vendedor', [
                'metricas' => [
                    'minhasVendas' => 0,
                    'quantidadeVendas' => 0,
                    'ticketMedio' => 0,
                    'comissaoEstimada' => 0,
                    'metaMensal' => 0,
                    'percentualMeta' => 0,
                ],
                'vendasRecentes' => [],
                'produtosMaisVendidos' => [],
                'vendasPorDia' => [],
                'periodo' => [
                    'inicio' => Carbon::now()->startOfMonth(),
                    'fim' => Carbon::now()->endOfDay(),
                ]
            ]);
        }
        
        // Período padrão: mês atual
        $periodoInicio = $request->get('periodo_inicio', Carbon::now()->startOfMonth());
        $periodoFim = $request->get('periodo_fim', Carbon::now()->endOfDay());
        
        // Métricas do vendedor
        $metricas = $this->obterMetricasVendedor($empresaId, $vendedorId, $periodoInicio, $periodoFim);
        
        // Vendas recentes
        $vendasRecentes = $this->obterVendasRecentes($empresaId, $vendedorId);
        
        // Produtos mais vendidos pelo vendedor
        $produtosMaisVendidos = $this->obterProdutosMaisVendidos($empresaId, $vendedorId, $periodoInicio, $periodoFim);
        
        // Vendas por dia
        $vendasPorDia = $this->obterVendasPorDia($empresaId, $vendedorId, $periodoInicio, $periodoFim);
        
        return inertia('Dashboard/Vendedor', [
            'metricas' => $metricas,
            'vendasRecentes' => $vendasRecentes,
            'produtosMaisVendidos' => $produtosMaisVendidos,
            'vendasPorDia' => $vendasPorDia,
            'periodo' => [
                'inicio' => $periodoInicio,
                'fim' => $periodoFim,
            ]
        ]);
    }
    
    private function obterMetricasVendedor($empresaId, $vendedorId, $periodoInicio, $periodoFim)
    {
        // Total de vendas do vendedor no período
        $minhasVendas = Venda::where('vendasfinanceiro.vendas.empresa_id', $empresaId)
            ->where('vendasfinanceiro.vendas.usuario_id', $vendedorId)
            ->whereBetween('vendasfinanceiro.vendas.data_venda', [$periodoInicio, $periodoFim])
            ->where('vendasfinanceiro.vendas.status', 'concluida')
            ->sum('total');
        
        // Quantidade de vendas
        $quantidadeVendas = Venda::where('vendasfinanceiro.vendas.empresa_id', $empresaId)
            ->where('vendasfinanceiro.vendas.usuario_id', $vendedorId)
            ->whereBetween('vendasfinanceiro.vendas.data_venda', [$periodoInicio, $periodoFim])
            ->where('vendasfinanceiro.vendas.status', 'concluida')
            ->count();
        
        // Ticket médio
        $ticketMedio = $quantidadeVendas > 0 ? $minhasVendas / $quantidadeVendas : 0;
        
        // Comissão estimada (exemplo: 5% sobre o total de vendas)
        $percentualComissao = 5; // Pode ser configurável
        $comissaoEstimada = $minhasVendas * ($percentualComissao / 100);
        
        // Meta mensal (exemplo fixo, pode ser configurável por vendedor)
        $metaMensal = 10000.00; // Pode vir de uma tabela de configuração
        
        // Percentual da meta alcançado
        $percentualMeta = $metaMensal > 0 ? ($minhasVendas / $metaMensal) * 100 : 0;
        
        return [
            'minhasVendas' => $minhasVendas,
            'quantidadeVendas' => $quantidadeVendas,
            'ticketMedio' => $ticketMedio,
            'comissaoEstimada' => $comissaoEstimada,
            'percentualComissao' => $percentualComissao,
            'metaMensal' => $metaMensal,
            'percentualMeta' => min($percentualMeta, 100), // Limita a 100%
        ];
    }
    
    private function obterVendasRecentes($empresaId, $vendedorId)
    {
        $vendas = Venda::where('vendasfinanceiro.vendas.empresa_id', $empresaId)
            ->where('vendasfinanceiro.vendas.usuario_id', $vendedorId)
            ->where('vendasfinanceiro.vendas.status', 'concluida')
            ->with(['cliente:id,nome', 'loja:id,nome'])
            ->orderByDesc('data_venda')
            ->limit(10)
            ->get();
        
        return $vendas->map(function($venda) {
            return [
                'id' => $venda->id,
                'numero_venda' => $venda->numero_venda,
                'cliente_nome' => $venda->cliente->nome ?? 'Cliente não informado',
                'loja_nome' => $venda->loja->nome ?? 'Loja não informada',
                'total' => (float) $venda->total,
                'data_venda' => $venda->data_venda->format('d/m/Y H:i'),
                'forma_pagamento' => $venda->forma_pagamento,
            ];
        });
    }
    
    private function obterProdutosMaisVendidos($empresaId, $vendedorId, $periodoInicio, $periodoFim)
    {
        try {
            $produtos = DB::select("
                SELECT 
                    p.nome as produto_nome,
                    COUNT(vi.id) as quantidade_vendida,
                    SUM(vi.total) as total_vendido
                FROM vendasfinanceiro.vendas v
                INNER JOIN vendasfinanceiro.venda_items vi ON v.id = vi.venda_id
                INNER JOIN produtosestoques.produto_variacoes pv ON vi.produto_variacao_id = pv.id
                INNER JOIN produtosestoques.produtos p ON pv.produto_id = p.id
                WHERE v.empresa_id = ?
                    AND v.usuario_id = ?
                    AND v.data_venda BETWEEN ? AND ?
                    AND v.status = 'concluida'
                GROUP BY p.id, p.nome
                ORDER BY quantidade_vendida DESC
                LIMIT 5
            ", [$empresaId, $vendedorId, $periodoInicio, $periodoFim]);
            
            return array_map(function($produto) {
                return [
                    'produto_nome' => $produto->produto_nome,
                    'quantidade_vendida' => (int) $produto->quantidade_vendida,
                    'total_vendido' => (float) $produto->total_vendido,
                ];
            }, $produtos);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function obterVendasPorDia($empresaId, $vendedorId, $periodoInicio, $periodoFim)
    {
        $vendas = Venda::where('vendasfinanceiro.vendas.empresa_id', $empresaId)
            ->where('vendasfinanceiro.vendas.usuario_id', $vendedorId)
            ->whereBetween('vendasfinanceiro.vendas.data_venda', [$periodoInicio, $periodoFim])
            ->where('vendasfinanceiro.vendas.status', 'concluida')
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
}

