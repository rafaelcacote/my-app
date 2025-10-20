<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Loja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('users.index', 'Você não tem permissão para visualizar usuários.');

        $query = User::with(['empresa', 'lojas']);

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Filtro por empresa
        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        // Busca por nome ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Buscar empresas para filtro
        $empresas = Empresa::ativas()->orderBy('razao_social')->get(['id', 'razao_social']);

        return Inertia::render('users/Index', [
            'users' => $users,
            'empresas' => $empresas,
            'filters' => $request->only(['search', 'status', 'empresa_id']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        $this->checkPermission('users.create', 'Você não tem permissão para criar usuários.');

        $empresas = Empresa::ativas()->orderBy('razao_social')->get(['id', 'razao_social']);
        $roles = Role::orderBy('name')->get(['id', 'name']);
        
        return Inertia::render('users/Create', [
            'empresas' => $empresas,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('users.store', 'Você não tem permissão para criar usuários.');

        $data = $request->validated();
        
        DB::beginTransaction();
        try {
            // Hash da senha
            $data['password'] = Hash::make($data['password']);
            
            // Limpar CPF (remover máscara)
            if (isset($data['cpf'])) {
                $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);
            }
            
            // Se CPF estiver vazio, remover do array
            if (empty($data['cpf'])) {
                unset($data['cpf']);
            }
            
            // Se tipo estiver vazio, remover do array
            if (empty($data['tipo'])) {
                unset($data['tipo']);
            }
            
            // Remove lojas do array principal para processar separadamente
            $lojas = $data['lojas'] ?? [];
            unset($data['lojas']);

            // Remove roles do array principal para processar separadamente
            $roles = $data['roles'] ?? [];
            unset($data['roles']);

            // Criar usuário
            $user = User::create($data);
          
            // Associar lojas se existirem
            if (!empty($lojas)) {
                $user->lojas()->sync($lojas);
            }
            
            // Associar perfis se existirem
            if (!empty($roles)) {
                $user->syncRoles($roles);
            }
            
            DB::commit();

            return to_route('users.index')
                ->with('success', 'Usuário cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao criar usuário:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao cadastrar usuário. Tente novamente.']);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): Response
    {
        $this->checkPermission('users.show', 'Você não tem permissão para visualizar usuários.');

        $user->load(['empresa', 'lojas']);
        
        return Inertia::render('users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        $this->checkPermission('users.edit', 'Você não tem permissão para editar usuários.');

        $user->load(['empresa', 'lojas', 'roles']);
        
        $empresas = Empresa::ativas()->orderBy('razao_social')->get(['id', 'razao_social']);
        $roles = Role::orderBy('name')->get(['id', 'name']);
        
        return Inertia::render('users/Edit', [
            'user' => $user,
            'empresas' => $empresas,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->checkPermission('users.update', 'Você não tem permissão para editar usuários.');

        $data = $request->validated();
        
        DB::beginTransaction();
        try {
            // Hash da senha se fornecida
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            // Limpar CPF (remover máscara)
            if (isset($data['cpf'])) {
                $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);
            }
            
            // Se CPF estiver vazio, remover do array
            if (empty($data['cpf'])) {
                unset($data['cpf']);
            }
            
            // Se tipo estiver vazio, remover do array
            if (empty($data['tipo'])) {
                unset($data['tipo']);
            }
            
            // Remove lojas do array principal para processar separadamente
            $lojas = $data['lojas'] ?? [];
            unset($data['lojas']);
            
            // Remove roles do array principal para processar separadamente
            $roles = $data['roles'] ?? [];
            unset($data['roles']);
            
            // Atualizar usuário
            $user->update($data);
            
            // Atualizar lojas associadas
            $user->lojas()->sync($lojas);
            
            // Atualizar perfis associados
            $user->syncRoles($roles);
            
            DB::commit();

            return to_route('users.index')
                ->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao atualizar usuário:', ['error' => $e->getMessage()]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar usuário. Tente novamente.']);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->checkPermission('users.delete', 'Você não tem permissão para excluir usuários.');

        // Não permitir exclusão do próprio usuário
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Você não pode excluir seu próprio usuário.']);
        }
        
        $user->delete();

        return to_route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }

    /**
     * Get lojas by empresa for autocomplete.
     */
    public function getLojasByEmpresa(Request $request)
    {
        $empresaId = $request->get('empresa_id');
        
        if (!$empresaId) {
            return response()->json([]);
        }
        
        $lojas = Loja::where('empresa_id', $empresaId)
            ->ativas()
            ->orderBy('nome')
            ->get(['id', 'nome', 'cnpj']);
            
        return response()->json($lojas);
    }
}