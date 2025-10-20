<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('perfi');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'guard_name' => ['nullable', 'string', 'max:50'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.unique' => 'Já existe um perfil com esse nome.',
            'permissions.array' => 'As permissões devem ser um array.',
            'permissions.*.integer' => 'Cada permissão deve ser um ID válido.',
            'permissions.*.exists' => 'Uma ou mais permissões selecionadas não existem.',
        ];
    }
}
