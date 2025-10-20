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
import { index as categoriasIndex } from '@/routes/categorias';
import { index as coresIndex } from '@/routes/cores';
import { index as marcasIndex } from '@/routes/marcas';
import { index as tamanhosIndex } from '@/routes/tamanhos';
import { index as produtosIndex } from '@/routes/produtos';
import { index as produtoVariacoesIndex } from '@/routes/produto-variacoes';
import { index as movimentacoesEstoqueIndex } from '@/routes/movimentacoes-estoque';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Building2, Users, Shield, Package, Tag, Palette, Ruler, Layers, ShoppingCart, BarChart3 } from 'lucide-vue-next';
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

// Menu de Produtos e Estoque
const produtoEstoqueNavItems: NavItem[] = [
    {
        title: 'Produtos',
        href: produtosIndex(),
        icon: Package,
    },
    {
        title: 'Categorias',
        href: categoriasIndex(),
        icon: Tag,
    },
    {
        title: 'Marcas',
        href: marcasIndex(),
        icon: Tag,
    },
    {
        title: 'Cores',
        href: coresIndex(),
        icon: Palette,
    },
    {
        title: 'Tamanhos',
        href: tamanhosIndex(),
        icon: Ruler,
    },
    {
        title: 'Variações',
        href: produtoVariacoesIndex(),
        icon: Layers,
    },
    {
        title: 'Movimentações',
        href: movimentacoesEstoqueIndex(),
        icon: BarChart3,
    },
];

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

            <!-- Dropdown: Produtos e Estoque -->
            <SidebarMenu class="mt-2">
                <SidebarMenuItem>
                    <SidebarMenuButton as-child>
                        <div class="flex items-center gap-2">
                            <Package />
                            <span>Produtos e Estoque</span>
                        </div>
                    </SidebarMenuButton>
                    <SidebarMenuSub>
                        <SidebarMenuSubItem>
                            <SidebarMenuSubButton as-child>
                                <Link :href="produtosIndex().url">
                                    <Package class="w-4 h-4" />
                                    <span>Produtos</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                        <SidebarMenuSubItem>
                            <SidebarMenuSubButton as-child>
                                <Link :href="categoriasIndex().url">
                                    <Tag class="w-4 h-4" />
                                    <span>Categorias</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                        <SidebarMenuSubItem>
                            <SidebarMenuSubButton as-child>
                                <Link :href="marcasIndex().url">
                                    <Tag class="w-4 h-4" />
                                    <span>Marcas</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                        <SidebarMenuSubItem>
                            <SidebarMenuSubButton as-child>
                                <Link :href="coresIndex().url">
                                    <Palette class="w-4 h-4" />
                                    <span>Cores</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                        <SidebarMenuSubItem>
                            <SidebarMenuSubButton as-child>
                                <Link :href="tamanhosIndex().url">
                                    <Ruler class="w-4 h-4" />
                                    <span>Tamanhos</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                        <SidebarMenuSubItem>
                            <SidebarMenuSubButton as-child>
                                <Link :href="produtoVariacoesIndex().url">
                                    <Layers class="w-4 h-4" />
                                    <span>Variações</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                        <SidebarMenuSubItem>
                            <SidebarMenuSubButton as-child>
                                <Link :href="movimentacoesEstoqueIndex().url">
                                    <BarChart3 class="w-4 h-4" />
                                    <span>Movimentações</span>
                                </Link>
                            </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                    </SidebarMenuSub>
                </SidebarMenuItem>
            </SidebarMenu>

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
