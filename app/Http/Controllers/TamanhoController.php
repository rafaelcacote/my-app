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
        return Inertia::render('tamanhos/Create');
    }

    /**
     * Store a newly created tamanho.
     */
    public function store(TamanhoStoreRequest $request): RedirectResponse
    {
        Tamanho::create($request->validated());

        return to_route('tamanhos.index')
            ->with('success', 'Tamanho cadastrado com sucesso!');
    }

    /**
     * Display the specified tamanho.
     */
    public function show(Tamanho $tamanho): Response
    {
        return Inertia::render('tamanhos/Show', [
            'tamanho' => $tamanho,
        ]);
    }

    /**
     * Show the form for editing the specified tamanho.
     */
    public function edit(Tamanho $tamanho): Response
    {
        return Inertia::render('tamanhos/Edit', [
            'tamanho' => $tamanho,
        ]);
    }

    /**
     * Update the specified tamanho.
     */
    public function update(TamanhoUpdateRequest $request, Tamanho $tamanho): RedirectResponse
    {
        $tamanho->update($request->validated());

        return to_route('tamanhos.index')
            ->with('success', 'Tamanho atualizado com sucesso!');
    }

    /**
     * Remove the specified tamanho (soft delete).
     */
    public function destroy(Tamanho $tamanho): RedirectResponse
    {
        $tamanho->delete();

        return to_route('tamanhos.index')
            ->with('success', 'Tamanho excluído com sucesso!');
    }
}
