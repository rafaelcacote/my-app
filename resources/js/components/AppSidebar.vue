<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubItem,
    SidebarMenuSubButton,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as empresasIndex } from '@/routes/empresas';
import { index as usersIndex } from '@/routes/users';
import { index as permissoesIndex } from '@/routes/permissoes';
import { index as perfisIndex } from '@/routes/perfis';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Building2, Users, Shield } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { usePermissions } from '@/composables/usePermissions';

const { canAccessModule, hasPermission } = usePermissions();

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Empresas',
        href: empresasIndex(),
        icon: Building2,
    },
    {
        title: 'Usuários',
        href: usersIndex(),
        icon: Users,
    },
].filter(item => {
    // Filtrar itens baseado nas permissões
    if (item.title === 'Empresas') {
        return canAccessModule('empresas');
    }
    if (item.title === 'Usuários') {
        return canAccessModule('users');
    }
    return true; // Dashboard sempre visível
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <!-- Dropdown: Permissões e Perfis -->
            <SidebarMenu v-if="canAccessModule('permissoes') || canAccessModule('perfis')" class="mt-2">
                <SidebarMenuItem>
                    <SidebarMenuButton as-child>
                        <div class="flex items-center gap-2">
                            <Shield />
                            <span>Permissões e Perfis</span>
                        </div>
                    </SidebarMenuButton>
                    <SidebarMenuSub>
                        <SidebarMenuSubItem v-if="canAccessModule('permissoes')">
                            <SidebarMenuSubButton as-child>
                                <Link :href="permissoesIndex().url">
                                    <span>Permissões</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                        <SidebarMenuSubItem v-if="canAccessModule('perfis')">
                            <SidebarMenuSubButton as-child>
                                <Link :href="perfisIndex().url">
                                    <span>Perfis</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                    </SidebarMenuSub>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
