<?php

namespace App\Http\Controllers;

use App\Helpers\EmpresaHelper;
use App\Models\Loja;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExemploController extends Controller
{
    /**
     * Exemplo de como usar o sistema de contexto de empresa
     */
    public function exemploLojas(): Response
    {
        // Com o trait BelongsToEmpresa, automaticamente filtra por empresa_id
        $lojas = Loja::all(); // Já filtra automaticamente pela empresa do usuário
        
        // Ou você pode usar explicitamente:
        $lojasExplicitas = Loja::forCurrentEmpresa()->get();
        
        // Ou para uma empresa específica:
        $empresaId = EmpresaHelper::getCurrentEmpresaId();
        $lojasEspecificas = Loja::forEmpresa($empresaId)->get();
        
        return Inertia::render('Exemplo', [
            'lojas' => $lojas,
            'empresa_atual' => EmpresaHelper::getCurrentEmpresa(),
        ]);
    }
    
    /**
     * Exemplo de criação com empresa_id automático
     */
    public function criarLoja(Request $request)
    {
        // O trait automaticamente adiciona empresa_id na criação
        $loja = Loja::create([
            'nome' => $request->nome,
            'cnpj' => $request->cnpj,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'ativo' => true,
            // empresa_id é adicionado automaticamente pelo trait
        ]);
        
        return response()->json(['success' => true, 'loja' => $loja]);
    }
    
    /**
     * Exemplo de verificação de contexto
     */
    public function verificarContexto()
    {
        $temContexto = EmpresaHelper::hasContext();
        $empresaId = EmpresaHelper::getCurrentEmpresaId();
        $empresa = EmpresaHelper::getCurrentEmpresa();
        
        return response()->json([
            'tem_contexto' => $temContexto,
            'empresa_id' => $empresaId,
            'empresa' => $empresa,
        ]);
    }
}
