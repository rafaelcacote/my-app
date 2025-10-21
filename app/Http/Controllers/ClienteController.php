<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteStoreRequest;
use App\Http\Requests\ClienteUpdateRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClienteController extends Controller
{
    /**
     * Display a listing of clientes.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('clientes.index', 'Você não tem permissão para visualizar clientes.');
        
        $query = Cliente::query();

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Filtro por tipo (fisica/juridica)
        if ($request->has('tipo')) {
            if ($request->tipo === 'fisica') {
                $query->where('tipo', 'fisica');
            } elseif ($request->tipo === 'juridica') {
                $query->where('tipo', 'juridica');
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

        $clientes = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('clientes/Index', [
            'clientes' => $clientes,
            'filters' => $request->only(['search', 'status', 'tipo']),
        ]);
    }

    /**
     * Show the form for creating a new cliente.
     */
    public function create(): Response
    {
        $this->checkPermission('clientes.create', 'Você não tem permissão para criar clientes.');
        
        return Inertia::render('clientes/Create');
    }

    /**
     * Store a newly created cliente.
     */
    public function store(ClienteStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('clientes.store', 'Você não tem permissão para criar clientes.');
        
        $data = $request->validated();
        
        // Adiciona empresa_id do contexto atual
        $data['empresa_id'] = auth()->user()->empresa_id;

        Cliente::create($data);

        return to_route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Display the specified cliente.
     */
    public function show(Cliente $cliente): Response
    {
        $this->checkPermission('clientes.show', 'Você não tem permissão para visualizar clientes.');
        
        $cliente->load(['vendas' => function ($query) {
            $query->latest()->take(10);
        }]);

        return Inertia::render('clientes/Show', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Show the form for editing the specified cliente.
     */
    public function edit(Cliente $cliente): Response
    {
        $this->checkPermission('clientes.edit', 'Você não tem permissão para editar clientes.');
        
        return Inertia::render('clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Update the specified cliente.
     */
    public function update(ClienteUpdateRequest $request, Cliente $cliente): RedirectResponse
    {
        $this->checkPermission('clientes.update', 'Você não tem permissão para editar clientes.');
        
        $data = $request->validated();
        
        $cliente->update($data);

        return to_route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified cliente (soft delete).
     */
    public function destroy(Cliente $cliente): RedirectResponse
    {
        $this->checkPermission('clientes.delete', 'Você não tem permissão para excluir clientes.');
        
        // Verifica se o cliente tem vendas associadas
        if ($cliente->vendas()->count() > 0) {
            return to_route('clientes.index')
                ->with('error', 'Não é possível excluir cliente com vendas associadas.');
        }

        $cliente->delete();

        return to_route('clientes.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }
}