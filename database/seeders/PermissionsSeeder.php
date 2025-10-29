<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões para o Dashboard
        $dashboardPermissions = [
            'dashboard.index',
            'dashboard.vendedor',
        ];

        foreach ($dashboardPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar permissões para Vendas
        $vendasPermissions = [
            'vendas.index',
            'vendas.create',
            'vendas.store',
            'vendas.show',
            'vendas.edit',
            'vendas.update',
            'vendas.destroy',
        ];

        foreach ($vendasPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar permissões para Clientes
        $clientesPermissions = [
            'clientes.index',
            'clientes.create',
            'clientes.store',
            'clientes.show',
            'clientes.edit',
            'clientes.update',
            'clientes.destroy',
        ];

        foreach ($clientesPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar permissões para Produtos
        $produtosPermissions = [
            'produtos.index',
            'produtos.show',
        ];

        foreach ($produtosPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar permissões administrativas
        $adminPermissions = [
            'empresas.index',
            'empresas.create',
            'empresas.store',
            'empresas.show',
            'empresas.edit',
            'empresas.update',
            'empresas.destroy',
            'usuarios.index',
            'usuarios.create',
            'usuarios.store',
            'usuarios.show',
            'usuarios.edit',
            'usuarios.update',
            'usuarios.destroy',
            'perfis.index',
            'perfis.create',
            'perfis.store',
            'perfis.show',
            'perfis.edit',
            'perfis.update',
            'perfis.destroy',
            'permissoes.index',
            'permissoes.create',
            'permissoes.store',
            'permissoes.edit',
            'permissoes.update',
            'permissoes.destroy',
            'lojas.index',
            'lojas.create',
            'lojas.store',
            'lojas.show',
            'lojas.edit',
            'lojas.update',
            'lojas.destroy',
            'categorias.index',
            'categorias.create',
            'categorias.store',
            'categorias.show',
            'categorias.edit',
            'categorias.update',
            'categorias.destroy',
            'marcas.index',
            'marcas.create',
            'marcas.store',
            'marcas.show',
            'marcas.edit',
            'marcas.update',
            'marcas.destroy',
            'tamanhos.index',
            'tamanhos.create',
            'tamanhos.store',
            'tamanhos.show',
            'tamanhos.edit',
            'tamanhos.update',
            'tamanhos.destroy',
            'cores.index',
            'cores.create',
            'cores.store',
            'cores.show',
            'cores.edit',
            'cores.update',
            'cores.destroy',
            'fornecedores.index',
            'fornecedores.create',
            'fornecedores.store',
            'fornecedores.show',
            'fornecedores.edit',
            'fornecedores.update',
            'fornecedores.destroy',
            'produtos.create',
            'produtos.store',
            'produtos.edit',
            'produtos.update',
            'produtos.destroy',
            'estoque.index',
            'estoque.entrada',
            'estoque.saida',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar Roles (Perfis)
        
        // Role: Administrador - Acesso total
        $adminRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Role: Gerente - Acesso amplo, mas sem gerenciar empresas e usuários
        $gerenteRole = Role::firstOrCreate(['name' => 'gerente', 'guard_name' => 'web']);
        $gerenteRole->syncPermissions([
            'dashboard.index',
            'vendas.index',
            'vendas.create',
            'vendas.store',
            'vendas.show',
            'vendas.edit',
            'vendas.update',
            'vendas.destroy',
            'clientes.index',
            'clientes.create',
            'clientes.store',
            'clientes.show',
            'clientes.edit',
            'clientes.update',
            'clientes.destroy',
            'produtos.index',
            'produtos.show',
            'produtos.create',
            'produtos.store',
            'produtos.edit',
            'produtos.update',
            'produtos.destroy',
            'categorias.index',
            'categorias.create',
            'categorias.store',
            'categorias.show',
            'categorias.edit',
            'categorias.update',
            'categorias.destroy',
            'marcas.index',
            'marcas.create',
            'marcas.store',
            'marcas.show',
            'marcas.edit',
            'marcas.update',
            'marcas.destroy',
            'fornecedores.index',
            'fornecedores.create',
            'fornecedores.store',
            'fornecedores.show',
            'fornecedores.edit',
            'fornecedores.update',
            'fornecedores.destroy',
            'estoque.index',
            'estoque.entrada',
            'estoque.saida',
            'lojas.index',
            'lojas.show',
        ]);

        // Role: Vendedor - Acesso limitado às funcionalidades de venda
        $vendedorRole = Role::firstOrCreate(['name' => 'vendedor', 'guard_name' => 'web']);
        $vendedorRole->syncPermissions([
            'dashboard.vendedor',
            'vendas.index',
            'vendas.create',
            'vendas.store',
            'vendas.show',
            'clientes.index',
            'clientes.create',
            'clientes.store',
            'clientes.show',
            'produtos.index',
            'produtos.show',
        ]);

        // Role: Estoquista - Acesso focado em estoque
        $estoquistaRole = Role::firstOrCreate(['name' => 'estoquista', 'guard_name' => 'web']);
        $estoquistaRole->syncPermissions([
            'dashboard.index',
            'produtos.index',
            'produtos.show',
            'produtos.create',
            'produtos.store',
            'produtos.edit',
            'produtos.update',
            'estoque.index',
            'estoque.entrada',
            'estoque.saida',
            'fornecedores.index',
            'fornecedores.show',
        ]);

        $this->command->info('Permissões e perfis criados com sucesso!');
        $this->command->info('Perfis criados: administrador, gerente, vendedor, estoquista');
    }
}

