<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Verifica se o usuário tem uma permissão específica
     */
    protected function checkPermission(string $permission, string $message = null)
    {
        if (!auth()->user()->can($permission)) {
            $message = $message ?? "Você não tem permissão para realizar esta ação.";
            
            // Se for erro 403 em uma rota do dashboard, redireciona ao login
            if (request()->is('dashboard*')) {
                auth()->logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                redirect()->route('login')->with('error', $message)->send();
                exit;
            }
            
            abort(403, $message);
        }
    }
}
