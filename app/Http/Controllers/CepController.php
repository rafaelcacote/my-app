<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    /**
     * Search CEP using multiple APIs with fallback.
     *
     * @param  string  $cep
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($cep)
    {
        // Remove caracteres não numéricos
        $cep = preg_replace('/\D/', '', $cep);
        
        // Valida o CEP
        if (strlen($cep) != 8) {
            return response()->json([
                'error' => 'CEP inválido'
            ], 400);
        }
        
        // Tenta buscar o CEP em múltiplas APIs
        $data = $this->searchCepWithFallback($cep);
        
        if (!$data) {
            return response()->json([
                'error' => 'CEP não encontrado ou serviço temporariamente indisponível'
            ], 404);
        }
        
        // Busca o estado pelo UF
        $estado = Estado::where('uf', $data['uf'])->first();
        
        // Busca o município pelo nome e estado
        $municipio = null;
        if ($estado) {
            $municipio = Municipio::where('estado_id', $estado->id)
                ->where('nome', 'LIKE', '%' . $data['cidade'] . '%')
                ->first();
        }
        
        // Formata a resposta
        $resultado = [
            'cep' => $data['cep'],
            'endereco' => $data['endereco'],
            'complemento' => $data['complemento'],
            'bairro' => $data['bairro'],
            'cidade' => $data['cidade'],
            'uf' => $data['uf'],
            'estado_id' => $estado ? $estado->id : null,
            'municipio_id' => $municipio ? $municipio->id : null,
        ];
        
        return response()->json($resultado);
    }
    
    /**
     * Tenta buscar CEP em múltiplas APIs com fallback.
     *
     * @param  string  $cep
     * @return array|null
     */
    private function searchCepWithFallback($cep)
    {
        // Lista de APIs para tentar, em ordem de prioridade
        $apis = [
            'brasilapi' => fn() => $this->searchBrasilAPI($cep),
            'viacep' => fn() => $this->searchViaCEP($cep),
            'opencep' => fn() => $this->searchOpenCEP($cep),
        ];
        
        // Tenta cada API até conseguir uma resposta válida
        foreach ($apis as $apiName => $searchFunction) {
            try {
                $data = $searchFunction();
                if ($data) {
                    \Log::info("CEP encontrado usando {$apiName}: {$cep}");
                    return $data;
                }
            } catch (\Exception $e) {
                \Log::warning("Erro ao buscar CEP {$cep} na {$apiName}: " . $e->getMessage());
                continue;
            }
        }
        
        return null;
    }
    
    /**
     * Busca CEP na API BrasilAPI.
     *
     * @param  string  $cep
     * @return array|null
     */
    private function searchBrasilAPI($cep)
    {
        try {
            $response = Http::timeout(10)
                ->get("https://brasilapi.com.br/api/cep/v2/{$cep}");
            
            if (!$response->successful()) {
                return null;
            }
            
            $data = $response->json();
            
            return [
                'cep' => $data['cep'] ?? '',
                'endereco' => $data['street'] ?? '',
                'complemento' => '',
                'bairro' => $data['neighborhood'] ?? '',
                'cidade' => $data['city'] ?? '',
                'uf' => $data['state'] ?? '',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Busca CEP na API ViaCEP.
     *
     * @param  string  $cep
     * @return array|null
     */
    private function searchViaCEP($cep)
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
                'connect_timeout' => 5,
            ])->get("https://viacep.com.br/ws/{$cep}/json/");
            
            if (!$response->successful()) {
                return null;
            }
            
            $data = $response->json();
            
            // Verifica se o CEP foi encontrado
            if (isset($data['erro']) && $data['erro']) {
                return null;
            }
            
            return [
                'cep' => $data['cep'] ?? '',
                'endereco' => $data['logradouro'] ?? '',
                'complemento' => $data['complemento'] ?? '',
                'bairro' => $data['bairro'] ?? '',
                'cidade' => $data['localidade'] ?? '',
                'uf' => $data['uf'] ?? '',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Busca CEP na API OpenCEP.
     *
     * @param  string  $cep
     * @return array|null
     */
    private function searchOpenCEP($cep)
    {
        try {
            $response = Http::timeout(10)
                ->get("https://opencep.com/v1/{$cep}");
            
            if (!$response->successful()) {
                return null;
            }
            
            $data = $response->json();
            
            // Verifica se retornou erro
            if (isset($data['error'])) {
                return null;
            }
            
            return [
                'cep' => $data['cep'] ?? '',
                'endereco' => $data['logradouro'] ?? '',
                'complemento' => $data['complemento'] ?? '',
                'bairro' => $data['bairro'] ?? '',
                'cidade' => $data['localidade'] ?? '',
                'uf' => $data['uf'] ?? '',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
