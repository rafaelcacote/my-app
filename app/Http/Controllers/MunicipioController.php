<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    /**
     * Get municipios by estado_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $estadoId = $request->query('estado_id');
        
        $query = Municipio::query();
        
        if ($estadoId) {
            $query->where('estado_id', $estadoId);
        }
        
        $municipios = $query->orderBy('nome')->get(['id', 'nome', 'estado_id']);
        
        return response()->json($municipios);
    }

    /**
     * Get a single municipio by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $municipio = Municipio::with('estado')->findOrFail($id);
        
        return response()->json($municipio);
    }
}
