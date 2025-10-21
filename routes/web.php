<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CepController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EntradaMercadoriaController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimentacaoEstoqueController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProdutoVariacaoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TamanhoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Rotas de Empresas (protegidas por autenticação)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('empresas', EmpresaController::class);
    Route::resource('users', UserController::class);
    Route::resource('perfis', RoleController::class)->parameters(['perfis' => 'perfi']);
    Route::resource('permissoes', PermissionController::class)->parameters(['permissoes' => 'permiso']);
    
    // Rotas aninhadas de Lojas
    Route::prefix('empresas/{empresa}')->group(function () {
        Route::get('lojas', [LojaController::class, 'index'])->name('empresas.lojas.index');
        Route::get('lojas/create', [LojaController::class, 'create'])->name('empresas.lojas.create');
        Route::post('lojas', [LojaController::class, 'store'])->name('empresas.lojas.store');
        Route::get('lojas/{loja}/edit', [LojaController::class, 'edit'])->name('empresas.lojas.edit');
        Route::put('lojas/{loja}', [LojaController::class, 'update'])->name('empresas.lojas.update');
        Route::delete('lojas/{loja}', [LojaController::class, 'destroy'])->name('empresas.lojas.destroy');
    });
    
    // Rotas do Sistema de Produtos e Estoque
    Route::resource('categorias', CategoriaController::class);
    Route::resource('cores', CorController::class);
    Route::resource('marcas', MarcaController::class);
    Route::resource('tamanhos', TamanhoController::class);
    Route::resource('produtos', ProdutoController::class);
    Route::resource('produto-variacoes', ProdutoVariacaoController::class)->parameters(['produto-variacoes' => 'produtoVariacao']);
    Route::resource('movimentacoes-estoque', MovimentacaoEstoqueController::class)->parameters(['movimentacoes-estoque' => 'movimentacaoEstoque']);
    
    // Rotas do Sistema de Gestão de Mercadorias
    Route::resource('fornecedores', FornecedorController::class);
    Route::resource('entradas-mercadoria', EntradaMercadoriaController::class)->parameters(['entradas-mercadoria' => 'entradaMercadoria']);
    
    // Rotas do Sistema de Vendas e Financeiro
    Route::resource('clientes', ClienteController::class);
    Route::resource('vendas', VendaController::class);
    Route::resource('pagamentos', PagamentoController::class);
    
    // Rotas adicionais para Vendas
    Route::post('vendas/{venda}/concluir', [VendaController::class, 'concluir'])->name('vendas.concluir');
    Route::post('vendas/{venda}/cancelar', [VendaController::class, 'cancelar'])->name('vendas.cancelar');
    
    // Rotas adicionais para Pagamentos
    Route::post('pagamentos/{pagamento}/marcar-como-pago', [PagamentoController::class, 'marcarComoPago'])->name('pagamentos.marcar-como-pago');
    Route::post('pagamentos/{pagamento}/cancelar', [PagamentoController::class, 'cancelar'])->name('pagamentos.cancelar');
});

// API Routes para Estados, Municípios e CEP (protegidas por autenticação)
Route::middleware(['auth', 'verified'])->prefix('api')->group(function () {
    Route::get('estados', [EstadoController::class, 'index'])->name('api.estados.index');
    Route::get('estados/{id}', [EstadoController::class, 'show'])->name('api.estados.show');
    
    Route::get('municipios', [MunicipioController::class, 'index'])->name('api.municipios.index');
    Route::get('municipios/{id}', [MunicipioController::class, 'show'])->name('api.municipios.show');
    
    Route::get('cep/{cep}', [CepController::class, 'show'])->name('api.cep.show');
    
    // API para buscar lojas por empresa
    Route::get('lojas-by-empresa', [UserController::class, 'getLojasByEmpresa'])->name('api.lojas-by-empresa');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
