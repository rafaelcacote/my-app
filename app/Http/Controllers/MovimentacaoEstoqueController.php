<?php

namespace App\Http\Controllers;

use App\Helpers\EmpresaHelper;
use App\Models\MovimentacaoEstoque;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MovimentacaoEstoqueController extends Controller
{
    /**
     * Lista todas as movimentações da empresa atual
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('movimentacoes-estoque.index', 'Você não tem permissão para visualizar movimentações de estoque.');
        
        $query = MovimentacaoEstoque::with(['loja', 'produtoVariacao.produto', 'usuario']);
        
        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('motivo', 'ilike', "%{$search}%")
                  ->orWhere('observacao', 'ilike', "%{$search}%");
            });
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        if ($request->filled('loja_id')) {
            $query->where('loja_id', $request->loja_id);
        }
        
        if ($request->filled('produto_variacao_id')) {
            $query->where('produto_variacao_id', $request->produto_variacao_id);
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        
        $movimentacoes = $query->latest()->paginate(15)->withQueryString();
        
        // Busca lojas da empresa atual para filtros
        $lojas = EmpresaHelper::queryForCurrentEmpresa(\App\Models\Loja::class)
            ->where('ativo', true)
            ->get();
        
        return Inertia::render('movimentacoes-estoque/Index', [
            'movimentacoesEstoque' => $movimentacoes,
            'lojas' => $lojas,
            'filters' => $request->only(['search', 'tipo', 'loja_id', 'produto_variacao_id', 'data_inicio', 'data_fim']),
        ]);
    }
    
    /**
     * Mostra o formulário de criação
     */
    public function create(): Response
    {
        $this->checkPermission('movimentacoes-estoque.create', 'Você não tem permissão para criar movimentações de estoque.');
        
        // Busca lojas da empresa atual
        $lojas = EmpresaHelper::queryForCurrentEmpresa(\App\Models\Loja::class)
            ->where('ativo', true)
            ->get();
        
        // Busca variações de produtos da empresa atual
        $produtoVariacoes = EmpresaHelper::queryForCurrentEmpresa(\App\Models\ProdutoVariacao::class)
            ->with(['produto', 'tamanho', 'cor'])
            ->where('ativo', true)
            ->get();
        
        return Inertia::render('movimentacoes-estoque/Create', [
            'lojas' => $lojas,
            'produtoVariacoes' => $produtoVariacoes,
        ]);
    }
    
    /**
     * Cria uma nova movimentação
     */
    public function store(Request $request)
    {
        $this->checkPermission('movimentacoes-estoque.store', 'Você não tem permissão para criar movimentações de estoque.');
        
        $request->validate([
            'loja_id' => 'required|exists:multitenancy.lojas,id',
            'produto_variacao_id' => 'required|exists:produtosestoques.produto_variacoes,id',
            'tipo' => 'required|in:entrada,saida,ajuste',
            'quantidade' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'observacao' => 'nullable|string|max:500',
        ]);
        
        // Verifica se a loja pertence à empresa atual
        $loja = EmpresaHelper::findForCurrentEmpresa(\App\Models\Loja::class, $request->loja_id);
        if (!$loja) {
            return back()->withErrors(['loja_id' => 'Loja não encontrada ou não pertence à sua empresa']);
        }
        
        // Verifica se a variação do produto pertence à empresa atual
        $produtoVariacao = EmpresaHelper::findForCurrentEmpresa(\App\Models\ProdutoVariacao::class, $request->produto_variacao_id);
        if (!$produtoVariacao) {
            return back()->withErrors(['produto_variacao_id' => 'Variação do produto não encontrada ou não pertence à sua empresa']);
        }
        
        // Calcula quantidades
        $quantidadeAnterior = $produtoVariacao->quantidade_atual ?? 0;
        $quantidadeAtual = $request->tipo === 'entrada' 
            ? $quantidadeAnterior + $request->quantidade
            : $quantidadeAnterior - $request->quantidade;
        
        MovimentacaoEstoque::create([
            'loja_id' => $request->loja_id,
            'produto_variacao_id' => $request->produto_variacao_id,
            'tipo' => $request->tipo,
            'quantidade' => $request->quantidade,
            'quantidade_anterior' => $quantidadeAnterior,
            'quantidade_atual' => $quantidadeAtual,
            'motivo' => $request->motivo,
            'observacao' => $request->observacao,
            'usuario_id' => auth()->id(),
        ]);
        
        // Atualiza a quantidade atual na variação do produto
        $produtoVariacao->update(['quantidade_atual' => $quantidadeAtual]);
        
        return redirect()->route('movimentacoes-estoque.index')
            ->with('success', 'Movimentação criada com sucesso!');
    }
    
    /**
     * Mostra uma movimentação específica
     */
    public function show(int $id): Response
    {
        $this->checkPermission('movimentacoes-estoque.show', 'Você não tem permissão para visualizar movimentações de estoque.');
        
        $movimentacao = EmpresaHelper::findForCurrentEmpresa(MovimentacaoEstoque::class, $id);
        
        if (!$movimentacao) {
            abort(404, 'Movimentação não encontrada');
        }
        
        $movimentacao->load(['loja', 'produtoVariacao.produto', 'usuario']);
        
        return Inertia::render('movimentacoes-estoque/Show', [
            'movimentacao' => $movimentacao,
        ]);
    }
    
    /**
     * Mostra o formulário de edição
     */
    public function edit(int $id): Response
    {
        $this->checkPermission('movimentacoes-estoque.edit', 'Você não tem permissão para editar movimentações de estoque.');
        
        $movimentacao = EmpresaHelper::findForCurrentEmpresa(MovimentacaoEstoque::class, $id);
        
        if (!$movimentacao) {
            abort(404, 'Movimentação não encontrada');
        }
        
        $movimentacao->load(['loja', 'produtoVariacao.produto', 'usuario']);
        
        return Inertia::render('movimentacoes-estoque/Edit', [
            'movimentacao' => $movimentacao,
        ]);
    }
    
    /**
     * Atualiza uma movimentação
     */
    public function update(Request $request, int $id)
    {
        $this->checkPermission('movimentacoes-estoque.update', 'Você não tem permissão para editar movimentações de estoque.');
        
        $movimentacao = EmpresaHelper::findForCurrentEmpresa(MovimentacaoEstoque::class, $id);
        
        if (!$movimentacao) {
            abort(404, 'Movimentação não encontrada');
        }
        
        $request->validate([
            'motivo' => 'required|string|max:255',
            'observacao' => 'nullable|string|max:500',
        ]);
        
        try {
            EmpresaHelper::updateForCurrentEmpresa($movimentacao, [
                'motivo' => $request->motivo,
                'observacao' => $request->observacao,
            ]);
            
            return redirect()->route('movimentacoes-estoque.index')
                ->with('success', 'Movimentação atualizada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Remove uma movimentação
     */
    public function destroy(int $id)
    {
        $this->checkPermission('movimentacoes-estoque.delete', 'Você não tem permissão para excluir movimentações de estoque.');
        
        $movimentacao = EmpresaHelper::findForCurrentEmpresa(MovimentacaoEstoque::class, $id);
        
        if (!$movimentacao) {
            abort(404, 'Movimentação não encontrada');
        }
        
        try {
            EmpresaHelper::deleteForCurrentEmpresa($movimentacao);
            
            return redirect()->route('movimentacoes-estoque.index')
                ->with('success', 'Movimentação removida com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}