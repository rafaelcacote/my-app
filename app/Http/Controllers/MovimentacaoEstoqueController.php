<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovimentacaoEstoqueStoreRequest;
use App\Http\Requests\MovimentacaoEstoqueUpdateRequest;
use App\Models\Loja;
use App\Models\MovimentacaoEstoque;
use App\Models\ProdutoVariacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MovimentacaoEstoqueController extends Controller
{
    /**
     * Display a listing of movimentacoes estoque.
     */
    public function index(Request $request): Response
    {
        $query = MovimentacaoEstoque::with(['loja', 'produtoVariacao.produto', 'produtoVariacao.tamanho', 'produtoVariacao.cor', 'usuario']);

        // Filtro por tipo de movimentação
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por loja
        if ($request->filled('loja_id')) {
            $query->where('loja_id', $request->loja_id);
        }

        // Filtro por produto variação
        if ($request->filled('produto_variacao_id')) {
            $query->where('produto_variacao_id', $request->produto_variacao_id);
        }

        // Filtro por usuário
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Filtro por data
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        // Busca por motivo ou observação
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('motivo', 'ilike', "%{$search}%")
                    ->orWhere('observacao', 'ilike', "%{$search}%");
            });
        }

        $movimentacoesEstoque = $query->latest()->paginate(15)->withQueryString();

        // Buscar dados para filtros
        $lojas = Loja::orderBy('nome')->get();
        $produtoVariacoes = ProdutoVariacao::with(['produto', 'tamanho', 'cor'])->orderBy('produto_id')->get();

        return Inertia::render('movimentacoes-estoque/Index', [
            'movimentacoesEstoque' => $movimentacoesEstoque,
            'lojas' => $lojas,
            'produtoVariacoes' => $produtoVariacoes,
            'filters' => $request->only(['search', 'tipo', 'loja_id', 'produto_variacao_id', 'usuario_id', 'data_inicio', 'data_fim']),
        ]);
    }

    /**
     * Show the form for creating a new movimentacao estoque.
     */
    public function create(): Response
    {
        $lojas = Loja::orderBy('nome')->get();
        $produtoVariacoes = ProdutoVariacao::with(['produto', 'tamanho', 'cor'])->orderBy('produto_id')->get();

        return Inertia::render('movimentacoes-estoque/Create', [
            'lojas' => $lojas,
            'produtoVariacoes' => $produtoVariacoes,
        ]);
    }

    /**
     * Store a newly created movimentacao estoque.
     */
    public function store(MovimentacaoEstoqueStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['usuario_id'] = auth()->id();

        MovimentacaoEstoque::create($data);

        return to_route('movimentacoes-estoque.index')
            ->with('success', 'Movimentação de estoque registrada com sucesso!');
    }

    /**
     * Display the specified movimentacao estoque.
     */
    public function show(MovimentacaoEstoque $movimentacaoEstoque): Response
    {
        $movimentacaoEstoque->load(['loja', 'produtoVariacao.produto', 'produtoVariacao.tamanho', 'produtoVariacao.cor', 'usuario']);

        return Inertia::render('movimentacoes-estoque/Show', [
            'movimentacaoEstoque' => $movimentacaoEstoque,
        ]);
    }

    /**
     * Show the form for editing the specified movimentacao estoque.
     */
    public function edit(MovimentacaoEstoque $movimentacaoEstoque): Response
    {
        $lojas = Loja::orderBy('nome')->get();
        $produtoVariacoes = ProdutoVariacao::with(['produto', 'tamanho', 'cor'])->orderBy('produto_id')->get();

        return Inertia::render('movimentacoes-estoque/Edit', [
            'movimentacaoEstoque' => $movimentacaoEstoque,
            'lojas' => $lojas,
            'produtoVariacoes' => $produtoVariacoes,
        ]);
    }

    /**
     * Update the specified movimentacao estoque.
     */
    public function update(MovimentacaoEstoqueUpdateRequest $request, MovimentacaoEstoque $movimentacaoEstoque): RedirectResponse
    {
        $movimentacaoEstoque->update($request->validated());

        return to_route('movimentacoes-estoque.index')
            ->with('success', 'Movimentação de estoque atualizada com sucesso!');
    }

    /**
     * Remove the specified movimentacao estoque.
     */
    public function destroy(MovimentacaoEstoque $movimentacaoEstoque): RedirectResponse
    {
        $movimentacaoEstoque->delete();

        return to_route('movimentacoes-estoque.index')
            ->with('success', 'Movimentação de estoque excluída com sucesso!');
    }
}
