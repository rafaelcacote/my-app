<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaStoreRequest;
use App\Http\Requests\CategoriaUpdateRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoriaController extends Controller
{
    /**
     * Display a listing of categorias.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('categorias.index', 'Você não tem permissão para visualizar categorias.');
        
        $query = Categoria::query();

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

        $categorias = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('categorias/Index', [
            'categorias' => $categorias,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new categoria.
     */
    public function create(): Response
    {
        $this->checkPermission('categorias.create', 'Você não tem permissão para criar categorias.');
        
        return Inertia::render('categorias/Create');
    }

    /**
     * Store a newly created categoria.
     */
    public function store(CategoriaStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('categorias.store', 'Você não tem permissão para criar categorias.');
        
        Categoria::create($request->validated());

        return to_route('categorias.index')
            ->with('success', 'Categoria cadastrada com sucesso!');
    }

    /**
     * Display the specified categoria.
     */
    public function show(Categoria $categoria): Response
    {
        $this->checkPermission('categorias.show', 'Você não tem permissão para visualizar categorias.');
        
        return Inertia::render('categorias/Show', [
            'categoria' => $categoria,
        ]);
    }

    /**
     * Show the form for editing the specified categoria.
     */
    public function edit(Categoria $categoria): Response
    {
        $this->checkPermission('categorias.edit', 'Você não tem permissão para editar categorias.');
        
        return Inertia::render('categorias/Edit', [
            'categoria' => $categoria,
        ]);
    }

    /**
     * Update the specified categoria.
     */
    public function update(CategoriaUpdateRequest $request, Categoria $categoria): RedirectResponse
    {
        $this->checkPermission('categorias.update', 'Você não tem permissão para editar categorias.');
        
        $categoria->update($request->validated());

        return to_route('categorias.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified categoria (soft delete).
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        $this->checkPermission('categorias.delete', 'Você não tem permissão para excluir categorias.');
        
        $categoria->delete();

        return to_route('categorias.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
