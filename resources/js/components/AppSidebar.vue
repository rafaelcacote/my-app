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
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
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
import { index as fornecedoresIndex } from '@/routes/fornecedores';
import { index as entradasMercadoriaIndex } from '@/routes/entradas-mercadoria';
import { index as clientesIndex } from '@/routes/clientes';
import { index as vendasIndex } from '@/routes/vendas';
import { index as pagamentosIndex } from '@/routes/pagamentos';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Building2, Users, Shield, Package, Tag, Palette, Ruler, Layers, ShoppingCart, BarChart3, Truck, PackageCheck, ChevronDown, CreditCard, Receipt, KeyRound, UserCheck } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { usePermissions } from '@/composables/usePermissions';
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { urlIsActive } from '@/lib/utils';

const { canAccessModule, hasPermission } = usePermissions();
const page = usePage();

// Estados dos dropdowns
const produtoEstoqueOpen = ref(false);
const gestaoMercadoriasOpen = ref(false);
const vendasFinanceiroOpen = ref(false);
const permissoesPerfisOpen = ref(false);
const empresasUsuariosOpen = ref(false);

// URLs dos grupos de menu
const produtoEstoqueUrls = [
    produtosIndex().url,
    categoriasIndex().url,
    marcasIndex().url,
    coresIndex().url,
    tamanhosIndex().url,
    produtoVariacoesIndex().url,
    movimentacoesEstoqueIndex().url,
];

const gestaoMercadoriasUrls = [
    fornecedoresIndex().url,
    entradasMercadoriaIndex().url,
];

const vendasFinanceiroUrls = [
    clientesIndex().url,
    vendasIndex().url,
    pagamentosIndex().url,
];

const permissoesPerfisUrls = [
    permissoesIndex().url,
    perfisIndex().url,
];

const empresasUsuariosUrls = [
    empresasIndex().url,
    usersIndex().url,
];

// Função para verificar se alguma URL do grupo está ativa
const isGroupActive = (urls: string[]) => {
    return urls.some(url => urlIsActive(url, page.url));
};

// Função para verificar se um item específico está ativo
const isItemActive = (url: string) => {
    return urlIsActive(url, page.url);
};

// Computed para controlar automaticamente os dropdowns baseado na URL ativa
const currentUrl = computed(() => page.url);

