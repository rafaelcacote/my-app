import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();
    
    const user = computed(() => page.props.auth?.user);
    const permissions = computed(() => user.value?.permissions || []);
    const roles = computed(() => user.value?.roles || []);

    /**
     * Verifica se o usuário tem uma permissão específica
     */
    const hasPermission = (permission: string): boolean => {
        return permissions.value.includes(permission);
    };

    /**
     * Verifica se o usuário tem uma das permissões fornecidas
     */
    const hasAnyPermission = (permissions: string[]): boolean => {
        return permissions.some(permission => hasPermission(permission));
    };

    /**
     * Verifica se o usuário tem todas as permissões fornecidas
     */
    const hasAllPermissions = (permissions: string[]): boolean => {
        return permissions.every(permission => hasPermission(permission));
    };

    /**
     * Verifica se o usuário tem um papel específico
     */
    const hasRole = (role: string): boolean => {
        return roles.value.includes(role);
    };

    /**
     * Verifica se o usuário tem um dos papéis fornecidos
     */
    const hasAnyRole = (roles: string[]): boolean => {
        return roles.some(role => hasRole(role));
    };

    /**
     * Verifica se o usuário tem todos os papéis fornecidos
     */
    const hasAllRoles = (roles: string[]): boolean => {
        return roles.every(role => hasRole(role));
    };

    /**
     * Verifica se o usuário é super admin
     */
    const isSuperAdmin = computed(() => {
        return hasRole('Super Admin') || hasPermission('super_admin');
    });

    /**
     * Verifica se o usuário pode acessar um módulo específico
     */
    const canAccessModule = (module: string): boolean => {
        const modulePermissions = [
            `${module}.index`,
            `${module}.create`,
            `${module}.show`,
            `${module}.edit`,
            `${module}.delete`
        ];
        
        return hasAnyPermission(modulePermissions) || isSuperAdmin.value;
    };

    return {
        user,
        permissions,
        roles,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
        hasRole,
        hasAnyRole,
        hasAllRoles,
        isSuperAdmin,
        canAccessModule,
    };
}
