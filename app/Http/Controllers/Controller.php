<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Verifica se o usuário tem uma permissão específica
     */
    protected function checkPermission(string $permission, string $message = null): void
    {
        if (!auth()->user()->can($permission)) {
            $message = $message ?? "Você não tem permissão para realizar esta ação.";
            abort(403, $message);
        }
    }
}
