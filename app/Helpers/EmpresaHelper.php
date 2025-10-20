<?php

namespace App\Helpers;

use App\Services\EmpresaContextService;

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
    public static function getCurrentEmpresa(): ?\App\Models\Empresa
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
    public static function refreshContext(): ?\App\Models\Empresa
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
}
