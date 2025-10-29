<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CriarVendedorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendedor:criar 
                            {nome : Nome do vendedor}
                            {email : Email do vendedor}
                            {--senha= : Senha do vendedor (padrão: senha123)}
                            {--empresa= : ID da empresa (padrão: primeira empresa)}
                            {--cpf= : CPF do vendedor (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um novo usuário com perfil de Vendedor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nome = $this->argument('nome');
        $email = $this->argument('email');
        $senha = $this->option('senha') ?? 'senha123';
        $empresaId = $this->option('empresa');
        $cpf = $this->option('cpf');

        // Validar email
        if (User::where('email', $email)->exists()) {
            $this->error("❌ Erro: Email '{$email}' já está em uso!");
            return 1;
        }

        // Obter empresa
        if (!$empresaId) {
            $empresa = Empresa::first();
            if (!$empresa) {
                $this->error('❌ Erro: Nenhuma empresa encontrada! Crie uma empresa primeiro.');
                return 1;
            }
            $empresaId = $empresa->id;
            $this->info("ℹ️  Usando empresa: {$empresa->nome_fantasia} (ID: {$empresaId})");
        } else {
            $empresa = Empresa::find($empresaId);
            if (!$empresa) {
                $this->error("❌ Erro: Empresa com ID {$empresaId} não encontrada!");
                return 1;
            }
        }

        // Criar usuário
        try {
            $user = User::create([
                'name' => $nome,
                'email' => $email,
                'password' => Hash::make($senha),
                'empresa_id' => $empresaId,
                'cpf' => $cpf,
                'tipo' => 'vendedor',
                'ativo' => true,
            ]);

            // Atribuir role de vendedor
            $user->assignRole('vendedor');

            $this->newLine();
            $this->info('✅ Vendedor criado com sucesso!');
            $this->newLine();
            
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID', $user->id],
                    ['Nome', $user->name],
                    ['Email', $user->email],
                    ['CPF', $user->cpf ?? 'Não informado'],
                    ['Senha', $senha],
                    ['Empresa', $empresa->nome_fantasia ?? 'N/A'],
                    ['Empresa ID', $empresaId],
                    ['Perfil', 'vendedor'],
                    ['Ativo', $user->ativo ? 'Sim' : 'Não'],
                ]
            );

            $this->newLine();
            $this->info('🔗 URL de acesso: ' . url('/login'));
            $this->info('📊 Dashboard: ' . url('/dashboard/vendedor'));
            $this->newLine();
            
            $this->comment('💡 Dica: O vendedor será automaticamente redirecionado para o dashboard específico após login.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erro ao criar vendedor: ' . $e->getMessage());
            return 1;
        }
    }
}

