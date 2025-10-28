<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FixAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *  php artisan fix:admin-permissions {--email=admin@admin.com} {--role="Administrador Geral"} {--apply}
     */
    protected $signature = 'fix:admin-permissions {--email=admin@admin.com} {--role=Administrador Geral} {--apply : Create missing permissions, ensure role has all, and assign role to user}';

    /**
     * The console command description.
     */
    protected $description = 'Verifica e (opcionalmente) corrige permissões do Administrador Geral e do usuário admin.';

    public function handle(): int
    {
        $email = (string) $this->option('email');
        $roleName = (string) $this->option('role');
        $apply = (bool) $this->option('apply');

        $this->info("Checando usuário: {$email}");
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuário com email {$email} não encontrado.");
            return self::FAILURE;
        }

        // Coleta permissões referenciadas nos controllers via checkPermission('x.y')
        $referenced = $this->collectReferencedPermissions();
        $this->line('Permissões referenciadas (controllers): '.count($referenced));

        // Garante existência das permissões (apenas quando --apply)
        if ($apply) {
            foreach ($referenced as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
        }

        // Carrega do banco
        $dbPermissions = Permission::orderBy('name')->pluck('name')->toArray();
        $missingInDb = array_values(array_diff($referenced, $dbPermissions));

        if (!empty($missingInDb)) {
            $this->warn('Permissões faltando no banco:');
            foreach ($missingInDb as $p) {
                $this->line(" - {$p}");
            }
        } else {
            $this->info('Nenhuma permissão faltando no banco para as referências atuais.');
        }

        // Papel (role) Administrador Geral
        $role = Role::where('name', $roleName)->first();
        if (!$role && $apply) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            $this->info("Role criado: {$roleName}");
        }

        if ($role) {
            $rolePerms = $role->permissions()->pluck('name')->toArray();
            $missingInRole = array_values(array_diff($dbPermissions, $rolePerms));
            $this->line("Permissões no role '{$roleName}': ".count($rolePerms));

            if (!empty($missingInRole)) {
                $this->warn("Permissões faltando no role '{$roleName}':");
                foreach ($missingInRole as $p) {
                    $this->line(" - {$p}");
                }

                if ($apply) {
                    $role->syncPermissions($dbPermissions);
                    $this->info("Role '{$roleName}' sincronizado com todas as permissões.");
                }
            } else {
                $this->info("Role '{$roleName}' já possui todas as permissões do sistema.");
            }
        } else {
            $this->warn("Role '{$roleName}' não encontrado.");
        }

        // Atribui o role ao usuário
        if ($role) {
            $hasRole = $user->roles()->where('name', $roleName)->exists();
            if (!$hasRole) {
                if ($apply) {
                    $user->assignRole($roleName);
                    $this->info("Role '{$roleName}' atribuído ao usuário {$email}.");
                } else {
                    $this->warn("Usuário não possui o role '{$roleName}'. Use --apply para atribuir.");
                }
            } else {
                $this->info("Usuário já possui o role '{$roleName}'.");
            }
        }

        // Lista final: roles e permissões do usuário
        $this->line('Roles do usuário:');
        foreach ($user->roles()->pluck('name')->toArray() as $r) {
            $this->line(" - {$r}");
        }

        $userPerms = $user->getAllPermissions()->pluck('name')->sort()->values()->toArray();
        $this->line('Quantidade de permissões efetivas do usuário: '.count($userPerms));

        // Checagens específicas para edição de usuários
        $toCheck = ['users.edit', 'users.update'];
        foreach ($toCheck as $permName) {
            $this->line("Pode '{$permName}'? ".($user->can($permName) ? 'SIM' : 'NÃO'));
        }

        $this->info('Concluído.');
        return self::SUCCESS;
    }

    /**
     * Coleta permissões referenciadas em app/Http/Controllers via regex.
     * @return array<string>
     */
    private function collectReferencedPermissions(): array
    {
        $base = base_path('app/Http/Controllers');
        $dir = new RecursiveDirectoryIterator($base);
        $it = new RecursiveIteratorIterator($dir);
        $perms = [];

        foreach ($it as $file) {
            if (!$file->isFile()) {
                continue;
            }
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $content = @file_get_contents($file->getPathname());
            if ($content === false) {
                continue;
            }
            if (preg_match_all("/checkPermission\(['\"]([a-zA-Z0-9_.-]+)['\"]/", $content, $m)) {
                foreach ($m[1] as $perm) {
                    $perms[$perm] = true;
                }
            }
        }

        return array_keys($perms);
    }
}


