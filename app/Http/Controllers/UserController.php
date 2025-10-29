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
use App\Models\Role;

class UserController extends Controller
{

    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('users.index', 'Você não tem permissão para visualizar usuários.');

        $currentUser = auth()->user();
        $query = User::with(['empresa', 'lojas']);

        // Se não for super admin, filtrar apenas usuários da mesma empresa
        if (!$currentUser->isSuperAdmin()) {
            $query->where('empresa_id', $currentUser->empresa_id);
        }

        // Filtro por status (ativo/inativo)
        if ($request->has('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        // Filtro por empresa (apenas para super admin)
        if ($request->filled('empresa_id') && $currentUser->isSuperAdmin()) {
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

        // Buscar empresas para filtro (todas para super admin, apenas a do usuário para outros)
        if ($currentUser->isSuperAdmin()) {
            $empresas = Empresa::ativas()->orderBy('razao_social')->get(['id', 'razao_social']);
        } else {
            $empresas = Empresa::where('id', $currentUser->empresa_id)
                ->orderBy('razao_social')
                ->get(['id', 'razao_social']);
        }

        return Inertia::render('users/Index', [
            'users' => $users,
            'empresas' => $empresas,
            'filters' => $request->only(['search', 'status', 'empresa_id']),
            'isSuperAdmin' => $currentUser->isSuperAdmin(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        $this->checkPermission('users.create', 'Você não tem permissão para criar usuários.');

        $currentUser = auth()->user();

        // Buscar empresas (todas para super admin, apenas a do usuário para outros)
        if ($currentUser->isSuperAdmin()) {
            $empresas = Empresa::ativas()->orderBy('razao_social')->get(['id', 'razao_social']);
        } else {
            $empresas = Empresa::where('id', $currentUser->empresa_id)
                ->orderBy('razao_social')
                ->get(['id', 'razao_social']);
        }

        $roles = Role::orderBy('name')->get(['id', 'name']);
        
        return Inertia::render('users/Create', [
            'empresas' => $empresas,
            'roles' => $roles,
            'isSuperAdmin' => $currentUser->isSuperAdmin(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('users.store', 'Você não tem permissão para criar usuários.');

        $currentUser = auth()->user();
        $data = $request->validated();

        // Se não for super admin, forçar empresa_id do usuário logado
        if (!$currentUser->isSuperAdmin()) {
            $data['empresa_id'] = $currentUser->empresa_id;
        }

        // Validar que empresa_id foi fornecido
        if (empty($data['empresa_id'])) {
            return back()
                ->withInput()
                ->withErrors(['empresa_id' => 'A empresa é obrigatória.']);
        }

        // Se não for super admin, validar que a empresa é a mesma do usuário
        if (!$currentUser->isSuperAdmin() && $data['empresa_id'] != $currentUser->empresa_id) {
            return back()
                ->withInput()
                ->withErrors(['empresa_id' => 'Você só pode criar usuários para sua própria empresa.']);
        }
        
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

        $currentUser = auth()->user();

        // Se não for super admin, verificar se o usuário pertence à mesma empresa
        if (!$currentUser->isSuperAdmin() && $user->empresa_id != $currentUser->empresa_id) {
            abort(403, 'Você não tem permissão para visualizar este usuário.');
        }

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

        $currentUser = auth()->user();

        // Se não for super admin, verificar se o usuário pertence à mesma empresa
        if (!$currentUser->isSuperAdmin() && $user->empresa_id != $currentUser->empresa_id) {
            abort(403, 'Você não tem permissão para editar este usuário.');
        }

        $user->load(['empresa', 'lojas', 'roles']);
        
        // Buscar empresas (todas para super admin, apenas a do usuário para outros)
        if ($currentUser->isSuperAdmin()) {
            $empresas = Empresa::ativas()->orderBy('razao_social')->get(['id', 'razao_social']);
        } else {
            $empresas = Empresa::where('id', $currentUser->empresa_id)
                ->orderBy('razao_social')
                ->get(['id', 'razao_social']);
        }

        $roles = Role::orderBy('name')->get(['id', 'name']);
        
        return Inertia::render('users/Edit', [
            'user' => $user,
            'empresas' => $empresas,
            'roles' => $roles,
            'isSuperAdmin' => $currentUser->isSuperAdmin(),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->checkPermission('users.update', 'Você não tem permissão para editar usuários.');

        $currentUser = auth()->user();

        // Se não for super admin, verificar se o usuário pertence à mesma empresa
        if (!$currentUser->isSuperAdmin() && $user->empresa_id != $currentUser->empresa_id) {
            return back()->withErrors(['error' => 'Você não tem permissão para editar este usuário.']);
        }

        $data = $request->validated();

        // Se não for super admin, forçar empresa_id do usuário logado
        if (!$currentUser->isSuperAdmin()) {
            $data['empresa_id'] = $currentUser->empresa_id;
        }

        // Se não for super admin, validar que a empresa não foi alterada
        if (!$currentUser->isSuperAdmin() && isset($data['empresa_id']) && $data['empresa_id'] != $currentUser->empresa_id) {
            return back()
                ->withInput()
                ->withErrors(['empresa_id' => 'Você só pode editar usuários da sua própria empresa.']);
        }
        
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

        $currentUser = auth()->user();

        // Não permitir exclusão do próprio usuário
        if ($user->id === $currentUser->id) {
            return back()->withErrors(['error' => 'Você não pode excluir seu próprio usuário.']);
        }

        // Se não for super admin, verificar se o usuário pertence à mesma empresa
        if (!$currentUser->isSuperAdmin() && $user->empresa_id != $currentUser->empresa_id) {
            return back()->withErrors(['error' => 'Você não tem permissão para excluir este usuário.']);
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