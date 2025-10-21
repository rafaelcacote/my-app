<?php

namespace App\Helpers;

use App\Models\Empresa;
use App\Services\EmpresaContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmpresaHelper
{
    /**
     * Obtém o ID da empresa atual do contexto
     */
    public static function getCurrentEmpresaId(): ?int
    {
        $service = app(EmpresaContextService::class);
        return $service->getCurrentEmpresaId();
    }
    
    /**
     * Obtém a empresa atual do contexto
     */
    public static function getCurrentEmpresa(): ?Empresa
    {
        $service = app(EmpresaContextService::class);
        return $service->getCurrentEmpresa();
    }
    
    /**
     * Verifica se há um contexto de empresa ativo
     */
    public static function hasContext(): bool
    {
        $service = app(EmpresaContextService::class);
        return $service->hasContext();
    }
    
    /**
     * Força a atualização do contexto
     */
    public static function refreshContext(): ?Empresa
    {
        $service = app(EmpresaContextService::class);
        return $service->refreshContext();
    }
    
    /**
     * Limpa o contexto da empresa
     */
    public static function clearContext(): void
    {
        $service = app(EmpresaContextService::class);
        $service->clearContext();
    }
    
    /**
     * Cria um modelo automaticamente vinculado à empresa atual
     * 
     * @param string $modelClass Classe do modelo
     * @param array $data Dados para criação
     * @return Model
     */
    public static function createForCurrentEmpresa(string $modelClass, array $data = []): Model
    {
        $empresaId = self::getCurrentEmpresaId();
        
        if (!$empresaId) {
            throw new \Exception('Nenhuma empresa encontrada no contexto atual');
        }
        
        $data['empresa_id'] = $empresaId;
        
        return $modelClass::create($data);
    }
    
    /**
     * Atualiza um modelo verificando se pertence à empresa atual
     * Funciona tanto para modelos com empresa_id direto quanto através de relacionamentos
     * 
     * @param Model $model Modelo a ser atualizado
     * @param array $data Dados para atualização
     * @return bool
     */
    public static function updateForCurrentEmpresa(Model $model, array $data = []): bool
    {
        $empresaId = self::getCurrentEmpresaId();
        
        if (!$empresaId) {
            throw new \Exception('Nenhuma empresa encontrada no contexto atual');
        }
        
        // Verifica se o modelo pertence à empresa atual
        if (!self::belongsToCurrentEmpresa($model)) {
            throw new \Exception('Este registro não pertence à empresa atual');
        }
        
        return $model->update($data);
    }
    
    /**
     * Deleta um modelo verificando se pertence à empresa atual
     * Funciona tanto para modelos com empresa_id direto quanto através de relacionamentos
     * 
     * @param Model $model Modelo a ser deletado
     * @return bool
     */
    public static function deleteForCurrentEmpresa(Model $model): bool
    {
        $empresaId = self::getCurrentEmpresaId();
        
        if (!$empresaId) {
            throw new \Exception('Nenhuma empresa encontrada no contexto atual');
        }
        
        // Verifica se o modelo pertence à empresa atual
        if (!self::belongsToCurrentEmpresa($model)) {
            throw new \Exception('Este registro não pertence à empresa atual');
        }
        
        return $model->delete();
    }
    
    /**
     * Busca um modelo por ID verificando se pertence à empresa atual
     * Funciona tanto para modelos com empresa_id direto quanto através de relacionamentos
     * 
     * @param string $modelClass Classe do modelo
     * @param int $id ID do modelo
     * @return Model|null
     */
    public static function findForCurrentEmpresa(string $modelClass, int $id): ?Model
    {
        $empresaId = self::getCurrentEmpresaId();
        
        if (!$empresaId) {
            return null;
        }
        
        // Verifica se o modelo usa o trait BelongsToEmpresaThroughLoja
        if (in_array('App\Traits\BelongsToEmpresaThroughLoja', class_uses_recursive($modelClass))) {
            return $modelClass::where('id', $id)
                ->whereHas('loja', function ($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->first();
        }
        
        // Verifica se o modelo usa o trait BelongsToEmpresaThroughProduto
        if (in_array('App\Traits\BelongsToEmpresaThroughProduto', class_uses_recursive($modelClass))) {
            return $modelClass::where('id', $id)
                ->whereHas('produto', function ($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })
                ->first();
        }
        
        // Para modelos com empresa_id direto
        return $modelClass::where('id', $id)
            ->where('empresa_id', $empresaId)
            ->first();
    }
    
    /**
     * Busca todos os modelos da empresa atual
     * Funciona tanto para modelos com empresa_id direto quanto através de relacionamentos
     * 
     * @param string $modelClass Classe do modelo
     * @return Builder
     */
    public static function queryForCurrentEmpresa(string $modelClass): Builder
    {
        $empresaId = self::getCurrentEmpresaId();
        
        if (!$empresaId) {
            // Retorna uma query que não retorna nada
            return $modelClass::whereRaw('1 = 0');
        }
        
        // Verifica se o modelo usa o trait BelongsToEmpresaThroughLoja
        if (in_array('App\Traits\BelongsToEmpresaThroughLoja', class_uses_recursive($modelClass))) {
            return $modelClass::whereHas('loja', function ($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId);
            });
        }
        
        // Verifica se o modelo usa o trait BelongsToEmpresaThroughProduto
        if (in_array('App\Traits\BelongsToEmpresaThroughProduto', class_uses_recursive($modelClass))) {
            return $modelClass::whereHas('produto', function ($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId);
            });
        }
        
        // Para modelos com empresa_id direto
        return $modelClass::where('empresa_id', $empresaId);
    }
    
    /**
     * Verifica se um modelo pertence à empresa atual
     * Funciona tanto para modelos com empresa_id direto quanto através de relacionamentos
     * 
     * @param Model $model Modelo a ser verificado
     * @return bool
     */
    public static function belongsToCurrentEmpresa(Model $model): bool
    {
        $empresaId = self::getCurrentEmpresaId();
        
        if (!$empresaId) {
            return false;
        }
        
        // Verifica se o modelo usa o trait BelongsToEmpresaThroughLoja
        if (in_array('App\Traits\BelongsToEmpresaThroughLoja', class_uses_recursive($model::class))) {
            return $model->loja?->empresa_id === $empresaId;
        }
        
        // Verifica se o modelo usa o trait BelongsToEmpresaThroughProduto
        if (in_array('App\Traits\BelongsToEmpresaThroughProduto', class_uses_recursive($model::class))) {
            return $model->produto?->empresa_id === $empresaId;
        }
        
        // Para modelos com empresa_id direto
        return $model->empresa_id === $empresaId;
    }
    
    /**
     * Executa uma função dentro do contexto de uma empresa específica
     * 
     * @param int $empresaId ID da empresa
     * @param callable $callback Função a ser executada
     * @return mixed
     */
    public static function withEmpresaContext(int $empresaId, callable $callback)
    {
        $service = app(EmpresaContextService::class);
        $empresa = Empresa::find($empresaId);
        
        if (!$empresa) {
            throw new \Exception("Empresa com ID {$empresaId} não encontrada");
        }
        
        // Salva o contexto atual
        $currentEmpresaId = self::getCurrentEmpresaId();
        
        try {
            // Define o novo contexto
            $service->setContext($empresa);
            
            // Executa a função
            return $callback();
        } finally {
            // Restaura o contexto anterior
            if ($currentEmpresaId) {
                $currentEmpresa = Empresa::find($currentEmpresaId);
                if ($currentEmpresa) {
                    $service->setContext($currentEmpresa);
                }
            } else {
                $service->clearContext();
            }
        }
    }
}
