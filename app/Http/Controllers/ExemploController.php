<?php

namespace App\Http\Controllers;

use App\Helpers\EmpresaHelper;
use App\Models\Categoria;
use App\Models\Loja;
use App\Models\Produto;
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
     * Exemplo de criação usando o helper EmpresaHelper
     */
    public function criarCategoria(Request $request)
    {
        try {
            // Usando o helper para criar automaticamente vinculado à empresa
            $categoria = EmpresaHelper::createForCurrentEmpresa(Categoria::class, [
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'ativo' => true,
            ]);
            
            return response()->json(['success' => true, 'categoria' => $categoria]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Exemplo de listagem usando diferentes métodos
     */
    public function listarProdutos()
    {
        // Método 1: Usando o trait (automático)
        $produtos1 = Produto::all();
        
        // Método 2: Usando o scope do trait
        $produtos2 = Produto::forCurrentEmpresa()->get();
        
        // Método 3: Usando o helper
        $produtos3 = EmpresaHelper::queryForCurrentEmpresa(Produto::class)->get();
        
        // Método 4: Query manual
        $empresaId = EmpresaHelper::getCurrentEmpresaId();
        $produtos4 = Produto::where('empresa_id', $empresaId)->get();
        
        return response()->json([
            'produtos_trait' => $produtos1,
            'produtos_scope' => $produtos2,
            'produtos_helper' => $produtos3,
            'produtos_manual' => $produtos4,
        ]);
    }
    
    /**
     * Exemplo de busca por ID com verificação de empresa
     */
    public function buscarProduto(int $id)
    {
        // Método 1: Usando o helper (recomendado)
        $produto = EmpresaHelper::findForCurrentEmpresa(Produto::class, $id);
        
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }
        
        return response()->json(['produto' => $produto]);
    }
    
    /**
     * Exemplo de atualização com verificação de empresa
     */
    public function atualizarProduto(Request $request, int $id)
    {
        try {
            $produto = EmpresaHelper::findForCurrentEmpresa(Produto::class, $id);
            
            if (!$produto) {
                return response()->json(['message' => 'Produto não encontrado'], 404);
            }
            
            // Usando o helper para atualizar com verificação
            $success = EmpresaHelper::updateForCurrentEmpresa($produto, [
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'preco_venda' => $request->preco_venda,
            ]);
            
            return response()->json(['success' => $success, 'produto' => $produto->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Exemplo de exclusão com verificação de empresa
     */
    public function excluirProduto(int $id)
    {
        try {
            $produto = EmpresaHelper::findForCurrentEmpresa(Produto::class, $id);
            
            if (!$produto) {
                return response()->json(['message' => 'Produto não encontrado'], 404);
            }
            
            // Usando o helper para deletar com verificação
            $success = EmpresaHelper::deleteForCurrentEmpresa($produto);
            
            return response()->json(['success' => $success]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
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
    
    /**
     * Exemplo de execução em contexto específico de empresa
     */
    public function exemploContextoEspecifico(int $empresaId)
    {
        try {
            $resultado = EmpresaHelper::withEmpresaContext($empresaId, function () {
                // Dentro deste contexto, todas as operações serão para a empresa específica
                $categorias = Categoria::all(); // Será filtrado pela empresa do contexto
                $produtos = Produto::all(); // Será filtrado pela empresa do contexto
                
                return [
                    'categorias' => $categorias,
                    'produtos' => $produtos,
                ];
            });
            
            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
