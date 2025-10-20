<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Helpers\PermissionHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    /**
     * Display a listing of roles.
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('perfis.index', 'Você não tem permissão para visualizar perfis.');

        $query = Role::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('name', 'ilike', "%{$search}%");
        }

        $roles = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('perfis/Index', [
            'perfis' => $roles,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): Response
    {
        $this->checkPermission('perfis.create', 'Você não tem permissão para criar perfis.');

        $permissionsGrouped = PermissionHelper::getAllPermissionsGrouped();
        
        return Inertia::render('perfis/Create', [
            'permissionsGrouped' => $permissionsGrouped,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $this->checkPermission('perfis.store', 'Você não tem permissão para criar perfis.');

        $data = $request->validated();
        
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
        
        // Atribuir permissões se fornecidas
        if (isset($data['permissions']) && !empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return to_route('perfis.index')
            ->with('success', 'Perfil criado com sucesso!');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $perfi): Response
    {
        $permissionsGrouped = PermissionHelper::getAllPermissionsGrouped();
        $rolePermissions = $perfi->permissions->pluck('id')->toArray();
        
        return Inertia::render('perfis/Edit', [
            'perfil' => $perfi,
            'permissionsGrouped' => $permissionsGrouped,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(RoleUpdateRequest $request, Role $perfi): RedirectResponse
    {
        $data = $request->validated();
        
        $perfi->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $perfi->guard_name,
        ]);
        
        // Sincronizar permissões
        if (isset($data['permissions'])) {
            $perfi->syncPermissions($data['permissions']);
        }

        return to_route('perfis.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $perfi): RedirectResponse
    {
        $perfi->delete();

        return to_route('perfis.index')
            ->with('success', 'Perfil excluído com sucesso!');
    }
}



