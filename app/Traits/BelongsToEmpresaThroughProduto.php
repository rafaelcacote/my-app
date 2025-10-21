<?php

namespace App\Traits;

use App\Services\EmpresaContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToEmpresaThroughProduto
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToEmpresaThroughProduto()
    {
        // Automatically scope queries to current empresa through produto relationship
        static::addGlobalScope('empresa_through_produto', function (Builder $builder) {
            $empresaContextService = app(EmpresaContextService::class);
            $empresaId = $empresaContextService->getCurrentEmpresaId();
            
            if ($empresaId) {
                $builder->whereHas('produto', function ($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                });
            }
        });
    }
    
    /**
     * Get the empresa through produto relationship
     */
    public function empresa()
    {
        return $this->produto->empresa();
    }
    
    /**
     * Scope a query to only include records for a specific empresa through produto
     */
    public function scopeForEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->whereHas('produto', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }
    
    /**
     * Scope a query to only include records for the current empresa context through produto
     */
    public function scopeForCurrentEmpresa(Builder $query): Builder
    {
        $empresaContextService = app(EmpresaContextService::class);
        $empresaId = $empresaContextService->getCurrentEmpresaId();
        
        if ($empresaId) {
            return $query->whereHas('produto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            });
        }
        
        return $query->whereRaw('1 = 0'); // Return empty result if no empresa context
    }
    
    /**
     * Check if the model belongs to the current empresa context through produto
     */
    public function belongsToCurrentEmpresa(): bool
    {
        $empresaContextService = app(EmpresaContextService::class);
        $currentEmpresaId = $empresaContextService->getCurrentEmpresaId();
        
        if (!$currentEmpresaId || !$this->produto) {
            return false;
        }
        
        return $this->produto->empresa_id === $currentEmpresaId;
    }
    
    /**
     * Get the empresa_id through produto relationship
     */
    public function getEmpresaIdAttribute(): ?int
    {
        return $this->produto?->empresa_id;
    }
}
