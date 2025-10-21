<?php

namespace App\Traits;

use App\Services\EmpresaContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToEmpresaThroughLoja
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToEmpresaThroughLoja()
    {
        // Automatically scope queries to current empresa through loja relationship
        static::addGlobalScope('empresa_through_loja', function (Builder $builder) {
            $empresaContextService = app(EmpresaContextService::class);
            $empresaId = $empresaContextService->getCurrentEmpresaId();
            
            if ($empresaId) {
                $builder->whereHas('loja', function ($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                });
            }
        });
    }
    
    /**
     * Get the empresa through loja relationship
     */
    public function empresa()
    {
        return $this->loja->empresa();
    }
    
    /**
     * Scope a query to only include records for a specific empresa through loja
     */
    public function scopeForEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->whereHas('loja', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }
    
    /**
     * Scope a query to only include records for the current empresa context through loja
     */
    public function scopeForCurrentEmpresa(Builder $query): Builder
    {
        $empresaContextService = app(EmpresaContextService::class);
        $empresaId = $empresaContextService->getCurrentEmpresaId();
        
        if ($empresaId) {
            return $query->whereHas('loja', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            });
        }
        
        return $query->whereRaw('1 = 0'); // Return empty result if no empresa context
    }
    
    /**
     * Check if the model belongs to the current empresa context through loja
     */
    public function belongsToCurrentEmpresa(): bool
    {
        $empresaContextService = app(EmpresaContextService::class);
        $currentEmpresaId = $empresaContextService->getCurrentEmpresaId();
        
        if (!$currentEmpresaId || !$this->loja) {
            return false;
        }
        
        return $this->loja->empresa_id === $currentEmpresaId;
    }
    
    /**
     * Get the empresa_id through loja relationship
     */
    public function getEmpresaIdAttribute(): ?int
    {
        return $this->loja?->empresa_id;
    }
}
