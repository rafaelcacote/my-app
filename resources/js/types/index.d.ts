import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    cpf?: string;
    avatar?: string;
    email_verified_at: string | null;
    empresa_id?: number;
    tipo?: string;
    ativo: boolean;
    created_at: string;
    updated_at: string;
    empresa?: Empresa;
    lojas?: Loja[];
}

export interface Empresa {
    id: number;
    uuid: string;
    razao_social: string;
    nome_fantasia: string;
    cnpj?: string;
    email: string;
    telefone?: string;
    logo_path?: string;
    logo_url?: string;
    ativo: boolean;
    data_adesao: string;
    data_expiracao?: string;
    created_at: string;
    updated_at: string;
    deleted_at?: string;
}

export interface Loja {
    id: number;
    empresa_id: number;
    nome: string;
    cnpj?: string;
    telefone?: string;
    email?: string;
    ativo: boolean;
    created_at: string;
    updated_at: string;
    deleted_at?: string;
    empresa?: Empresa;
}

export type BreadcrumbItemType = BreadcrumbItem;
