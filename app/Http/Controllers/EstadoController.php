<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    /**
     * Get all estados.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $estados = Estado::orderBy('nome')->get(['id', 'nome', 'uf']);
        
        return response()->json($estados);
    }

    /**
     * Get a single estado by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $estado = Estado::with('municipios')->findOrFail($id);
        
        return response()->json($estado);
    }
}
