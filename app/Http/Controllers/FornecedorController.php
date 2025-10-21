<?php

namespace App\Http\Controllers;

use App\Http\Requests\FornecedorStoreRequest;
use App\Http\Requests\FornecedorUpdateRequest;
use App\Models\Fornecedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FornecedorController extends Controller
{
    /**
     * Display a listing of fornecedores.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('fornecedores.index', 'Você não tem permissão para visualizar fornecedores.');
        
        $empresaId = auth()->user()->empresa_id;
        
        if (!$empresaId) {
            return Inertia::render('fornecedores/Index', [
                'fornecedores' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 15,
                    'total' => 0,
                    'links' => [],
                ],
                'filters' => $request->only(['search', 'status']),
            ]);
        }
        
        $query = Fornecedor::with(['empresa'])
            ->daEmpresa($empresaId);

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->ativos();
            } elseif ($request->status === 'inativo') {
                $query->inativos();
            }
        }

        // Busca por nome, CPF/CNPJ ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'ilike', "%{$search}%")
                    ->orWhere('cpf_cnpj', 'like', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $fornecedores = $query->with(['endereco'])->latest()->paginate(15)->withQueryString();

        return Inertia::render('fornecedores/Index', [
            'fornecedores' => $fornecedores,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new fornecedor.
     */
    public function create(): Response
    {
        $this->checkPermission('fornecedores.create', 'Você não tem permissão para criar fornecedores.');
        
        $empresaId = auth()->user()->empresa_id;
        $enderecos = $empresaId ? \App\Models\Endereco::where('empresa_id', $empresaId)->get() : collect();

        return Inertia::render('fornecedores/Create', [
            'enderecos' => $enderecos,
        ]);
    }

    /**
     * Store a newly created fornecedor.
     */
    public function store(FornecedorStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('fornecedores.store', 'Você não tem permissão para criar fornecedores.');
        
        $data = $request->validated();
        $empresaId = auth()->user()->empresa_id;
        
        if (!$empresaId) {
            return to_route('fornecedores.index')
                ->with('error', 'Usuário não possui empresa associada.');
        }
        
        DB::beginTransaction();
        try {
            // Processa o endereço se existir
            $enderecoId = null;
            if (!empty($data['endereco']) && !empty($data['endereco']['endereco'])) {
                $endereco = \App\Models\Endereco::create($data['endereco']);
                $enderecoId = $endereco->id;
            }
            
            // Remove dados de endereço do array principal
            unset($data['endereco']);
            
            // Adiciona o endereco_id
            $data['endereco_id'] = $enderecoId;
            $data['empresa_id'] = $empresaId;

            Fornecedor::create($data);
            
            DB::commit();
            
            return to_route('fornecedores.index')
                ->with('success', 'Fornecedor cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return to_route('fornecedores.index')
                ->with('error', 'Erro ao cadastrar fornecedor. Tente novamente.');
        }
    }

    /**
     * Display the specified fornecedor.
     */
    public function show(Fornecedor $fornecedor): Response
    {
        $this->checkPermission('fornecedores.show', 'Você não tem permissão para visualizar fornecedores.');
        
        $fornecedor->load(['empresa', 'endereco', 'entradasMercadoria.loja']);

        return Inertia::render('fornecedores/Show', [
            'fornecedor' => $fornecedor,
        ]);
    }

    /**
     * Show the form for editing the specified fornecedor.
     */
    public function edit(Fornecedor $fornecedor): Response
    {
        $this->checkPermission('fornecedores.edit', 'Você não tem permissão para editar fornecedores.');
        
        $fornecedor->load(['endereco']);

        return Inertia::render('fornecedores/Edit', [
            'fornecedor' => $fornecedor,
        ]);
    }

    /**
     * Update the specified fornecedor.
     */
    public function update(FornecedorUpdateRequest $request, Fornecedor $fornecedor): RedirectResponse
    {
        $this->checkPermission('fornecedores.update', 'Você não tem permissão para editar fornecedores.');
        
        $data = $request->validated();

        DB::beginTransaction();
        try {
            // Processa o endereço se existir
            $enderecoId = $fornecedor->endereco_id;
            if (!empty($data['endereco']) && !empty($data['endereco']['endereco'])) {
                if ($fornecedor->endereco) {
                    // Atualiza endereço existente
                    $fornecedor->endereco->update($data['endereco']);
                } else {
                    // Cria novo endereço
                    $endereco = \App\Models\Endereco::create($data['endereco']);
                    $enderecoId = $endereco->id;
                }
            }
            
            // Remove dados de endereço do array principal
            unset($data['endereco']);
            
            // Atualiza o endereco_id se necessário
            if (isset($enderecoId)) {
                $data['endereco_id'] = $enderecoId;
            }

            $fornecedor->update($data);
            
            DB::commit();
            
            return to_route('fornecedores.index')
                ->with('success', 'Fornecedor atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return to_route('fornecedores.index')
                ->with('error', 'Erro ao atualizar fornecedor. Tente novamente.');
        }
    }

    /**
     * Remove the specified fornecedor.
     */
    public function destroy(Fornecedor $fornecedor): RedirectResponse
    {
        $this->checkPermission('fornecedores.delete', 'Você não tem permissão para excluir fornecedores.');
        
        // Verificar se o fornecedor tem entradas de mercadoria
        if ($fornecedor->entradasMercadoria()->exists()) {
            return to_route('fornecedores.index')
                ->with('error', 'Não é possível excluir um fornecedor que possui entradas de mercadoria.');
        }

        $fornecedor->delete();

        return to_route('fornecedores.index')
            ->with('success', 'Fornecedor excluído com sucesso!');
    }
}
