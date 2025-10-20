<?php

namespace App\Helpers;

use Spatie\Permission\Models\Permission;

class PermissionHelper
{
    /**
     * Organiza as permissões por módulo baseado no nome da permissão.
     * 
     * @param array $permissions Array de permissões
     * @return array Array organizado por módulo
     */
    public static function groupByModule(array $permissions): array
    {
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $module = self::extractModuleFromPermissionName($permission['name']);
            
            if (!isset($grouped[$module])) {
                $grouped[$module] = [
                    'name' => $module,
                    'permissions' => []
                ];
            }
            
            $grouped[$module]['permissions'][] = $permission;
        }
        
        // Ordenar módulos alfabeticamente e converter para array numérico
        ksort($grouped);
        
        return array_values($grouped);
    }
    
    /**
     * Extrai o nome do módulo do nome da permissão.
     * 
     * @param string $permissionName Nome da permissão (ex: "users.create", "empresas.edit")
     * @return string Nome do módulo
     */
    public static function extractModuleFromPermissionName(string $permissionName): string
    {
        // Se a permissão contém um ponto, pega a parte antes do ponto
        if (strpos($permissionName, '.') !== false) {
            $parts = explode('.', $permissionName);
            return ucfirst($parts[0]);
        }
        
        // Se não contém ponto, tenta extrair baseado em padrões comuns
        $patterns = [
            '/^(create|edit|delete|view|index|show|store|update|destroy)/i',
            '/^(manage|access|admin)/i'
        ];
        
        foreach ($patterns as $pattern) {
            $permissionName = preg_replace($pattern, '', $permissionName);
        }
        
        return ucfirst(trim($permissionName)) ?: 'Geral';
    }
    
    /**
     * Obtém todas as permissões organizadas por módulo.
     * 
     * @return array
     */
    public static function getAllPermissionsGrouped(): array
    {
        $permissions = Permission::all()->toArray();
        return self::groupByModule($permissions);
    }
    
    /**
     * Obtém permissões de um role organizadas por módulo.
     * 
     * @param \Spatie\Permission\Models\Role $role
     * @return array
     */
    public static function getRolePermissionsGrouped($role): array
    {
        $permissions = $role->permissions->toArray();
        return self::groupByModule($permissions);
    }
}
