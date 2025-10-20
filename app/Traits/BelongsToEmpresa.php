<?php

namespace App\Traits;

use App\Services\EmpresaContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToEmpresa
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToEmpresa()
    {
        // Automatically add empresa_id when creating
        static::creating(function (Model $model) {
            if (empty($model->empresa_id)) {
                $empresaContextService = app(EmpresaContextService::class);
                $empresaId = $empresaContextService->getCurrentEmpresaId();
                
                if ($empresaId) {
                    $model->empresa_id = $empresaId;
                }
            }
        });
        
        // Automatically scope queries to current empresa
        static::addGlobalScope('empresa', function (Builder $builder) {
            $empresaContextService = app(EmpresaContextService::class);
            $empresaId = $empresaContextService->getCurrentEmpresaId();
            
            if ($empresaId) {
                $builder->where('empresa_id', $empresaId);
            }
        });
    }
    
    /**
     * Get the empresa that owns the model
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }
    
    /**
     * Scope a query to only include records for a specific empresa
     */
    public function scopeForEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }
    
    /**
     * Scope a query to only include records for the current empresa context
     */
    public function scopeForCurrentEmpresa(Builder $query): Builder
    {
        $empresaContextService = app(EmpresaContextService::class);
        $empresaId = $empresaContextService->getCurrentEmpresaId();
        
        if ($empresaId) {
            return $query->where('empresa_id', $empresaId);
        }
        
        return $query->whereRaw('1 = 0'); // Return empty result if no empresa context
    }
    
    /**
     * Check if the model belongs to the current empresa context
     */
    public function belongsToCurrentEmpresa(): bool
    {
        $empresaContextService = app(EmpresaContextService::class);
        $currentEmpresaId = $empresaContextService->getCurrentEmpresaId();
        
        return $this->empresa_id === $currentEmpresaId;
    }
}
