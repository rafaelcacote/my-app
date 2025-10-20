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
        return Inertia::render('cores/Create');
    }

    /**
     * Store a newly created cor.
     */
    public function store(CorStoreRequest $request): RedirectResponse
    {
        Cor::create($request->validated());

        return to_route('cores.index')
            ->with('success', 'Cor cadastrada com sucesso!');
    }

    /**
     * Display the specified cor.
     */
    public function show(Cor $cor): Response
    {
        return Inertia::render('cores/Show', [
            'cor' => $cor,
        ]);
    }

    /**
     * Show the form for editing the specified cor.
     */
    public function edit(Cor $cor): Response
    {
        return Inertia::render('cores/Edit', [
            'cor' => $cor,
        ]);
    }

    /**
     * Update the specified cor.
     */
    public function update(CorUpdateRequest $request, Cor $cor): RedirectResponse
    {
        $cor->update($request->validated());

        return to_route('cores.index')
            ->with('success', 'Cor atualizada com sucesso!');
    }

    /**
     * Remove the specified cor (soft delete).
     */
    public function destroy(Cor $cor): RedirectResponse
    {
        $cor->delete();

        return to_route('cores.index')
            ->with('success', 'Cor excluída com sucesso!');
    }
}
