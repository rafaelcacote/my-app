<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarcaStoreRequest;
use App\Http\Requests\MarcaUpdateRequest;
use App\Models\Marca;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarcaController extends Controller
{
    /**
     * Display a listing of marcas.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('marcas.index', 'Você não tem permissão para visualizar marcas.');
        
        $query = Marca::query();

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Busca por nome ou descrição
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'ilike', "%{$search}%")
                    ->orWhere('descricao', 'ilike', "%{$search}%");
            });
        }

        $marcas = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('marcas/Index', [
            'marcas' => $marcas,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new marca.
     */
    public function create(): Response
    {
        $this->checkPermission('marcas.create', 'Você não tem permissão para criar marcas.');
        
        return Inertia::render('marcas/Create');
    }

    /**
     * Store a newly created marca.
     */
    public function store(MarcaStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('marcas.store', 'Você não tem permissão para criar marcas.');
        
        Marca::create($request->validated());

        return to_route('marcas.index')
            ->with('success', 'Marca cadastrada com sucesso!');
    }

    /**
     * Display the specified marca.
     */
    public function show(Marca $marca): Response
    {
        $this->checkPermission('marcas.show', 'Você não tem permissão para visualizar marcas.');
        
        return Inertia::render('marcas/Show', [
            'marca' => $marca,
        ]);
    }

    /**
     * Show the form for editing the specified marca.
     */
    public function edit(Marca $marca): Response
    {
        $this->checkPermission('marcas.edit', 'Você não tem permissão para editar marcas.');
        
        return Inertia::render('marcas/Edit', [
            'marca' => $marca,
        ]);
    }

    /**
     * Update the specified marca.
     */
    public function update(MarcaUpdateRequest $request, Marca $marca): RedirectResponse
    {
        $this->checkPermission('marcas.update', 'Você não tem permissão para editar marcas.');
        
        $marca->update($request->validated());

        return to_route('marcas.index')
            ->with('success', 'Marca atualizada com sucesso!');
    }

    /**
     * Remove the specified marca (soft delete).
     */
    public function destroy(Marca $marca): RedirectResponse
    {
        $this->checkPermission('marcas.delete', 'Você não tem permissão para excluir marcas.');
        
        $marca->delete();

        return to_route('marcas.index')
            ->with('success', 'Marca excluída com sucesso!');
    }
}
