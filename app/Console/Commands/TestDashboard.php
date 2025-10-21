<?php

namespace App\Console\Commands;

use App\Helpers\EmpresaHelper;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Console\Command;

class TestDashboard extends Command
{
    protected $signature = 'test:dashboard';
    protected $description = 'Testa o dashboard com dados de exemplo';

    public function handle()
    {
        $this->info('Testando Dashboard...');
        
        // Verifica se há empresas no banco
        $empresas = Empresa::count();
        $this->info("Empresas encontradas: {$empresas}");
        
        if ($empresas === 0) {
            $this->warn('Nenhuma empresa encontrada. Criando empresa de exemplo...');
            
            $empresa = Empresa::create([
                'nome' => 'Loja de Roupas Exemplo',
                'cnpj' => '12345678000199',
                'email' => 'contato@lojaexemplo.com',
                'telefone' => '(11) 99999-9999',
                'ativo' => true,
            ]);
            
            $this->info("Empresa criada com ID: {$empresa->id}");
        }
        
        // Verifica se há usuários
        $users = User::count();
        $this->info("Usuários encontrados: {$users}");
        
        if ($users === 0) {
            $this->warn('Nenhum usuário encontrado. Criando usuário de exemplo...');
            
            $empresa = Empresa::first();
            
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@lojaexemplo.com',
                'password' => bcrypt('password'),
                'empresa_id' => $empresa->id,
                'tipo' => 'admin',
                'ativo' => true,
            ]);
            
            $this->info("Usuário criado com ID: {$user->id}");
        }
        
        // Testa o EmpresaHelper
        $this->info('Testando EmpresaHelper...');
        
        // Simula um usuário logado
        $user = User::first();
        auth()->login($user);
        
        $empresaId = EmpresaHelper::getCurrentEmpresaId();
        $this->info("Empresa ID obtido: {$empresaId}");
        
        if ($empresaId) {
            $this->info('✅ Dashboard deve funcionar corretamente!');
        } else {
            $this->error('❌ Problema com o contexto da empresa');
        }
        
        return 0;
    }
}
