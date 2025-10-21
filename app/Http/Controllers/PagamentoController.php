<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagamentoStoreRequest;
use App\Http\Requests\PagamentoUpdateRequest;
use App\Models\Pagamento;
use App\Models\Venda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PagamentoController extends Controller
{
    /**
     * Display a listing of pagamentos.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('pagamentos.index', 'Você não tem permissão para visualizar pagamentos.');
        
        $query = Pagamento::with(['venda.cliente', 'venda.loja']);

        // Filtro por status
        if ($request->has('status')) {
            if ($request->status !== 'all') {
                $query->where('status', $request->status);
            }
        }

        // Filtro por forma de pagamento
        if ($request->has('forma_pagamento') && $request->forma_pagamento) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        // Filtro por período
        if ($request->filled('data_inicio')) {
            $query->where('data_pagamento', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->where('data_pagamento', '<=', $request->data_fim);
        }

        // Busca por número da venda ou nome do cliente
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('venda', function ($vendaQuery) use ($search) {
                    $vendaQuery->where('numero_venda', 'ilike', "%{$search}%")
                        ->orWhereHas('cliente', function ($clienteQuery) use ($search) {
                            $clienteQuery->where('nome', 'ilike', "%{$search}%");
                        });
                });
            });
        }

        $pagamentos = $query->latest('data_pagamento')->paginate(15)->withQueryString();

        return Inertia::render('pagamentos/Index', [
            'pagamentos' => $pagamentos,
            'filters' => $request->only(['search', 'status', 'forma_pagamento', 'data_inicio', 'data_fim']),
        ]);
    }

    /**
     * Show the form for creating a new pagamento.
     */
    public function create(Request $request): Response
    {
        $this->checkPermission('pagamentos.create', 'Você não tem permissão para criar pagamentos.');
        
        $vendaId = $request->get('venda_id');
        $venda = null;
        
        if ($vendaId) {
            $venda = Venda::with(['cliente', 'loja'])->find($vendaId);
        }

        return Inertia::render('pagamentos/Create', [
            'venda' => $venda,
        ]);
    }

    /**
     * Store a newly created pagamento.
     */
    public function store(PagamentoStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('pagamentos.store', 'Você não tem permissão para criar pagamentos.');
        
        $data = $request->validated();
        
        $pagamento = Pagamento::create($data);

        return to_route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento cadastrado com sucesso!');
    }

    /**
     * Display the specified pagamento.
     */
    public function show(Pagamento $pagamento): Response
    {
        $this->checkPermission('pagamentos.show', 'Você não tem permissão para visualizar pagamentos.');
        
        $pagamento->load(['venda.cliente', 'venda.loja', 'venda.itens.produtoVariacao.produto']);

        return Inertia::render('pagamentos/Show', [
            'pagamento' => $pagamento,
        ]);
    }

    /**
     * Show the form for editing the specified pagamento.
     */
    public function edit(Pagamento $pagamento): Response
    {
        $this->checkPermission('pagamentos.edit', 'Você não tem permissão para editar pagamentos.');
        
        if (!$pagamento->podeSerCancelado()) {
            return to_route('pagamentos.index')
                ->with('error', 'Este pagamento não pode ser editado.');
        }

        $pagamento->load(['venda.cliente', 'venda.loja']);

        return Inertia::render('pagamentos/Edit', [
            'pagamento' => $pagamento,
        ]);
    }

    /**
     * Update the specified pagamento.
     */
    public function update(PagamentoUpdateRequest $request, Pagamento $pagamento): RedirectResponse
    {
        $this->checkPermission('pagamentos.update', 'Você não tem permissão para editar pagamentos.');
        
        if (!$pagamento->podeSerCancelado()) {
            return to_route('pagamentos.index')
                ->with('error', 'Este pagamento não pode ser editado.');
        }

        $data = $request->validated();
        
        $pagamento->update($data);

        return to_route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento atualizado com sucesso!');
    }

    /**
     * Remove the specified pagamento (soft delete).
     */
    public function destroy(Pagamento $pagamento): RedirectResponse
    {
        $this->checkPermission('pagamentos.delete', 'Você não tem permissão para excluir pagamentos.');
        
        if (!$pagamento->podeSerCancelado()) {
            return to_route('pagamentos.index')
                ->with('error', 'Este pagamento não pode ser excluído.');
        }

        $pagamento->delete();

        return to_route('pagamentos.index')
            ->with('success', 'Pagamento excluído com sucesso!');
    }

    /**
     * Marcar pagamento como pago.
     */
    public function marcarComoPago(Pagamento $pagamento): RedirectResponse
    {
        $this->checkPermission('pagamentos.marcar-como-pago', 'Você não tem permissão para marcar pagamentos como pagos.');
        
        if (!$pagamento->podeSerPago()) {
            return to_route('pagamentos.index')
                ->with('error', 'Este pagamento não pode ser marcado como pago.');
        }

        $pagamento->marcarComoPago();

        return to_route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento marcado como pago com sucesso!');
    }

    /**
     * Cancelar pagamento.
     */
    public function cancelar(Pagamento $pagamento): RedirectResponse
    {
        $this->checkPermission('pagamentos.cancelar', 'Você não tem permissão para cancelar pagamentos.');
        
        if (!$pagamento->podeSerCancelado()) {
            return to_route('pagamentos.index')
                ->with('error', 'Este pagamento não pode ser cancelado.');
        }

        $pagamento->cancelar();

        return to_route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento cancelado com sucesso!');
    }
}