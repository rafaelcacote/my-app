<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorStoreRequest;
use App\Http\Requests\CorUpdateRequest;
use App\Models\Cor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CorController extends Controller
{
    /**
     * Display a listing of cores.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('cores.index', 'Você não tem permissão para visualizar cores.');
        
        $query = Cor::query();

        // Busca por nome ou código hex
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'ilike', "%{$search}%")
                    ->orWhere('codigo_hex', 'ilike', "%{$search}%");
            });
        }

        $cores = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('cores/Index', [
            'cores' => $cores,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new cor.
     */
    public function create(): Response
    {
        $this->checkPermission('cores.create', 'Você não tem permissão para criar cores.');
        
        return Inertia::render('cores/Create');
    }

    /**
     * Store a newly created cor.
     */
    public function store(CorStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('cores.store', 'Você não tem permissão para criar cores.');
        
        Cor::create($request->validated());

        return to_route('cores.index')
            ->with('success', 'Cor cadastrada com sucesso!');
    }

    /**
     * Display the specified cor.
     */
    public function show(Cor $cor): Response
    {
        $this->checkPermission('cores.show', 'Você não tem permissão para visualizar cores.');
        
        return Inertia::render('cores/Show', [
            'cor' => $cor,
        ]);
    }

    /**
     * Show the form for editing the specified cor.
     */
    public function edit(Cor $cor): Response
    {
        $this->checkPermission('cores.edit', 'Você não tem permissão para editar cores.');
        
        return Inertia::render('cores/Edit', [
            'cor' => $cor,
        ]);
    }

    /**
     * Update the specified cor.
     */
    public function update(CorUpdateRequest $request, Cor $cor): RedirectResponse
    {
        $this->checkPermission('cores.update', 'Você não tem permissão para editar cores.');
        
        $cor->update($request->validated());

        return to_route('cores.index')
            ->with('success', 'Cor atualizada com sucesso!');
    }

    /**
     * Remove the specified cor (soft delete).
     */
    public function destroy(Cor $cor): RedirectResponse
    {
        $this->checkPermission('cores.delete', 'Você não tem permissão para excluir cores.');
        
        $cor->delete();

        return to_route('cores.index')
            ->with('success', 'Cor excluída com sucesso!');
    }
}