// Watcher para abrir automaticamente o dropdown correto baseado na URL
watch(currentUrl, (newUrl) => {
    if (isGroupActive(produtoEstoqueUrls)) {
        produtoEstoqueOpen.value = true;
    }
    if (isGroupActive(gestaoMercadoriasUrls)) {
        gestaoMercadoriasOpen.value = true;
    }
    if (isGroupActive(vendasFinanceiroUrls)) {
        vendasFinanceiroOpen.value = true;
    }
    if (isGroupActive(permissoesPerfisUrls)) {
        permissoesPerfisOpen.value = true;
    }
    if (isGroupActive(empresasUsuariosUrls)) {
        empresasUsuariosOpen.value = true;
    }
}, { immediate: true });

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
].filter(item => {
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

            <!-- Dropdown: Empresas e Usuários -->
            <SidebarMenu v-if="canAccessModule('empresas') || canAccessModule('users')" class="mt-2">
                <SidebarMenuItem>
                    <Collapsible v-model:open="empresasUsuariosOpen">
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :is-active="isGroupActive(empresasUsuariosUrls)">
                                <Building2 />
                                <span>Empresas e Usuários</span>
                                <ChevronDown 
                                    class="ml-auto h-4 w-4 transition-transform duration-200" 
                                    :class="{ 'rotate-180': empresasUsuariosOpen }"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-if="canAccessModule('empresas')">
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(empresasIndex().url)">
                                        <Link :href="empresasIndex().url">
                                            <Building2 class="w-4 h-4" />
                                            <span>Empresas</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem v-if="canAccessModule('users')">
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(usersIndex().url)">
                                        <Link :href="usersIndex().url">
                                            <Users class="w-4 h-4" />
                                            <span>Usuários</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </Collapsible>
                </SidebarMenuItem>
            </SidebarMenu>

            <!-- Dropdown: Produtos e Estoque -->
            <SidebarMenu class="mt-2">
                <SidebarMenuItem>
                    <Collapsible v-model:open="produtoEstoqueOpen">
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :is-active="isGroupActive(produtoEstoqueUrls)">
                                <Package />
                                <span>Produtos e Estoque</span>
                                <ChevronDown 
                                    class="ml-auto h-4 w-4 transition-transform duration-200" 
                                    :class="{ 'rotate-180': produtoEstoqueOpen }"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(produtosIndex().url)">
                                        <Link :href="produtosIndex().url">
                                            <Package class="w-4 h-4" />
                                            <span>Produtos</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(categoriasIndex().url)">
                                        <Link :href="categoriasIndex().url">
                                            <Tag class="w-4 h-4" />
                                            <span>Categorias</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(marcasIndex().url)">
                                        <Link :href="marcasIndex().url">
                                            <Tag class="w-4 h-4" />
                                            <span>Marcas</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(coresIndex().url)">
                                        <Link :href="coresIndex().url">
                                            <Palette class="w-4 h-4" />
                                            <span>Cores</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(tamanhosIndex().url)">
                                        <Link :href="tamanhosIndex().url">
                                            <Ruler class="w-4 h-4" />
                                            <span>Tamanhos</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(produtoVariacoesIndex().url)">
                                        <Link :href="produtoVariacoesIndex().url">
                                            <Layers class="w-4 h-4" />
                                            <span>Variações</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(movimentacoesEstoqueIndex().url)">
                                        <Link :href="movimentacoesEstoqueIndex().url">
                                            <BarChart3 class="w-4 h-4" />
                                            <span>Movimentações</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </Collapsible>
                </SidebarMenuItem>
            </SidebarMenu>

            <!-- Dropdown: Gestão de Mercadorias -->
            <SidebarMenu class="mt-2">
                <SidebarMenuItem>
                    <Collapsible v-model:open="gestaoMercadoriasOpen">
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :is-active="isGroupActive(gestaoMercadoriasUrls)">
                                <Truck />
                                <span>Gestão de Mercadorias</span>
                                <ChevronDown 
                                    class="ml-auto h-4 w-4 transition-transform duration-200" 
                                    :class="{ 'rotate-180': gestaoMercadoriasOpen }"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(fornecedoresIndex().url)">
                                        <Link :href="fornecedoresIndex().url">
                                            <Truck class="w-4 h-4" />
                                            <span>Fornecedores</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(entradasMercadoriaIndex().url)">
                                        <Link :href="entradasMercadoriaIndex().url">
                                            <PackageCheck class="w-4 h-4" />
                                            <span>Entradas de Mercadoria</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </Collapsible>
                </SidebarMenuItem>
            </SidebarMenu>

            <!-- Dropdown: Vendas e Financeiro -->
            <SidebarMenu class="mt-2">
                <SidebarMenuItem>
                    <Collapsible v-model:open="vendasFinanceiroOpen">
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :is-active="isGroupActive(vendasFinanceiroUrls)">
                                <ShoppingCart />
                                <span>Vendas e Financeiro</span>
                                <ChevronDown 
                                    class="ml-auto h-4 w-4 transition-transform duration-200" 
                                    :class="{ 'rotate-180': vendasFinanceiroOpen }"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(clientesIndex().url)">
                                        <Link :href="clientesIndex().url">
                                            <Users class="w-4 h-4" />
                                            <span>Clientes</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(vendasIndex().url)">
                                        <Link :href="vendasIndex().url">
                                            <Receipt class="w-4 h-4" />
                                            <span>Vendas</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem>
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(pagamentosIndex().url)">
                                        <Link :href="pagamentosIndex().url">
                                            <CreditCard class="w-4 h-4" />
                                            <span>Pagamentos</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </Collapsible>
                </SidebarMenuItem>
            </SidebarMenu>

            <!-- Dropdown: Permissões e Perfis -->
            <SidebarMenu v-if="canAccessModule('permissoes') || canAccessModule('perfis')" class="mt-2">
                <SidebarMenuItem>
                    <Collapsible v-model:open="permissoesPerfisOpen">
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :is-active="isGroupActive(permissoesPerfisUrls)">
                                <Shield />
                                <span>Permissões e Perfis</span>
                                <ChevronDown 
                                    class="ml-auto h-4 w-4 transition-transform duration-200" 
                                    :class="{ 'rotate-180': permissoesPerfisOpen }"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-if="canAccessModule('permissoes')">
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(permissoesIndex().url)">
                                        <Link :href="permissoesIndex().url">
                                            <KeyRound class="w-4 h-4" />
                                            <span>Permissões</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem v-if="canAccessModule('perfis')">
                                    <SidebarMenuSubButton as-child :is-active="isItemActive(perfisIndex().url)">
                                        <Link :href="perfisIndex().url">
                                            <UserCheck class="w-4 h-4" />
                                            <span>Perfis</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </Collapsible>
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
