<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntradaMercadoriaStoreRequest;
use App\Http\Requests\EntradaMercadoriaUpdateRequest;
use App\Models\EntradaMercadoria;
use App\Models\Fornecedor;
use App\Models\Loja;
use App\Models\ProdutoVariacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EntradaMercadoriaController extends Controller
{
    /**
     * Display a listing of entradas de mercadoria.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('entradas-mercadoria.index', 'Você não tem permissão para visualizar entradas de mercadoria.');
        
        $query = EntradaMercadoria::with(['empresa', 'loja', 'fornecedor', 'usuario'])
            ->daEmpresa(auth()->user()->empresa_id);

        // Filtro por loja
        if ($request->filled('loja_id')) {
            $query->daLoja($request->loja_id);
        }

        // Filtro por fornecedor
        if ($request->filled('fornecedor_id')) {
            $query->porFornecedor($request->fornecedor_id);
        }

        // Filtro por período
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->porPeriodo($request->data_inicio, $request->data_fim);
        }

        // Busca por número da nota ou observações
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_nota', 'ilike', "%{$search}%")
                    ->orWhere('observacoes', 'ilike', "%{$search}%");
            });
        }

        $entradas = $query->latest('data_entrada')->paginate(15)->withQueryString();

        // Dados para filtros
        $empresaId = auth()->user()->empresa_id;
        $lojas = $empresaId ? Loja::forEmpresa($empresaId)->get() : collect();
        $fornecedores = $empresaId ? Fornecedor::daEmpresa($empresaId)->ativos()->get() : collect();

        return Inertia::render('entradas-mercadoria/Index', [
            'entradas' => $entradas,
            'lojas' => $lojas,
            'fornecedores' => $fornecedores,
            'filters' => $request->only(['search', 'loja_id', 'fornecedor_id', 'data_inicio', 'data_fim']),
        ]);
    }

    /**
     * Show the form for creating a new entrada de mercadoria.
     */
    public function create(): Response
    {
        $this->checkPermission('entradas-mercadoria.create', 'Você não tem permissão para criar entradas de mercadoria.');
        
        $empresaId = auth()->user()->empresa_id;
        $lojas = $empresaId ? Loja::forEmpresa($empresaId)->get() : collect();
        $fornecedores = $empresaId ? Fornecedor::daEmpresa($empresaId)->ativos()->get() : collect();
        $produtoVariacoes = $empresaId ? ProdutoVariacao::forEmpresa($empresaId)
            ->with(['produto', 'cor', 'tamanho'])
            ->get() : collect();

        return Inertia::render('entradas-mercadoria/Create', [
            'lojas' => $lojas,
            'fornecedores' => $fornecedores,
            'produtoVariacoes' => $produtoVariacoes,
        ]);
    }

    /**
     * Store a newly created entrada de mercadoria.
     */
    public function store(EntradaMercadoriaStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('entradas-mercadoria.store', 'Você não tem permissão para criar entradas de mercadoria.');
        
        $data = $request->validated();
        $empresaId = auth()->user()->empresa_id;
        
        if (!$empresaId) {
            return to_route('entradas-mercadoria.index')
                ->with('error', 'Usuário não possui empresa associada.');
        }
        
        $data['empresa_id'] = $empresaId;
        $data['usuario_id'] = auth()->id();

        $entrada = EntradaMercadoria::create($data);

        // Criar os itens da entrada
        foreach ($data['itens'] as $item) {
            $entrada->itens()->create($item);
        }

        return to_route('entradas-mercadoria.index')
            ->with('success', 'Entrada de mercadoria cadastrada com sucesso!');
    }

    /**
     * Display the specified entrada de mercadoria.
     */
    public function show(EntradaMercadoria $entradaMercadoria): Response
    {
        $this->checkPermission('entradas-mercadoria.show', 'Você não tem permissão para visualizar entradas de mercadoria.');
        
        $entradaMercadoria->load([
            'empresa',
            'loja',
            'fornecedor',
            'usuario',
            'itens.produtoVariacao.produto',
            'itens.produtoVariacao.cor',
            'itens.produtoVariacao.tamanho'
        ]);

        return Inertia::render('entradas-mercadoria/Show', [
            'entrada' => $entradaMercadoria,
        ]);
    }

    /**
     * Show the form for editing the specified entrada de mercadoria.
     */
    public function edit(EntradaMercadoria $entradaMercadoria): Response
    {
        $this->checkPermission('entradas-mercadoria.edit', 'Você não tem permissão para editar entradas de mercadoria.');
        
        $entradaMercadoria->load(['itens.produtoVariacao.produto', 'itens.produtoVariacao.cor', 'itens.produtoVariacao.tamanho']);

        $empresaId = auth()->user()->empresa_id;
        $lojas = $empresaId ? Loja::forEmpresa($empresaId)->get() : collect();
        $fornecedores = $empresaId ? Fornecedor::daEmpresa($empresaId)->ativos()->get() : collect();
        $produtoVariacoes = $empresaId ? ProdutoVariacao::forEmpresa($empresaId)
            ->with(['produto', 'cor', 'tamanho'])
            ->get() : collect();

        return Inertia::render('entradas-mercadoria/Edit', [
            'entrada' => $entradaMercadoria,
            'lojas' => $lojas,
            'fornecedores' => $fornecedores,
            'produtoVariacoes' => $produtoVariacoes,
        ]);
    }

    /**
     * Update the specified entrada de mercadoria.
     */
    public function update(EntradaMercadoriaUpdateRequest $request, EntradaMercadoria $entradaMercadoria): RedirectResponse
    {
        $this->checkPermission('entradas-mercadoria.update', 'Você não tem permissão para editar entradas de mercadoria.');
        
        $data = $request->validated();

        $entradaMercadoria->update($data);

        // Atualizar os itens da entrada
        if (isset($data['itens'])) {
            // Remover itens antigos
            $entradaMercadoria->itens()->delete();

            // Criar novos itens
            foreach ($data['itens'] as $item) {
                $entradaMercadoria->itens()->create($item);
            }
        }

        return to_route('entradas-mercadoria.index')
            ->with('success', 'Entrada de mercadoria atualizada com sucesso!');
    }

    /**
     * Remove the specified entrada de mercadoria.
     */
    public function destroy(EntradaMercadoria $entradaMercadoria): RedirectResponse
    {
        $this->checkPermission('entradas-mercadoria.delete', 'Você não tem permissão para excluir entradas de mercadoria.');
        
        // Remover os itens primeiro
        $entradaMercadoria->itens()->delete();
        
        // Remover a entrada
        $entradaMercadoria->delete();

        return to_route('entradas-mercadoria.index')
            ->with('success', 'Entrada de mercadoria excluída com sucesso!');
    }
}
