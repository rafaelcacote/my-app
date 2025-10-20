<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoVariacaoStoreRequest;
use App\Http\Requests\ProdutoVariacaoUpdateRequest;
use App\Models\Cor;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use App\Models\Tamanho;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProdutoVariacaoController extends Controller
{
    /**
     * Display a listing of produto variacoes.
     */
    public function index(Request $request): Response
    {
        $query = ProdutoVariacao::with(['produto', 'tamanho', 'cor']);

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Filtro por produto
        if ($request->filled('produto_id')) {
            $query->where('produto_id', $request->produto_id);
        }

        // Filtro por tamanho
        if ($request->filled('tamanho_id')) {
            $query->where('tamanho_id', $request->tamanho_id);
        }

        // Filtro por cor
        if ($request->filled('cor_id')) {
            $query->where('cor_id', $request->cor_id);
        }

        // Busca por SKU variação
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('sku_variacao', 'ilike', "%{$search}%");
        }

        $produtoVariacoes = $query->latest()->paginate(15)->withQueryString();

        // Buscar dados para filtros
        $produtos = Produto::ativas()->orderBy('nome')->get();
        $tamanhos = Tamanho::ordered()->get();
        $cores = Cor::orderBy('nome')->get();

        return Inertia::render('produto-variacoes/Index', [
            'produtoVariacoes' => $produtoVariacoes,
            'produtos' => $produtos,
            'tamanhos' => $tamanhos,
            'cores' => $cores,
            'filters' => $request->only(['search', 'status', 'produto_id', 'tamanho_id', 'cor_id']),
        ]);
    }

    /**
     * Show the form for creating a new produto variacao.
     */
    public function create(): Response
    {
        $produtos = Produto::ativas()->orderBy('nome')->get();
        $tamanhos = Tamanho::ordered()->get();
        $cores = Cor::orderBy('nome')->get();

        return Inertia::render('produto-variacoes/Create', [
            'produtos' => $produtos,
            'tamanhos' => $tamanhos,
            'cores' => $cores,
        ]);
    }

    /**
     * Store a newly created produto variacao.
     */
    public function store(ProdutoVariacaoStoreRequest $request): RedirectResponse
    {
        ProdutoVariacao::create($request->validated());

        return to_route('produto-variacoes.index')
            ->with('success', 'Variação de produto cadastrada com sucesso!');
    }

    /**
     * Display the specified produto variacao.
     */
    public function show(ProdutoVariacao $produtoVariacao): Response
    {
        $produtoVariacao->load(['produto', 'tamanho', 'cor']);

        return Inertia::render('produto-variacoes/Show', [
            'produtoVariacao' => $produtoVariacao,
        ]);
    }

    /**
     * Show the form for editing the specified produto variacao.
     */
    public function edit(ProdutoVariacao $produtoVariacao): Response
    {
        $produtos = Produto::ativas()->orderBy('nome')->get();
        $tamanhos = Tamanho::ordered()->get();
        $cores = Cor::orderBy('nome')->get();

        return Inertia::render('produto-variacoes/Edit', [
            'produtoVariacao' => $produtoVariacao,
            'produtos' => $produtos,
            'tamanhos' => $tamanhos,
            'cores' => $cores,
        ]);
    }

    /**
     * Update the specified produto variacao.
     */
    public function update(ProdutoVariacaoUpdateRequest $request, ProdutoVariacao $produtoVariacao): RedirectResponse
    {
        $produtoVariacao->update($request->validated());

        return to_route('produto-variacoes.index')
            ->with('success', 'Variação de produto atualizada com sucesso!');
    }

    /**
     * Remove the specified produto variacao (soft delete).
     */
    public function destroy(ProdutoVariacao $produtoVariacao): RedirectResponse
    {
        $produtoVariacao->delete();

        return to_route('produto-variacoes.index')
            ->with('success', 'Variação de produto excluída com sucesso!');
    }
}
