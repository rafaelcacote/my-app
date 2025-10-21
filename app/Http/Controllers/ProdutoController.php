<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoStoreRequest;
use App\Http\Requests\ProdutoUpdateRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Produto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProdutoController extends Controller
{
    /**
     * Display a listing of produtos.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('produtos.index', 'Você não tem permissão para visualizar produtos.');
        
        $query = Produto::with(['categoria', 'marca']);

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Filtro por controle de estoque
        if ($request->has('controle_estoque')) {
            $query->where('controle_estoque', $request->controle_estoque);
        }

        // Filtro por categoria
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Filtro por marca
        if ($request->filled('marca_id')) {
            $query->where('marca_id', $request->marca_id);
        }

        // Busca por nome, SKU ou descrição
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'ilike', "%{$search}%")
                    ->orWhere('sku', 'ilike', "%{$search}%")
                    ->orWhere('descricao', 'ilike', "%{$search}%");
            });
        }

        $produtos = $query->latest()->paginate(15)->withQueryString();

        // Buscar categorias e marcas para filtros
        $categorias = Categoria::ativas()->orderBy('nome')->get();
        $marcas = Marca::ativas()->orderBy('nome')->get();

        return Inertia::render('produtos/Index', [
            'produtos' => $produtos,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'filters' => $request->only(['search', 'status', 'controle_estoque', 'categoria_id', 'marca_id']),
        ]);
    }

    /**
     * Show the form for creating a new produto.
     */
    public function create(): Response
    {
        $this->checkPermission('produtos.create', 'Você não tem permissão para criar produtos.');
        
        $categorias = Categoria::ativas()->orderBy('nome')->get();
        $marcas = Marca::ativas()->orderBy('nome')->get();

        return Inertia::render('produtos/Create', [
            'categorias' => $categorias,
            'marcas' => $marcas,
        ]);
    }

    /**
     * Store a newly created produto.
     */
    public function store(ProdutoStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('produtos.store', 'Você não tem permissão para criar produtos.');
        
        $data = $request->validated();
        
        // Calcular margem de lucro se não informada
        if (!isset($data['margem_lucro']) && isset($data['preco_custo']) && isset($data['preco_venda'])) {
            if ($data['preco_custo'] > 0) {
                $data['margem_lucro'] = (($data['preco_venda'] - $data['preco_custo']) / $data['preco_custo']) * 100;
            }
        }

        Produto::create($data);

        return to_route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    /**
     * Display the specified produto.
     */
    public function show(Produto $produto): Response
    {
        $this->checkPermission('produtos.show', 'Você não tem permissão para visualizar produtos.');
        
        $produto->load(['categoria', 'marca', 'produtoVariacoes.cor', 'produtoVariacoes.tamanho']);

        return Inertia::render('produtos/Show', [
            'produto' => $produto,
        ]);
    }

    /**
     * Show the form for editing the specified produto.
     */
    public function edit(Produto $produto): Response
    {
        $this->checkPermission('produtos.edit', 'Você não tem permissão para editar produtos.');
        
        $categorias = Categoria::ativas()->orderBy('nome')->get();
        $marcas = Marca::ativas()->orderBy('nome')->get();

        return Inertia::render('produtos/Edit', [
            'produto' => $produto,
            'categorias' => $categorias,
            'marcas' => $marcas,
        ]);
    }

    /**
     * Update the specified produto.
     */
    public function update(ProdutoUpdateRequest $request, Produto $produto): RedirectResponse
    {
        $this->checkPermission('produtos.update', 'Você não tem permissão para editar produtos.');
        
        $data = $request->validated();
        
        // Calcular margem de lucro se não informada
        if (!isset($data['margem_lucro']) && isset($data['preco_custo']) && isset($data['preco_venda'])) {
            if ($data['preco_custo'] > 0) {
                $data['margem_lucro'] = (($data['preco_venda'] - $data['preco_custo']) / $data['preco_custo']) * 100;
            }
        }

        $produto->update($data);

        return to_route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified produto (soft delete).
     */
    public function destroy(Produto $produto): RedirectResponse
    {
        $this->checkPermission('produtos.delete', 'Você não tem permissão para excluir produtos.');
        
        $produto->delete();

        return to_route('produtos.index')
            ->with('success', 'Produto excluído com sucesso!');
    }
}
