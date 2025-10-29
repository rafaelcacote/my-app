# 🚀 Guia Rápido - Dashboard do Vendedor

## Setup Inicial (Primeira vez)

### 1. Executar o Seeder de Permissões

```bash
php artisan db:seed --class=PermissionsSeeder
```

Isso criará:
- ✅ 4 perfis: Administrador, Gerente, Vendedor, Estoquista
- ✅ Todas as permissões necessárias
- ✅ Associação de permissões aos perfis

---

## Criar Vendedor

### Método 1: Via Comando Artisan (Recomendado)

```bash
# Forma básica
php artisan vendedor:criar "João Silva" "joao@loja.com"

# Com senha personalizada
php artisan vendedor:criar "Maria Santos" "maria@loja.com" --senha=maria123

# Com empresa específica
php artisan vendedor:criar "Pedro Costa" "pedro@loja.com" --empresa=1

# Com CPF
php artisan vendedor:criar "Ana Lima" "ana@loja.com" --cpf="123.456.789-00"

# Completo
php artisan vendedor:criar "Carlos Souza" "carlos@loja.com" \
    --senha=carlos123 \
    --empresa=1 \
    --cpf="987.654.321-00"
```

**Padrões:**
- Senha padrão: `senha123`
- Empresa: Primeira empresa cadastrada
- Status: Ativo
- Tipo: vendedor

### Método 2: Via Tinker

```bash
php artisan tinker
```

```php
$user = User::create([
    'name' => 'João Silva',
    'email' => 'joao@loja.com',
    'password' => bcrypt('senha123'),
    'empresa_id' => 1,
    'ativo' => true,
]);

$user->assignRole('Vendedor');
exit
```

### Método 3: Via Interface (se tiver CRUD de usuários)

1. Acessar página de usuários
2. Clicar em "Novo Usuário"
3. Preencher dados
4. Selecionar perfil "Vendedor"
5. Salvar

---

## Testar o Dashboard

### 1. Fazer Login como Vendedor

1. Acessar: `http://localhost/login` (ou seu domínio)
2. Email: `joao@loja.com`
3. Senha: `senha123` (ou a que você definiu)
4. Clicar em "Entrar"

### 2. Verificar Redirecionamento

- ✅ Vendedor deve ser redirecionado para `/dashboard/vendedor`
- ✅ Dashboard mostrará suas métricas personalizadas
- ✅ Verá apenas suas próprias vendas

### 3. Explorar as Funcionalidades

**Métricas exibidas:**
- Total vendido
- Quantidade de vendas
- Ticket médio
- Comissão estimada (5%)

**Dados detalhados:**
- Barra de progresso da meta mensal
- Últimas 10 vendas
- Top 5 produtos mais vendidos
- Vendas por dia

---

## Criar Vendas de Teste

### Para que o dashboard tenha dados, crie algumas vendas:

```bash
php artisan tinker
```

```php
use App\Models\Venda;
use App\Models\User;
use Carbon\Carbon;

$vendedor = User::where('email', 'joao@loja.com')->first();

// Criar 5 vendas de exemplo
for ($i = 1; $i <= 5; $i++) {
    Venda::create([
        'empresa_id' => 1,
        'loja_id' => 1,
        'usuario_id' => $vendedor->id,  // Importante!
        'cliente_id' => 1,
        'numero_venda' => 'VENDA-' . str_pad($i, 5, '0', STR_PAD_LEFT),
        'status' => 'concluida',
        'subtotal' => rand(100, 500),
        'desconto' => 0,
        'total' => rand(100, 500),
        'forma_pagamento' => ['pix', 'dinheiro', 'cartao_credito'][rand(0, 2)],
        'data_venda' => Carbon::now()->subDays(rand(0, 30)),
    ]);
}

exit
```

---

## Verificar Permissões

### Via Tinker

```bash
php artisan tinker
```

