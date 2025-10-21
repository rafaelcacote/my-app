<?php

namespace App\Http\Controllers;

use App\Http\Requests\TamanhoStoreRequest;
use App\Http\Requests\TamanhoUpdateRequest;
use App\Models\Tamanho;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TamanhoController extends Controller
{
    /**
     * Display a listing of tamanhos.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('tamanhos.index', 'Você não tem permissão para visualizar tamanhos.');
        
        $query = Tamanho::query();

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Busca por nome
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nome', 'ilike', "%{$search}%");
        }

        $tamanhos = $query->ordered()->paginate(15)->withQueryString();

        return Inertia::render('tamanhos/Index', [
            'tamanhos' => $tamanhos,
            'filters' => $request->only(['search', 'tipo']),
        ]);
    }

    /**
     * Show the form for creating a new tamanho.
     */
    public function create(): Response
    {
        $this->checkPermission('tamanhos.create', 'Você não tem permissão para criar tamanhos.');
        
        return Inertia::render('tamanhos/Create');
    }

    /**
     * Store a newly created tamanho.
     */
    public function store(TamanhoStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('tamanhos.store', 'Você não tem permissão para criar tamanhos.');
        
        Tamanho::create($request->validated());

        return to_route('tamanhos.index')
            ->with('success', 'Tamanho cadastrado com sucesso!');
    }

    /**
     * Display the specified tamanho.
     */
    public function show(Tamanho $tamanho): Response
    {
        $this->checkPermission('tamanhos.show', 'Você não tem permissão para visualizar tamanhos.');
        
        return Inertia::render('tamanhos/Show', [
            'tamanho' => $tamanho,
        ]);
    }

    /**
     * Show the form for editing the specified tamanho.
     */
    public function edit(Tamanho $tamanho): Response
    {
        $this->checkPermission('tamanhos.edit', 'Você não tem permissão para editar tamanhos.');
        
        return Inertia::render('tamanhos/Edit', [
            'tamanho' => $tamanho,
        ]);
    }

    /**
     * Update the specified tamanho.
     */
    public function update(TamanhoUpdateRequest $request, Tamanho $tamanho): RedirectResponse
    {
        $this->checkPermission('tamanhos.update', 'Você não tem permissão para editar tamanhos.');
        
        $tamanho->update($request->validated());

        return to_route('tamanhos.index')
            ->with('success', 'Tamanho atualizado com sucesso!');
    }

    /**
     * Remove the specified tamanho (soft delete).
     */
    public function destroy(Tamanho $tamanho): RedirectResponse
    {
        $this->checkPermission('tamanhos.delete', 'Você não tem permissão para excluir tamanhos.');
        
        $tamanho->delete();

        return to_route('tamanhos.index')
            ->with('success', 'Tamanho excluído com sucesso!');
    }
}
