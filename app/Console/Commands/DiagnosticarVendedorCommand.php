<?php

namespace App\Console\Commands;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Console\Command;

class DiagnosticarVendedorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendedor:diagnosticar {email : Email do vendedor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostica e corrige problemas de permissão do vendedor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('🔍 Iniciando diagnóstico...');
        $this->newLine();
        
        // 1. Verificar se o usuário existe
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ Usuário com email '{$email}' não encontrado!");
            return 1;
        }
        
        $this->info("✅ Usuário encontrado: {$user->name} (ID: {$user->id})");
        $this->newLine();
        
        // 2. Verificar roles
        $this->info('📋 Verificando perfis (roles)...');
        $roles = $user->roles;
        
        if ($roles->isEmpty()) {
            $this->warn('⚠️  Usuário NÃO tem nenhum perfil atribuído!');
        } else {
            $this->info('✅ Perfis atribuídos:');
            foreach ($roles as $role) {
                $this->line("   - {$role->name}");
            }
        }
        $this->newLine();
        
        // 3. Verificar se o role "vendedor" existe
        $this->info('🔍 Verificando se o perfil "vendedor" existe...');
        $vendedorRole = Role::where('name', 'vendedor')->first();
        
        if (!$vendedorRole) {
            $this->error('❌ Perfil "vendedor" NÃO existe!');
            $this->warn('💡 Você precisa executar: php artisan db:seed --class=PermissionsSeeder');
            return 1;
        }
        
        $this->info('✅ Perfil "vendedor" existe');
        $this->newLine();
        
        // 4. Verificar se a permissão "dashboard.vendedor" existe
        $this->info('🔍 Verificando permissão "dashboard.vendedor"...');
        $permission = Permission::where('name', 'dashboard.vendedor')->first();
        
        if (!$permission) {
            $this->error('❌ Permissão "dashboard.vendedor" NÃO existe!');
            $this->warn('💡 Você precisa executar: php artisan db:seed --class=PermissionsSeeder');
            return 1;
        }
        
        $this->info('✅ Permissão "dashboard.vendedor" existe');
        $this->newLine();
        
        // 5. Verificar se o role tem a permissão
        $this->info('🔍 Verificando se o perfil "vendedor" tem a permissão...');
        $roleHasPermission = $vendedorRole->hasPermissionTo('dashboard.vendedor');
        
        if (!$roleHasPermission) {
            $this->warn('⚠️  Perfil "vendedor" NÃO tem a permissão "dashboard.vendedor"!');
            
            if ($this->confirm('Deseja adicionar a permissão ao perfil?', true)) {
                $vendedorRole->givePermissionTo('dashboard.vendedor');
                $this->info('✅ Permissão adicionada ao perfil "vendedor"');
            }
        } else {
            $this->info('✅ Perfil "vendedor" tem a permissão');
        }
        $this->newLine();
        
        // 6. Verificar se o usuário tem o role
        $userHasRole = $user->hasRole('vendedor');
        
        if (!$userHasRole) {
            $this->warn('⚠️  Usuário NÃO tem o perfil "vendedor"!');
            
            if ($this->confirm('Deseja atribuir o perfil "vendedor" ao usuário?', true)) {
                $user->assignRole('vendedor');
                $this->info('✅ Perfil "vendedor" atribuído ao usuário');
                $user = $user->fresh(); // Recarregar
            }
        } else {
            $this->info('✅ Usuário tem o perfil "vendedor"');
        }
        $this->newLine();
        
        // 7. Verificar permissões do usuário
        $this->info('📋 Permissões do usuário:');
        $userPermissions = $user->getAllPermissions();
        
        if ($userPermissions->isEmpty()) {
            $this->warn('⚠️  Usuário não tem nenhuma permissão!');
        } else {
            foreach ($userPermissions as $perm) {
                $icon = $perm->name === 'dashboard.vendedor' ? '✅' : '  ';
                $this->line("   {$icon} {$perm->name}");
            }
        }
        $this->newLine();
        
        // 8. Testar se pode acessar
        $canAccess = $user->can('dashboard.vendedor');
        
        if ($canAccess) {
            $this->info('🎉 SUCESSO! Usuário PODE acessar o dashboard do vendedor!');
            $this->newLine();
            
            // Limpar cache de permissões
            $this->info('🔄 Limpando cache de permissões...');
            $this->call('permission:cache-reset');
            
            $this->newLine();
            $this->info('✅ Tudo configurado corretamente!');
            $this->info('🔗 O usuário pode fazer login em: ' . url('/login'));
        } else {
            $this->error('❌ FALHA! Usuário NÃO pode acessar o dashboard.');
            $this->warn('💡 Tente executar: php artisan db:seed --class=PermissionsSeeder');
        }
        
        return 0;
    }
}