```php
$user = User::where('email', 'joao@loja.com')->first();

// Ver perfis
$user->roles->pluck('name');
// Deve mostrar: ["Vendedor"]

// Ver permissões
$user->getAllPermissions()->pluck('name');
// Deve mostrar: ["dashboard.vendedor", "vendas.index", ...]

// Testar permissão específica
$user->can('dashboard.vendedor');
// Deve retornar: true

exit
```

---

## Acessar como Outros Perfis

### Admin / Gerente

1. Login com admin/gerente
2. Acessa `/dashboard` (dashboard principal)
3. Vê seção "Vendas por Vendedor" com ranking de todos

### Vendedor

1. Login com vendedor
2. Redirecionado para `/dashboard/vendedor`
3. Vê apenas suas métricas pessoais

---

## Personalizar Valores

### Meta Mensal

**Local:** `app/Http/Controllers/DashboardVendedorController.php`
**Linha:** ~98

```php
// Trocar de 10000.00 para o valor desejado
$metaMensal = 15000.00;
```

### Percentual de Comissão

**Local:** `app/Http/Controllers/DashboardVendedorController.php`
**Linha:** ~91

```php
// Trocar de 5 para o percentual desejado
$percentualComissao = 8;
```

---

## Comandos Úteis

```bash
# Limpar cache de permissões
php artisan permission:cache-reset

# Reexecutar seeder (caso precise atualizar permissões)
php artisan db:seed --class=PermissionsSeeder

# Criar vendedor rapidamente
php artisan vendedor:criar "Nome" "email@example.com"

# Ver lista de comandos disponíveis
php artisan list

# Ver ajuda do comando vendedor
php artisan vendedor:criar --help
```

---

## URLs Importantes

```
Login:                  /login
Dashboard Principal:    /dashboard
Dashboard Vendedor:     /dashboard/vendedor
Vendas:                 /vendas
Clientes:               /clientes
Produtos:               /produtos
```

---

## Solução de Problemas

### Erro: "Permission denied"

```bash
php artisan permission:cache-reset
php artisan db:seed --class=PermissionsSeeder
```

### Vendedor não vê dados

**Verificar:**
1. Vendas têm `usuario_id` = ID do vendedor?
2. Vendas têm status `concluida`?
3. Vendas estão no período atual (mês atual)?
4. Vendas têm `empresa_id` correto?

**Criar venda de teste:**
```bash
php artisan tinker
```
```php
$vendedor = User::find(ID_DO_VENDEDOR);
Venda::create([
    'empresa_id' => $vendedor->empresa_id,
    'loja_id' => 1,
    'usuario_id' => $vendedor->id,
    'cliente_id' => 1,
    'numero_venda' => 'TESTE-001',
    'status' => 'concluida',
    'subtotal' => 100,
    'desconto' => 0,
    'total' => 100,
    'forma_pagamento' => 'pix',
    'data_venda' => now(),
]);
exit
```

### Dashboard vazio mesmo com vendas

**Verificar período:**
- Dashboard mostra vendas do mês atual por padrão
- Se as vendas são de meses anteriores, não aparecerão

**Solução temporária:**
Alterar data das vendas para o mês atual via tinker:
```php
Venda::where('usuario_id', ID_DO_VENDEDOR)->update(['data_venda' => now()]);
```

### Erro ao criar vendedor

**Verificar:**
1. Email já existe?
2. Empresa existe?
3. Perfil "Vendedor" foi criado? (executar seeder)

---

## Próximos Passos

1. ✅ Criar vendedores
2. ✅ Criar vendas de teste
3. ✅ Testar login e acesso
4. ✅ Verificar métricas
5. 🚀 Usar em produção!

---

## Suporte

- 📖 Documentação completa: `docs/DASHBOARD-VENDEDOR.md`
- 📋 Resumo técnico: `docs/RESUMO-IMPLEMENTACAO-DASHBOARD-VENDEDOR.md`
- 🔍 Logs: `storage/logs/laravel.log`

---

**Tudo pronto para começar! 🎉**

