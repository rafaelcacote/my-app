<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendaStoreRequest;
use App\Http\Requests\VendaUpdateRequest;
use App\Models\Venda;
use App\Models\Cliente;
use App\Models\Loja;
use App\Models\ProdutoVariacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VendaController extends Controller
{
    /**
     * Display a listing of vendas.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('vendas.index', 'Você não tem permissão para visualizar vendas.');
        
        $query = Venda::with(['cliente', 'loja', 'usuario']);

        // Filtro por status
        if ($request->has('status')) {
            if ($request->status !== 'all') {
                $query->where('status', $request->status);
            }
        }

        // Filtro por loja
        if ($request->has('loja_id') && $request->loja_id) {
            $query->where('loja_id', $request->loja_id);
        }

        // Filtro por período
        if ($request->filled('data_inicio')) {
            $query->where('data_venda', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->where('data_venda', '<=', $request->data_fim);
        }

        // Busca por número da venda ou nome do cliente
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_venda', 'ilike', "%{$search}%")
                    ->orWhereHas('cliente', function ($clienteQuery) use ($search) {
                        $clienteQuery->where('nome', 'ilike', "%{$search}%");
                    });
            });
        }

        $vendas = $query->latest('data_venda')->paginate(15)->withQueryString();

        // Dados para filtros
        $lojas = Loja::where('ativo', true)->get(['id', 'nome']);

        return Inertia::render('vendas/Index', [
            'vendas' => $vendas,
            'lojas' => $lojas,
            'filters' => $request->only(['search', 'status', 'loja_id', 'data_inicio', 'data_fim']),
        ]);
    }

    /**
     * Show the form for creating a new venda.
     */
    public function create(): Response
    {
        $this->checkPermission('vendas.create', 'Você não tem permissão para criar vendas.');
        
        $clientes = Cliente::ativos()->get(['id', 'nome', 'tipo']);
        $lojas = Loja::where('ativo', true)->get(['id', 'nome']);
        $produtos = ProdutoVariacao::with(['produto', 'cor', 'tamanho'])
            ->whereHas('produto', function ($query) {
                $query->where('ativo', true);
            })
            ->get();

        return Inertia::render('vendas/Create', [
            'clientes' => $clientes,
            'lojas' => $lojas,
            'produtos' => $produtos,
        ]);
    }

    /**
     * Store a newly created venda.
     */
    public function store(VendaStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('vendas.store', 'Você não tem permissão para criar vendas.');
        
        $data = $request->validated();
        
        // Adiciona dados do contexto
        $data['empresa_id'] = auth()->user()->empresa_id;
        $data['usuario_id'] = auth()->id();
        $data['data_venda'] = $data['data_venda'] ?? now();

        // Gera número da venda se não fornecido
        if (empty($data['numero_venda'])) {
            $data['numero_venda'] = $this->gerarNumeroVenda();
        }

        $venda = Venda::create($data);

        // Adiciona itens da venda
        if ($request->has('itens') && is_array($request->itens)) {
            foreach ($request->itens as $item) {
                $venda->adicionarItem(
                    $item['produto_variacao_id'],
                    $item['quantidade'],
                    $item['preco_unitario'],
                    $item['desconto'] ?? 0
                );
            }
        }

        // Cria pagamento se especificado
        if ($request->has('pagamento')) {
            $venda->pagamentos()->create([
                'forma_pagamento' => $data['forma_pagamento'],
                'valor' => $data['total'],
                'status' => 'pago',
                'data_pagamento' => $data['data_venda'],
            ]);
        }

        return to_route('vendas.show', $venda)
            ->with('success', 'Venda cadastrada com sucesso!');
    }

    /**
     * Display the specified venda.
     */
    public function show(Venda $venda): Response
    {
        $this->checkPermission('vendas.show', 'Você não tem permissão para visualizar vendas.');
        
        $venda->load(['cliente', 'loja', 'usuario', 'itens.produtoVariacao.produto', 'itens.produtoVariacao.cor', 'itens.produtoVariacao.tamanho', 'pagamentos']);

        return Inertia::render('vendas/Show', [
            'venda' => $venda,
        ]);
    }

    /**
     * Show the form for editing the specified venda.
     */
    public function edit(Venda $venda): Response
    {
        $this->checkPermission('vendas.edit', 'Você não tem permissão para editar vendas.');
        
        if (!$venda->podeSerCancelada()) {
            return to_route('vendas.index')
                ->with('error', 'Esta venda não pode ser editada.');
        }

        $venda->load(['itens.produtoVariacao.produto', 'itens.produtoVariacao.cor', 'itens.produtoVariacao.tamanho']);
        
        $clientes = Cliente::ativos()->get(['id', 'nome', 'tipo']);
        $lojas = Loja::where('ativo', true)->get(['id', 'nome']);
        $produtos = ProdutoVariacao::with(['produto', 'cor', 'tamanho'])
            ->whereHas('produto', function ($query) {
                $query->where('ativo', true);
            })
            ->get();

        return Inertia::render('vendas/Edit', [
            'venda' => $venda,
            'clientes' => $clientes,
            'lojas' => $lojas,
            'produtos' => $produtos,
        ]);
    }

    /**
     * Update the specified venda.
     */
    public function update(VendaUpdateRequest $request, Venda $venda): RedirectResponse
    {
        $this->checkPermission('vendas.update', 'Você não tem permissão para editar vendas.');
        
        if (!$venda->podeSerCancelada()) {
            return to_route('vendas.index')
                ->with('error', 'Esta venda não pode ser editada.');
        }

        $data = $request->validated();
        
        $venda->update($data);

        // Atualiza itens se fornecidos
        if ($request->has('itens') && is_array($request->itens)) {
            // Remove itens existentes
            $venda->itens()->delete();
            
            // Adiciona novos itens
            foreach ($request->itens as $item) {
                $venda->adicionarItem(
                    $item['produto_variacao_id'],
                    $item['quantidade'],
                    $item['preco_unitario'],
                    $item['desconto'] ?? 0
                );
            }
        }

        return to_route('vendas.show', $venda)
            ->with('success', 'Venda atualizada com sucesso!');
    }

    /**
     * Remove the specified venda (soft delete).
     */
    public function destroy(Venda $venda): RedirectResponse
    {
        $this->checkPermission('vendas.delete', 'Você não tem permissão para excluir vendas.');
        
        if (!$venda->podeSerCancelada()) {
            return to_route('vendas.index')
                ->with('error', 'Esta venda não pode ser excluída.');
        }

        $venda->delete();

        return to_route('vendas.index')
            ->with('success', 'Venda excluída com sucesso!');
    }

    /**
     * Concluir venda pendente.
     */
    public function concluir(Venda $venda): RedirectResponse
    {
        $this->checkPermission('vendas.concluir', 'Você não tem permissão para concluir vendas.');
        
        if (!$venda->podeSerConcluida()) {
            return to_route('vendas.index')
                ->with('error', 'Esta venda não pode ser concluída.');
        }

        $venda->update(['status' => 'concluida']);

        return to_route('vendas.show', $venda)
            ->with('success', 'Venda concluída com sucesso!');
    }

    /**
     * Cancelar venda.
     */
    public function cancelar(Venda $venda): RedirectResponse
    {
        $this->checkPermission('vendas.cancelar', 'Você não tem permissão para cancelar vendas.');
        
        if (!$venda->podeSerCancelada()) {
            return to_route('vendas.index')
                ->with('error', 'Esta venda não pode ser cancelada.');
        }

        $venda->update(['status' => 'cancelada']);

        return to_route('vendas.show', $venda)
            ->with('success', 'Venda cancelada com sucesso!');
    }

    /**
     * Gerar número único para venda.
     */
    private function gerarNumeroVenda(): string
    {
        $empresaId = auth()->user()->empresa_id;
        $ano = date('Y');
        $mes = date('m');
        
        $ultimaVenda = Venda::where('empresa_id', $empresaId)
            ->whereYear('created_at', $ano)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();

        $sequencial = $ultimaVenda ? (intval(substr($ultimaVenda->numero_venda, -6)) + 1) : 1;
        
        return sprintf('%04d%02d%06d', $empresaId, $mes, $sequencial);
    }
}