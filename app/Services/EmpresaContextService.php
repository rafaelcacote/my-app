<?php

namespace App\Services;

use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EmpresaContextService
{
    const SESSION_KEY = 'empresa_context_id';
    
    /**
     * Define a empresa atual do contexto baseada no usuário logado
     */
    public function setContextFromUser(): ?Empresa
    {
        $user = Auth::user();
        
        if (!$user || !$user->empresa_id) {
            $this->clearContext();
            return null;
        }
        
        $empresa = $user->empresa;
        
        if ($empresa) {
            $this->setContext($empresa);
            return $empresa;
        }
        
        $this->clearContext();
        return null;
    }
    
    /**
     * Define uma empresa específica no contexto
     */
    public function setContext(Empresa $empresa): void
    {
        Session::put(self::SESSION_KEY, $empresa->id);
    }
    
    /**
     * Obtém a empresa atual do contexto
     */
    public function getCurrentEmpresa(): ?Empresa
    {
        $empresaId = Session::get(self::SESSION_KEY);
        
        if (!$empresaId) {
            return $this->setContextFromUser();
        }
        
        $empresa = Empresa::find($empresaId);
        
        // Se a empresa não existe mais ou o usuário não tem acesso, limpa o contexto
        if (!$empresa || !$this->userHasAccessToEmpresa($empresa)) {
            $this->clearContext();
            return $this->setContextFromUser();
        }
        
        return $empresa;
    }
    
    /**
     * Obtém apenas o ID da empresa atual
     */
    public function getCurrentEmpresaId(): ?int
    {
        $empresa = $this->getCurrentEmpresa();
        return $empresa ? $empresa->id : null;
    }
    
    /**
     * Limpa o contexto da empresa
     */
    public function clearContext(): void
    {
        Session::forget(self::SESSION_KEY);
    }
    
    /**
     * Verifica se o usuário tem acesso à empresa
     */
    private function userHasAccessToEmpresa(Empresa $empresa): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Se o usuário é da empresa ou é admin (você pode implementar lógica de admin aqui)
        return $user->empresa_id === $empresa->id;
    }
    
    /**
     * Verifica se há um contexto de empresa ativo
     */
    public function hasContext(): bool
    {
        return Session::has(self::SESSION_KEY) && $this->getCurrentEmpresa() !== null;
    }
    
    /**
     * Força a atualização do contexto baseado no usuário atual
     */
    public function refreshContext(): ?Empresa
    {
        $this->clearContext();
        return $this->setContextFromUser();
    }
}
