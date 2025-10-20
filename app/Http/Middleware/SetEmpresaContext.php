<?php

namespace App\Http\Middleware;

use App\Services\EmpresaContextService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetEmpresaContext
{
    protected EmpresaContextService $empresaContextService;
    
    public function __construct(EmpresaContextService $empresaContextService)
    {
        $this->empresaContextService = $empresaContextService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Só define o contexto se o usuário estiver autenticado
        if (auth()->check()) {
            $this->empresaContextService->setContextFromUser();
        }
        
        return $next($request);
    }
}
